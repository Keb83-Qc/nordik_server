<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $quoteTypeId = DB::table('quote_types')->where('slug', 'commercial')->value('id');

        $steps = [
            [
                'identifier' => 'full_name',
                'question'   => [
                    'fr' => 'Votre prénom et nom',
                    'en' => 'Your first and last name',
                    'es' => 'Su nombre y apellido',
                    'ht' => 'Prenon ak non ou',
                ],
            ],
            [
                'identifier' => 'company_name',
                'question'   => [
                    'fr' => "Nom de l'entreprise",
                    'en' => 'Company name',
                    'es' => 'Nombre de la empresa',
                    'ht' => 'Non antrepriz la',
                ],
            ],
            [
                'identifier' => 'main_shareholder',
                'question'   => [
                    'fr' => 'Actionnaire principal',
                    'en' => 'Main shareholder',
                    'es' => 'Accionista principal',
                    'ht' => 'Aksyonè prensipal',
                ],
            ],
            [
                'identifier' => 'other_shareholders',
                'question'   => [
                    'fr' => "S'il y a d'autres actionnaires, quels sont leurs noms ?",
                    'en' => 'If there are other shareholders, what are their names?',
                    'es' => 'Si hay otros accionistas, ¿cuáles son sus nombres?',
                    'ht' => 'Si gen lòt aksyonè, ki non yo?',
                ],
            ],
            [
                'identifier' => 'business_activity',
                'question'   => [
                    'fr' => 'Activité commerciale',
                    'en' => 'Business activity',
                    'es' => 'Actividad comercial',
                    'ht' => 'Aktivite komèsyal',
                ],
            ],
            [
                'identifier' => 'neq',
                'question'   => [
                    'fr' => 'NEQ',
                    'en' => 'NEQ',
                    'es' => 'NEQ',
                    'ht' => 'NEQ',
                ],
            ],
            [
                'identifier' => 'phone',
                'question'   => [
                    'fr' => 'Téléphone pour vous rejoindre',
                    'en' => 'Phone number to reach you',
                    'es' => 'Teléfono para contactarle',
                    'ht' => 'Telefòn pou kontakte ou',
                ],
            ],
            [
                'identifier' => 'email',
                'question'   => [
                    'fr' => 'Adresse courriel',
                    'en' => 'Email address',
                    'es' => 'Correo electrónico',
                    'ht' => 'Adrès imel',
                ],
            ],
            [
                'identifier' => 'address',
                'question'   => [
                    'fr' => 'Adresse civique',
                    'en' => 'Civic address',
                    'es' => 'Dirección cívica',
                    'ht' => 'Adrès sivik',
                ],
            ],
        ];

        foreach ($steps as $index => $step) {
            DB::table('chat_steps')->updateOrInsert(
                ['chat_type' => 'commercial', 'identifier' => $step['identifier']],
                [
                    'quote_type_id' => $quoteTypeId,
                    'question'      => json_encode($step['question'], JSON_UNESCAPED_UNICODE),
                    'input_type'    => 'text',
                    'options'       => null,
                    'sort_order'    => $index + 1,
                    'is_active'     => true,
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('chat_steps')
            ->where('chat_type', 'commercial')
            ->whereIn('identifier', [
                'full_name',
                'company_name',
                'main_shareholder',
                'other_shareholders',
                'business_activity',
                'neq',
                'phone',
                'email',
                'address',
            ])
            ->delete();
    }
};
