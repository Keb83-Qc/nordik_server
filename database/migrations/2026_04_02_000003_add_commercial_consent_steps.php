<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix #4 — Ajoute les étapes de consentement au flow commercial.
 *
 * Le flow commercial (quote_type_id=4, chat_type='commercial') avait 9 étapes
 * mais aucun consentement. Sans consentement, aucune preuve légale de collecte
 * de données personnelles n'est capturée. On ajoute les 4 consentements
 * standards (identiques aux autres types) après l'étape address.
 *
 * Les sort_order partent de 10 (address=9, donc consent_profile=10, etc.)
 * pour conserver la cohérence ascendante.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $steps = [
            [
                'identifier'  => 'consent_profile',
                'chat_type'   => 'commercial',
                'quote_type_id' => 4,
                'question'    => json_encode([
                    'fr' => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                    'en' => "Consent: collect/use/share certain information to better know you.",
                    'es' => "Consentimiento: recopilar/usar/compartir cierta información para conocerle mejor.",
                    'ht' => "Konsantman: ranmase/itilize/pataje kèk enfòmasyon pou pi byen konnen w.",
                ]),
                'input_type'  => 'consent',
                'options'     => null,
                'sort_order'  => 10,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'identifier'  => 'consent_marketing',
                'chat_type'   => 'commercial',
                'quote_type_id' => 4,
                'question'    => json_encode([
                    'fr' => "Consentement : vous faire part de promotions/produits/services/événements.",
                    'en' => "Consent: share promotions/products/services/events with you.",
                    'es' => "Consentimiento: compartir promociones/productos/servicios/eventos con usted.",
                    'ht' => "Konsantman: pataje pwomosyon/pwodui/sèvis/evènman avèk ou.",
                ]),
                'input_type'  => 'consent',
                'options'     => null,
                'sort_order'  => 11,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'identifier'  => 'marketing_email',
                'chat_type'   => 'commercial',
                'quote_type_id' => 4,
                'question'    => json_encode([
                    'fr' => "Souhaitez-vous recevoir ces communications par courriel ?",
                    'en' => "Would you like to receive these communications by email?",
                    'es' => "¿Desea recibir estas comunicaciones por correo electrónico?",
                    'ht' => "Èske ou ta renmen resevwa kominikasyon sa yo nan imèl?",
                ]),
                'input_type'  => 'radio',
                'options'     => json_encode(['Oui' => 'Oui', 'Non' => 'Non']),
                'sort_order'  => 12,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'identifier'  => 'consent_credit',
                'chat_type'   => 'commercial',
                'quote_type_id' => 4,
                'question'    => json_encode([
                    'fr' => "Nous permettez-vous de faire un survol de votre dossier de crédit (aucun impact) ?",
                    'en' => "Do you allow us to collect information from your credit file (no impact)?",
                    'es' => "¿Nos permite obtener información de su historial crediticio (sin impacto)?",
                    'ht' => "Èske w pèmèt nou pran enfòmasyon nan dosye kredi w (pa gen okenn enpak)?",
                ]),
                'input_type'  => 'consent',
                'options'     => null,
                'sort_order'  => 13,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        DB::table('chat_steps')->insert($steps);

        \Illuminate\Support\Facades\Cache::forget('chat_steps_commercial');
    }

    public function down(): void
    {
        DB::table('chat_steps')
            ->where('chat_type', 'commercial')
            ->whereIn('identifier', ['consent_profile', 'consent_marketing', 'marketing_email', 'consent_credit'])
            ->delete();

        \Illuminate\Support\Facades\Cache::forget('chat_steps_commercial');
    }
};
