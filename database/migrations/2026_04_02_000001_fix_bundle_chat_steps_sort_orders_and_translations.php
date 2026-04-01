<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix #2 — Bundle chat_steps: sort_order duplicates + missing translations
 *
 * Duplicates found (same sort_order within chat_type='bundle'):
 *   90  → auto_renewal_date (57) vs auto_renewal (85)
 *   110 → auto_km_annuel   (59) vs auto_km      (86)
 *   130 → auto_existing_products (60) vs auto_license_number (61)
 *   190 → hab_living_there (66) vs hab_move_in_date (67)
 *   310 → hab_industry (78) vs hab_has_ia_products (79)
 *   320 → hab_consent_profile (80) vs hab_ia_products (90)
 *   330 → hab_consent_marketing (81) vs common_consent_profile (91)
 *   340 → hab_marketing_email (82) vs common_consent_marketing (92)
 *
 * Strategy: keep original steps in place, offset the duplicate (newer/redundant) step.
 *
 * Missing translations fixed:
 *   auto_renewal_date (bundle id=57): en/es/ht empty
 *   auto_km_annuel    (bundle id=59): en/es/ht empty
 *   hab_consent_profile    (id=80):   en/es/ht empty
 *   hab_consent_marketing  (id=81):   en/es/ht empty
 *   hab_marketing_email    (id=82):   en/es/ht empty
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Fix sort_order duplicates ─────────────────────────────────────

        $fixes = [
            // auto_renewal       → 92  (was 90, duplicate of auto_renewal_date)
            ['identifier' => 'auto_renewal',          'chat_type' => 'bundle', 'sort_order' => 92],
            // auto_km            → 113 (was 110, duplicate of auto_km_annuel)
            ['identifier' => 'auto_km',               'chat_type' => 'bundle', 'sort_order' => 113],
            // auto_license_number → 138 (was 130, duplicate of auto_existing_products)
            ['identifier' => 'auto_license_number',   'chat_type' => 'bundle', 'sort_order' => 138],
            // hab_move_in_date   → 196 (was 190, duplicate of hab_living_there)
            ['identifier' => 'hab_move_in_date',      'chat_type' => 'bundle', 'sort_order' => 196],
            // hab_has_ia_products → 315 (was 310, duplicate of hab_industry)
            ['identifier' => 'hab_has_ia_products',   'chat_type' => 'bundle', 'sort_order' => 315],
            // hab_ia_products    → 318 (was 320, clashed with hab_consent_profile)
            ['identifier' => 'hab_ia_products',       'chat_type' => 'bundle', 'sort_order' => 318],
            // common_consent_profile → 324 (was 330, clashed with hab_consent_marketing)
            ['identifier' => 'common_consent_profile',   'chat_type' => 'bundle', 'sort_order' => 324],
            // common_consent_marketing → 334 (was 340, clashed with hab_marketing_email)
            ['identifier' => 'common_consent_marketing', 'chat_type' => 'bundle', 'sort_order' => 334],
        ];

        foreach ($fixes as $fix) {
            DB::table('chat_steps')
                ->where('identifier', $fix['identifier'])
                ->where('chat_type', $fix['chat_type'])
                ->update(['sort_order' => $fix['sort_order']]);
        }

        // ── 2. Add missing translations ───────────────────────────────────────

        // auto_renewal_date (bundle, id=57) — copy wording from auto flow (id=9)
        DB::table('chat_steps')
            ->where('identifier', 'auto_renewal_date')
            ->where('chat_type', 'bundle')
            ->update([
                'question' => json_encode([
                    'fr' => "Quelle est la date de renouvellement de votre assurance auto ?",
                    'en' => "What is your expected auto insurance renewal date?",
                    'es' => "¿Cuál es su fecha prevista de renovación del seguro de auto?",
                    'ht' => "Ki dat renouvèlman asirans otomobil ou prevwa?",
                ]),
            ]);

        // auto_km_annuel (bundle, id=59) — copy wording from auto flow (id=11)
        DB::table('chat_steps')
            ->where('identifier', 'auto_km_annuel')
            ->where('chat_type', 'bundle')
            ->update([
                'question' => json_encode([
                    'fr' => "Environ combien de kilomètres faites-vous par année ?",
                    'en' => "About how many kilometers do you drive per year?",
                    'es' => "Aproximadamente, ¿cuántos kilómetros conduce al año?",
                    'ht' => "Apeprè konbyen kilomèt ou fè pa ane?",
                ]),
            ]);

        // hab_consent_profile (bundle, id=80)
        DB::table('chat_steps')
            ->where('identifier', 'hab_consent_profile')
            ->where('chat_type', 'bundle')
            ->update([
                'question' => json_encode([
                    'fr' => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                    'en' => "Consent: collect/use/share certain information to better know you.",
                    'es' => "Consentimiento: recopilar/usar/compartir cierta información para conocerle mejor.",
                    'ht' => "Konsantman: ranmase/itilize/pataje kèk enfòmasyon pou pi byen konnen w.",
                ]),
            ]);

        // hab_consent_marketing (bundle, id=81)
        DB::table('chat_steps')
            ->where('identifier', 'hab_consent_marketing')
            ->where('chat_type', 'bundle')
            ->update([
                'question' => json_encode([
                    'fr' => "Consentement : vous faire part de promotions/produits/services/événements.",
                    'en' => "Consent: share promotions/products/services/events with you.",
                    'es' => "Consentimiento: compartir promociones/productos/servicios/eventos con usted.",
                    'ht' => "Konsantman: pataje pwomosyon/pwodui/sèvis/evènman avèk ou.",
                ]),
            ]);

        // hab_marketing_email (bundle, id=82)
        DB::table('chat_steps')
            ->where('identifier', 'hab_marketing_email')
            ->where('chat_type', 'bundle')
            ->update([
                'question' => json_encode([
                    'fr' => "Souhaitez-vous recevoir ces communications par courriel ?",
                    'en' => "Would you like to receive these communications by email?",
                    'es' => "¿Desea recibir estas comunicaciones por correo electrónico?",
                    'ht' => "Èske ou ta renmen resevwa kominikasyon sa yo nan imèl?",
                ]),
            ]);

        // Invalide le cache pour forcer le rechargement
        \Illuminate\Support\Facades\Cache::forget('chat_steps_bundle');
    }

    public function down(): void
    {
        // Restaure les sort_orders originaux
        $originals = [
            ['identifier' => 'auto_renewal',             'chat_type' => 'bundle', 'sort_order' => 90],
            ['identifier' => 'auto_km',                  'chat_type' => 'bundle', 'sort_order' => 110],
            ['identifier' => 'auto_license_number',      'chat_type' => 'bundle', 'sort_order' => 130],
            ['identifier' => 'hab_move_in_date',         'chat_type' => 'bundle', 'sort_order' => 190],
            ['identifier' => 'hab_has_ia_products',      'chat_type' => 'bundle', 'sort_order' => 310],
            ['identifier' => 'hab_ia_products',          'chat_type' => 'bundle', 'sort_order' => 320],
            ['identifier' => 'common_consent_profile',   'chat_type' => 'bundle', 'sort_order' => 330],
            ['identifier' => 'common_consent_marketing', 'chat_type' => 'bundle', 'sort_order' => 340],
        ];

        foreach ($originals as $orig) {
            DB::table('chat_steps')
                ->where('identifier', $orig['identifier'])
                ->where('chat_type', $orig['chat_type'])
                ->update(['sort_order' => $orig['sort_order']]);
        }

        \Illuminate\Support\Facades\Cache::forget('chat_steps_bundle');
    }
};
