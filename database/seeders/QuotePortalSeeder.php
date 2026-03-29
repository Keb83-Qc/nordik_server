<?php

namespace Database\Seeders;

use App\Models\QuotePortal;
use App\Models\QuoteType;
use Illuminate\Database\Seeder;

class QuotePortalSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Portail interne (Nordik) ──────────────────────────────────────────
        $internal = QuotePortal::firstOrCreate(
            ['slug' => 'internal'],
            [
                'name'            => 'Nordik',
                'type'            => 'internal',
                'logo_path'       => null,
                'primary_color'   => '#1a2e4a',
                'secondary_color' => '#e8b84b',
                'consent_title'   => [
                    'fr' => 'Bienvenue chez Nordik',
                    'en' => 'Welcome to Nordik',
                    'es' => 'Bienvenido a Nordik',
                    'ht' => 'Byenveni nan Nordik',
                ],
                'consent_text' => [
                    'fr' => '<p>En soumettant ce formulaire, vous consentez à ce que vos informations personnelles soient utilisées par Nordik dans le but de vous fournir une soumission d\'assurance. Vos données sont traitées de manière confidentielle.</p>',
                    'en' => '<p>By submitting this form, you consent to your personal information being used by Nordik for the purpose of providing you with an insurance quote. Your data is handled confidentially.</p>',
                    'es' => '<p>Al enviar este formulario, usted consiente que su información personal sea utilizada por Nordik con el propósito de proporcionarle una cotización de seguro. Sus datos se tratan de forma confidencial.</p>',
                    'ht' => '<p>Lè ou soumèt fòm sa a, ou konsanti pou enfòmasyon pèsonèl ou yo itilize pa Nordik nan bi pou ba ou yon soumisyon asirans. Done ou yo trete avèk konfidansyalite.</p>',
                ],
                'is_active' => true,
            ]
        );

        // Attache tous les types de quotes actifs au portail interne
        $quoteTypes = QuoteType::orderBy('sort_order')->get();

        foreach ($quoteTypes as $index => $quoteType) {
            $internal->quoteTypes()->syncWithoutDetaching([
                $quoteType->id => [
                    'sort_order' => $index + 1,
                    'is_active'  => $quoteType->is_active,
                ],
            ]);
        }

        $this->command->info('✅ Portail interne créé avec ' . $quoteTypes->count() . ' types de quotes.');
    }
}
