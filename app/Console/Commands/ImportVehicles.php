<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Http;

class ImportVehicles extends Command
{
    protected $signature = 'vehicles:sync {--year=} {--years=2} {--sleep=120000}';
    protected $description = 'Sync Year/Make -> Models from NHTSA vPIC (clean + dedupe)';

    public function handle()
    {
        // Marques ciblées (tu peux garder ta liste)
        $selectedMakes = [
            'Ford',
            'Toyota',
            'Chevrolet',
            'Hyundai',
            'Honda',
            'GMC',
            'Nissan',
            'Ram',
            'Kia',
            'Volkswagen',
            'Mazda',
            'Jeep',
            'Subaru',
            'BMW',
            'Mercedes-Benz',
            'Audi',
            'Lexus',
            'Volvo',
            'Cadillac',
            'Buick',
            'Chrysler',
            'Dodge',
            'Mini',
            'Porsche',
            'Jaguar',
            'Land Rover',
            'Tesla',
            'Infiniti',
            'Acura',
            'Genesis',
            'Mitsubishi',
            'Lincoln',
            'Alfa Romeo',
            'Fiat'
        ];

        // Années : par défaut = [année courante, année courante +1]
        $startYear = (int)($this->option('year') ?: now()->year);
        $yearsSpan = max(1, (int)$this->option('years')); // nombre d’années à couvrir
        $years = [];
        for ($i = 0; $i < $yearsSpan; $i++) {
            $years[] = $startYear + $i;
        }

        $sleepUs = max(0, (int)$this->option('sleep')); // microseconds entre appels (anti-rate-limit)

        $this->info("Sync modèles vPIC pour " . count($selectedMakes) . " marques, années: " . implode(', ', $years));

        foreach ($selectedMakes as $makeName) {
            $this->warn("\nTraitement : {$makeName}");

            $brand = VehicleBrand::firstOrCreate(['name' => $makeName]);

            $totalAdded = 0;
            $seen = []; // dédup par marque (normalisé)

            // Charge les modèles déjà existants pour dédup
            $existing = VehicleModel::where('vehicle_brand_id', $brand->id)->pluck('name')->all();
            foreach ($existing as $n) {
                $seen[$this->normalizeModelName($n)] = true;
            }

            foreach ($years as $year) {
                $url = "https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeYear/make/"
                    . rawurlencode($makeName)
                    . "/modelyear/{$year}?format=json";

                try {
                    $response = Http::timeout(30)->get($url);

                    if (!$response->successful()) {
                        $this->error("  - {$year}: HTTP " . $response->status());
                        usleep($sleepUs);
                        continue;
                    }

                    $results = $response->json('Results') ?? [];
                    $addedYear = 0;

                    foreach ($results as $row) {
                        $raw = trim((string)($row['Model_Name'] ?? ''));

                        $clean = $this->normalizeModelName($raw);
                        if ($clean === '') continue;

                        // filtres anti-bruit
                        if ($this->isGarbageModel($clean)) continue;

                        // dédup
                        if (isset($seen[$clean])) continue;

                        VehicleModel::create([
                            'vehicle_brand_id' => $brand->id,
                            'name' => $clean,
                            'is_active' => 1,
                        ]);

                        $seen[$clean] = true;
                        $addedYear++;
                        $totalAdded++;
                    }

                    $this->info("  - {$year}: {$addedYear} modèles ajoutés");
                } catch (\Throwable $e) {
                    $this->error("  - {$year}: erreur: " . $e->getMessage());
                }

                usleep($sleepUs);
            }

            $this->info("Total ajoutés pour {$makeName}: {$totalAdded}");
        }

        $this->info("\nTerminé.");
    }

    private function normalizeModelName(string $name): string
    {
        $name = trim($name);
        if ($name === '') return '';

        // espaces multiples
        $name = preg_replace('/\s+/', ' ', $name);

        // retire tout ce qui ressemble à des suffixes de version (après un séparateur)
        // ex: "1500 CREW CAB 4X4" -> "1500"
        $name = preg_replace('/\s+(CREW|DOUBLE|EXTENDED|REGULAR|SUPER|MEGA)\s+CAB\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(4X4|4X2|AWD|FWD|RWD)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(HD|HEAVY DUTY|SUPER DUTY)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(LWB|SWB|WB|WHEELBASE)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(2DR|4DR|2D|4D)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(SEDAN|COUPE|HATCHBACK|WAGON|CONVERTIBLE|VAN|MINIVAN|SUV|TRUCK)\b.*/i', '', $name);

        // Normalisation F-series
        $name = preg_replace('/\bF\s*150\b/i', 'F-150', $name);
        $name = preg_replace('/\bF\s*250\b/i', 'F-250', $name);
        $name = preg_replace('/\bF\s*350\b/i', 'F-350', $name);
        $name = preg_replace('/\bF\s*450\b/i', 'F-450', $name);

        // Trim final + espaces
        $name = trim(preg_replace('/\s+/', ' ', $name));

        // Title case léger
        $name = preg_replace_callback('/\b([a-z]{2,})\b/i', function ($m) {
            $w = $m[1];
            $upper = strtoupper($w);
            if (in_array($upper, ['EV', 'SUV', 'PHEV', 'HEV', 'GT', 'HD'], true)) return $upper;
            return ucfirst(strtolower($w));
        }, $name);

        return trim($name);
    }

    private function isGarbageModel(string $name): bool
    {
        $u = strtoupper($name);

        if ($u === '' || strlen($u) < 2) return true;

        // trop de chiffres / codes
        if (preg_match('/^[A-Z0-9\- ]+$/', $u) && preg_match('/\d/', $u) && strlen($u) > 14) {
            return true;
        }

        // tokens bruit (si la string est longue)
        if (strlen($u) >= 18 && preg_match('/\b(TRIM|SERIES|PACKAGE|PLATFORM|CHASSIS)\b/i', $u)) {
            return true;
        }

        // exact garbage
        return in_array($u, ['UNKNOWN', 'OTHER', 'ALL', 'N/A', 'NA', 'NONE'], true);
    }
}
