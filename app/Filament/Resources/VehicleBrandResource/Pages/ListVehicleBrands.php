<?php

namespace App\Filament\Resources\VehicleBrandResource\Pages;

use App\Filament\Resources\VehicleBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class ListVehicleBrands extends ListRecords
{
    protected static string $resource = VehicleBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            // --- ACTION 1 : CRÉER LES MARQUES POPULAIRES (LISTE PROPRE) ---
            Actions\Action::make('import_popular_makes')
                ->label('Importer Marques (Liste Canada/USA)')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Réinitialiser avec les marques populaires ?')
                ->modalDescription('Cela va créer les marques courantes (Honda, Ford, Tesla...) et ignorer les camions lourds et remorques.')
                ->action(function () {
                    $count = 0;

                    $brands = [
                        // Asiatiques
                        'Acura',
                        'Honda',
                        'Toyota',
                        'Lexus',
                        'Nissan',
                        'Infiniti',
                        'Mazda',
                        'Subaru',
                        'Mitsubishi',
                        'Hyundai',
                        'Kia',
                        'Genesis',
                        'Suzuki',
                        'Scion',

                        // Américaines
                        'Ford',
                        'Lincoln',
                        'Chevrolet',
                        'GMC',
                        'Buick',
                        'Cadillac',
                        'Dodge',
                        'Chrysler',
                        'Jeep',
                        'Ram',
                        'Tesla',
                        'Rivian',
                        'Lucid',
                        'Hummer',
                        'Pontiac',
                        'Saturn',
                        'Oldsmobile',
                        'Mercury',

                        // Européennes
                        'Audi',
                        'BMW',
                        'Mercedes-Benz',
                        'Volkswagen',
                        'Volvo',
                        'Porsche',
                        'Land Rover',
                        'Jaguar',
                        'Mini',
                        'Fiat',
                        'Alfa Romeo',
                        'Maserati',
                        'Bentley',
                        'Rolls-Royce',
                        'Ferrari',
                        'Lamborghini',
                        'Aston Martin',
                        'McLaren',
                        'Polestar',
                        'Smart',
                        'Saab'
                    ];

                    foreach ($brands as $name) {
                        $brand = \App\Models\VehicleBrand::firstOrCreate(['name' => $name]);
                        if ($brand->wasRecentlyCreated) $count++;
                    }

                    Notification::make()
                        ->title('Liste propre importée')
                        ->body("$count nouvelles marques ajoutées.")
                        ->success()
                        ->send();
                }),

            // --- ACTION 2 : SYNC MODÈLES (PROPRE + FILTRES) ---
            Actions\Action::make('sync_models')
                ->label('Télécharger les Modèles (propre)')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->form([
                    Select::make('year')
                        ->label('Année à importer')
                        ->options(array_combine(range(date('Y') + 1, 2000), range(date('Y') + 1, 2000)))
                        ->default(date('Y'))
                        ->required(),

                    Toggle::make('truncate_first')
                        ->label('Vider la table vehicle_models avant import')
                        ->default(false),

                    Toggle::make('only_active_brands')
                        ->label('Importer seulement les marques actives (is_active=1)')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    set_time_limit(900); // 15 minutes

                    $year = (int) $data['year'];
                    $truncate = (bool) ($data['truncate_first'] ?? false);
                    $onlyActive = (bool) ($data['only_active_brands'] ?? true);

                    if ($truncate) {
                        \DB::statement('TRUNCATE TABLE vehicle_models');
                    }

                    $brands = $onlyActive
                        ? \App\Models\VehicleBrand::where('is_active', 1)->get()
                        : \App\Models\VehicleBrand::all();

                    $countCreated = 0;
                    $countSkipped = 0;
                    $countRequests = 0;

                    foreach ($brands as $brand) {
                        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/getmodelsformakeyear/make/"
                            . rawurlencode($brand->name)
                            . "/modelyear/{$year}?format=json";

                        try {
                            $resp = Http::timeout(25)
                                ->retry(2, 400) // 2 retries, 400ms
                                ->get($url);

                            $countRequests++;

                            if (!$resp->successful()) {
                                usleep(120000);
                                continue;
                            }

                            $results = $resp->json('Results') ?? [];

                            foreach ($results as $item) {
                                $raw = trim((string)($item['Model_Name'] ?? ''));
                                $modelName = $this->normalizeModelName($raw);

                                if ($modelName === '' || $this->isGarbageModel($modelName)) {
                                    $countSkipped++;
                                    continue;
                                }

                                $model = \App\Models\VehicleModel::firstOrCreate(
                                    [
                                        'vehicle_brand_id' => $brand->id,
                                        'name' => $modelName,
                                    ],
                                    [
                                        'is_active' => 1,
                                    ]
                                );

                                if ($model->wasRecentlyCreated) $countCreated++;
                            }
                        } catch (\Throwable $e) {
                            // On ignore la marque si erreur API
                        }

                        // throttle anti rate-limit
                        usleep(120000);
                    }

                    Notification::make()
                        ->title("Modèles $year importés (propre)")
                        ->body("$countCreated modèles ajoutés • $countSkipped ignorés • $countRequests requêtes API")
                        ->success()
                        ->send();
                }),

            // --- ACTION 3 : IMPORTATION MASSIVE (JOB) ---
            Actions\Action::make('import_full_history')
                ->label('Tout importer')
                ->icon('heroicon-o-server-stack')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Lancer l\'importation en arrière-plan ?')
                ->modalDescription('Le processus va démarrer sur le serveur. Vous pouvez continuer à travailler.')
                ->visible(fn() => auth()->id() === 1)
                ->action(function () {
                    \App\Jobs\ImportNhtsaModels::dispatch(auth()->user());

                    Notification::make()
                        ->title('Tâche lancée !')
                        ->body('L\'importation tourne en fond. Vous recevrez une alerte à la fin.')
                        ->info()
                        ->send();
                }),
        ];
    }

    /**
     * Nettoyage / normalisation du nom de modèle (anti-trims)
     */
    private function normalizeModelName(string $name): string
    {
        $name = trim($name);
        if ($name === '') return '';

        $name = preg_replace('/\s+/', ' ', $name);

        // coupe les suffixes "trims" et descripteurs
        $name = preg_replace('/\s+(CREW|DOUBLE|EXTENDED|REGULAR|SUPER|MEGA)\s+CAB\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(4X4|4X2|AWD|FWD|RWD)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(HD|HEAVY DUTY|SUPER DUTY)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(LWB|SWB|WB|WHEELBASE)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(2DR|4DR|2D|4D)\b.*/i', '', $name);
        $name = preg_replace('/\s+\b(SEDAN|COUPE|HATCHBACK|WAGON|CONVERTIBLE|VAN|MINIVAN|SUV|TRUCK)\b.*/i', '', $name);

        // F-series
        $name = preg_replace('/\bF\s*150\b/i', 'F-150', $name);
        $name = preg_replace('/\bF\s*250\b/i', 'F-250', $name);
        $name = preg_replace('/\bF\s*350\b/i', 'F-350', $name);
        $name = preg_replace('/\bF\s*450\b/i', 'F-450', $name);

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

    /**
     * Rejette les entrées trop génériques / bizarres
     */
    private function isGarbageModel(string $name): bool
    {
        $u = strtoupper($name);
        if ($u === '' || strlen($u) < 2) return true;

        if (in_array($u, ['UNKNOWN', 'OTHER', 'ALL', 'N/A', 'NA', 'NONE'], true)) return true;

        // codes trop longs avec chiffres
        if (strlen($u) > 18 && preg_match('/\d/', $u) && preg_match('/^[A-Z0-9\- ]+$/', $u)) {
            return true;
        }

        return false;
    }
}
