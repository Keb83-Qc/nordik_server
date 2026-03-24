<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatStep;

class ChatStepsSeeder extends Seeder
{
    public function run(): void
    {
        // ─── AUTO ─────────────────────────────────────────────────────────────
        $autoSteps = [
            [
                'identifier' => 'identity',
                'question'   => "C'est noté. À qui ai-je le plaisir de m'adresser ? (Prénom et Nom)",
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'age',
                'question'   => "C'est noté. Quel âge avez-vous ?",
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'email',
                'question'   => "Parfait. À quelle adresse courriel puis-je vous envoyer la soumission ?",
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'phone',
                'question'   => "Dernière étape, votre numéro de téléphone ?",
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'best_contact_time',
                'question'   => "Quel est le meilleur moment pour vous contacter ?",
                'input_type' => 'radio',
                'options'    => [
                    'matin'          => 'Matin (8h - 12h)',
                    'apres_midi'     => 'Après-midi (12h - 17h)',
                    'soir'           => 'Soir (17h - 20h)',
                    'nimporte_quand' => "N'importe quand",
                ],
                'sort_order' => 50,
            ],
            [
                'identifier' => 'year',
                'question'   => "Quelle est l'année du véhicule ?",
                'input_type' => 'select',
                'sort_order' => 60,
            ],
            [
                'identifier' => 'brand',
                'question'   => "Quelle est la marque ?",
                'input_type' => 'select',
                'sort_order' => 70,
            ],
            [
                'identifier' => 'model',
                'question'   => "Et le modèle ?",
                'input_type' => 'select',
                'sort_order' => 80,
            ],
            [
                'identifier' => 'renewal_date',
                'question'   => "Quelle est votre date de renouvellement prévue ?",
                'input_type' => 'date',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'usage',
                'question'   => "C'est pour quel usage ?",
                'input_type' => 'radio',
                'options'    => [
                    'personnel'  => 'Personnel',
                    'commercial' => 'Commercial',
                ],
                'sort_order' => 100,
            ],
            [
                'identifier' => 'km_annuel',
                'question'   => "Environ combien de kilomètres faites-vous par année ?",
                'input_type' => 'select',
                'options'    => [
                    '0-15000'   => '0 - 15 000 km',
                    '15000-20000' => '15 000 - 20 000 km',
                    '20000+'    => '20 000 km et plus',
                ],
                'sort_order' => 110,
            ],
            [
                'identifier' => 'address',
                'question'   => "Parfait. Quelle est votre adresse de résidence ?",
                'input_type' => 'text',
                'sort_order' => 120,
            ],
            [
                'identifier' => 'existing_products',
                'question'   => "Avez-vous déjà des produits d'assurances et/ou de placements ?",
                'input_type' => 'radio',
                'options'    => [
                    'assurances' => 'Assurances',
                    'placements' => 'Placements',
                    'les_deux'   => 'Les deux',
                    'aucun'      => 'Aucun',
                ],
                'sort_order' => 130,
            ],
            [
                'identifier' => 'profession',
                'question'   => "Super. Quelle est votre profession ?",
                'input_type' => 'text',
                'sort_order' => 140,
            ],
            [
                'identifier' => 'license_number',
                'question'   => "Dernière petite chose : si vous avez votre numéro de permis, cela accélère le traitement. Sinon, vous pouvez passer cette étape !",
                'input_type' => 'text',
                'sort_order' => 150,
            ],
            [
                'identifier' => 'consent_profile',
                'question'   => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                'input_type' => 'consent',
                'sort_order' => 160,
            ],
            [
                'identifier' => 'consent_marketing',
                'question'   => "Consentement : vous faire part de promotions/produits/services/évènements.",
                'input_type' => 'consent',
                'sort_order' => 170,
            ],
            [
                'identifier' => 'marketing_email',
                'question'   => "Souhaitez-vous recevoir ces communications par courriel?",
                'input_type' => 'radio',
                'options'    => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'sort_order' => 180,
            ],
            [
                'identifier' => 'consent_credit',
                'question'   => "Nous permettez-vous de recueillir l'information de votre dossier de crédit (aucun impact)?",
                'input_type' => 'consent',
                'sort_order' => 190,
            ],
        ];

        // ─── HABITATION ───────────────────────────────────────────────────────
        $habitationSteps = [
            [
                'identifier' => 'identity',
                'question'   => "Quel est votre prénom, nom de famille, et vous êtes?",
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'age',
                'question'   => "Quel est votre âge?",
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'email',
                'question'   => "Quelle est votre adresse courriel?",
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'phone',
                'question'   => "Quel est votre numéro de téléphone?",
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'phone_is_cell',
                'question'   => "Est-ce un téléphone cellulaire?",
                'input_type' => 'consent',
                'sort_order' => 50,
            ],
            [
                'identifier' => 'best_contact_time',
                'question'   => "Quel est le meilleur moment pour vous contacter ?",
                'input_type' => 'radio',
                'options'    => [
                    'matin'          => 'Matin (8h - 12h)',
                    'apres_midi'     => 'Après-midi (12h - 17h)',
                    'soir'           => 'Soir (17h - 20h)',
                    'nimporte_quand' => "N'importe quand",
                ],
                'sort_order' => 60,
            ],
            [
                'identifier' => 'occupancy',
                'question'   => "Êtes-vous locataire ou propriétaire?",
                'input_type' => 'radio',
                'options'    => [
                    'locataire'    => 'Locataire',
                    'proprietaire' => 'Propriétaire',
                ],
                'sort_order' => 70,
            ],
            [
                'identifier' => 'property_type',
                'question'   => "Quel type d'habitation souhaitez-vous assurer?",
                'input_type' => 'radio',
                'options'    => [
                    'maison'      => 'Maison',
                    'condo'       => 'Condo',
                    'appartement' => 'Appartement',
                ],
                'sort_order' => 80,
            ],
            [
                'identifier' => 'address',
                'question'   => "Quelle est l'adresse de l'habitation à assurer?",
                'input_type' => 'text',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'hab_renewal_date',
                'question'   => "Quelle est votre date de renouvellement d'assurance habitation ?",
                'input_type' => 'date',
                'sort_order' => 100,
            ],
            [
                'identifier' => 'living_there',
                'question'   => "Vivez-vous présentement à cette adresse?",
                'input_type' => 'consent',
                'sort_order' => 110,
            ],
            [
                'identifier' => 'years_at_address',
                'question'   => "Depuis combien d'années résidez-vous à cette adresse?",
                'input_type' => 'number',
                'sort_order' => 120,
            ],
            [
                'identifier' => 'units_in_building',
                'question'   => "Combien y a-t-il d'unités dans l'immeuble? (si maison : 1)",
                'input_type' => 'number',
                'sort_order' => 130,
            ],
            [
                'identifier' => 'contents_amount',
                'question'   => "Pour quel montant souhaitez-vous assurer tous vos biens?",
                'input_type' => 'number',
                'sort_order' => 140,
            ],
            [
                'identifier' => 'electric_baseboard',
                'question'   => "Les plinthes électriques sont-elles le chauffage principal?",
                'input_type' => 'consent',
                'sort_order' => 150,
            ],
            [
                'identifier' => 'supp_heating',
                'question'   => "Avez-vous un chauffage d'appoint (poêle, foyer, etc.)?",
                'input_type' => 'consent',
                'sort_order' => 160,
            ],
            [
                'identifier' => 'years_insured',
                'question'   => "Depuis combien d'années possédez-vous de l'assurance habitation?",
                'input_type' => 'select',
                'options'    => [
                    '0'      => '0 an',
                    '1-2'    => '1-2 ans',
                    '3-5'    => '3-5 ans',
                    '6-10'   => '6-10 ans',
                    '11+'    => '11 ans et plus',
                ],
                'sort_order' => 170,
            ],
            [
                'identifier' => 'years_with_insurer',
                'question'   => "Depuis combien d'années êtes-vous avec votre assureur actuel?",
                'input_type' => 'number',
                'sort_order' => 180,
            ],
            [
                'identifier' => 'current_insurer',
                'question'   => "Quel est votre assureur actuel?",
                'input_type' => 'text',
                'sort_order' => 190,
            ],
            [
                'identifier' => 'marital_status',
                'question'   => "Quel est votre état civil?",
                'input_type' => 'select',
                'options'    => [
                    'celibataire' => 'Célibataire',
                    'conjoint'    => 'Conjoint(e) de fait',
                    'marie'       => 'Marié(e)',
                    'autre'       => 'Autre',
                ],
                'sort_order' => 200,
            ],
            [
                'identifier' => 'employment_status',
                'question'   => "Quel est votre statut professionnel?",
                'input_type' => 'select',
                'options'    => [
                    'employe'              => 'Employé(e)',
                    'travailleur_autonome' => 'Travailleur autonome',
                    'etudiant'             => 'Étudiant(e)',
                    'retraite'             => 'Retraité(e)',
                    'sans_emploi'          => 'Sans emploi',
                ],
                'sort_order' => 210,
            ],
            [
                'identifier' => 'education_level',
                'question'   => "Quel est le dernier diplôme obtenu?",
                'input_type' => 'select',
                'options'    => [
                    'secondaire'  => 'Secondaire',
                    'college'     => 'Collège/Cégep',
                    'universite'  => 'Université',
                    'autre'       => 'Autre',
                ],
                'sort_order' => 220,
            ],
            [
                'identifier' => 'industry',
                'question'   => "Quel est votre secteur d'activité?",
                'input_type' => 'text',
                'sort_order' => 230,
            ],
            [
                'identifier' => 'has_ia_products',
                'question'   => "Vous (ou votre conjoint(e)) détenez des produits d'assurance / placements (rabais possible)?",
                'input_type' => 'consent',
                'sort_order' => 240,
            ],
            [
                'identifier' => 'consent_profile',
                'question'   => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                'input_type' => 'consent',
                'sort_order' => 250,
            ],
            [
                'identifier' => 'consent_marketing',
                'question'   => "Consentement : vous faire part de promotions/produits/services/évènements.",
                'input_type' => 'consent',
                'sort_order' => 260,
            ],
            [
                'identifier' => 'marketing_email',
                'question'   => "Souhaitez-vous recevoir ces communications par courriel?",
                'input_type' => 'radio',
                'options'    => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'sort_order' => 270,
            ],
            [
                'identifier' => 'consent_credit',
                'question'   => "Nous permettez-vous de recueillir l'information de votre dossier de crédit (aucun impact)?",
                'input_type' => 'consent',
                'sort_order' => 280,
            ],
        ];

        // ─── BUNDLE ───────────────────────────────────────────────────────────
        // Common
        $bundleSteps = [
            [
                'identifier' => 'common_identity',
                'question'   => "Quel est votre prénom, nom et votre genre?",
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'common_age',
                'question'   => "Quel est votre âge?",
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'common_email',
                'question'   => "Quel est votre courriel?",
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'common_phone',
                'question'   => "Quel est votre numéro de téléphone?",
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'common_best_contact_time',
                'question'   => "Quel est le meilleur moment pour vous contacter ?",
                'input_type' => 'radio',
                'options'    => [
                    'matin'          => 'Matin (8h - 12h)',
                    'apres_midi'     => 'Après-midi (12h - 17h)',
                    'soir'           => 'Soir (17h - 20h)',
                    'nimporte_quand' => "N'importe quand",
                ],
                'sort_order' => 50,
            ],
            // Auto section
            [
                'identifier' => 'auto_year',
                'question'   => "Quelle est l'année de votre véhicule?",
                'input_type' => 'select',
                'sort_order' => 60,
            ],
            [
                'identifier' => 'auto_brand',
                'question'   => "Quelle est la marque?",
                'input_type' => 'select',
                'sort_order' => 70,
            ],
            [
                'identifier' => 'auto_model',
                'question'   => "Quel est le modèle?",
                'input_type' => 'select',
                'sort_order' => 80,
            ],
            [
                'identifier' => 'auto_renewal_date',
                'question'   => "Quelle est la date de renouvellement?",
                'input_type' => 'date',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'auto_usage',
                'question'   => "Quel est l'usage du véhicule?",
                'input_type' => 'radio',
                'options'    => [
                    'personnel'  => 'Personnel',
                    'commercial' => 'Commercial',
                ],
                'sort_order' => 100,
            ],
            [
                'identifier' => 'auto_km_annuel',
                'question'   => "Combien de kilomètres par année?",
                'input_type' => 'select',
                'options'    => [
                    '0-15000'     => '0 - 15 000 km',
                    '15000-20000' => '15 000 - 20 000 km',
                    '20000+'      => '20 000 km et plus',
                ],
                'sort_order' => 110,
            ],
            [
                'identifier' => 'auto_existing_products',
                'question'   => "Avez-vous déjà des produits (assurances / placements)?",
                'input_type' => 'radio',
                'options'    => [
                    'assurance'  => 'Assurance',
                    'placement'  => 'Placement',
                    'les_deux'   => 'Les deux',
                    'aucun'      => 'Aucun',
                ],
                'sort_order' => 120,
            ],
            [
                'identifier' => 'auto_license_number',
                'question'   => "Quel est votre numéro de permis de conduire?",
                'input_type' => 'text',
                'sort_order' => 130,
            ],
            // Habitation section
            [
                'identifier' => 'hab_occupancy',
                'question'   => "Êtes-vous locataire ou propriétaire?",
                'input_type' => 'radio',
                'options'    => [
                    'locataire'    => 'Locataire',
                    'proprietaire' => 'Propriétaire',
                ],
                'sort_order' => 140,
            ],
            [
                'identifier' => 'hab_property_type',
                'question'   => "Quel est le type de propriété?",
                'input_type' => 'radio',
                'options'    => [
                    'maison'      => 'Maison',
                    'condo'       => 'Condo',
                    'appartement' => 'Appartement',
                ],
                'sort_order' => 150,
            ],
            [
                'identifier' => 'hab_renewal_date',
                'question'   => "Quelle est votre date de renouvellement d'assurance habitation ?",
                'input_type' => 'date',
                'sort_order' => 160,
            ],
            [
                'identifier' => 'hab_address',
                'question'   => "Quelle est votre adresse complète?",
                'input_type' => 'text',
                'sort_order' => 170,
            ],
            [
                'identifier' => 'hab_living_there',
                'question'   => "Vivez-vous à cette adresse?",
                'input_type' => 'consent',
                'sort_order' => 180,
            ],
            [
                'identifier' => 'hab_move_in_date',
                'question'   => "Quelle est votre date d'emménagement?",
                'input_type' => 'date',
                'sort_order' => 190,
            ],
            [
                'identifier' => 'hab_units_in_building',
                'question'   => "Combien d'unités y a-t-il dans l'immeuble?",
                'input_type' => 'number',
                'sort_order' => 200,
            ],
            [
                'identifier' => 'hab_contents_amount',
                'question'   => "Quel est le montant approximatif de vos biens (contenu)?",
                'input_type' => 'number',
                'sort_order' => 210,
            ],
            [
                'identifier' => 'hab_electric_baseboard',
                'question'   => "Le chauffage principal est-il à plinthes électriques?",
                'input_type' => 'consent',
                'sort_order' => 220,
            ],
            [
                'identifier' => 'hab_supp_heating',
                'question'   => "Avez-vous un chauffage d'appoint (poêle, foyer, etc.)?",
                'input_type' => 'consent',
                'sort_order' => 230,
            ],
            [
                'identifier' => 'hab_years_insured',
                'question'   => "Depuis combien d'années êtes-vous assuré(e) en habitation?",
                'input_type' => 'select',
                'options'    => [
                    '0'   => '0 an',
                    '1-2' => '1 à 2 ans',
                    '3-5' => '3 à 5 ans',
                    '6-10' => '6 à 10 ans',
                    '11+' => '11 ans et plus',
                ],
                'sort_order' => 240,
            ],
            [
                'identifier' => 'hab_years_with_insurer',
                'question'   => "Depuis combien d'années êtes-vous avec votre assureur actuel?",
                'input_type' => 'number',
                'sort_order' => 250,
            ],
            [
                'identifier' => 'hab_current_insurer',
                'question'   => "Quel est le nom de votre assureur actuel?",
                'input_type' => 'text',
                'sort_order' => 260,
            ],
            // Profile section
            [
                'identifier' => 'hab_marital_status',
                'question'   => "Quel est votre état civil?",
                'input_type' => 'select',
                'options'    => [
                    'celibataire' => 'Célibataire',
                    'conjoint'    => 'Conjoint(e)',
                    'marie'       => 'Marié(e)',
                    'autre'       => 'Autre',
                ],
                'sort_order' => 270,
            ],
            [
                'identifier' => 'hab_employment_status',
                'question'   => "Quel est votre statut professionnel?",
                'input_type' => 'select',
                'options'    => [
                    'employe'              => 'Employé(e)',
                    'travailleur_autonome' => 'Travailleur autonome',
                    'etudiant'             => 'Étudiant(e)',
                    'retraite'             => 'Retraité(e)',
                    'sans_emploi'          => 'Sans emploi',
                ],
                'sort_order' => 280,
            ],
            [
                'identifier' => 'hab_education_level',
                'question'   => "Quel est votre dernier niveau de scolarité?",
                'input_type' => 'select',
                'options'    => [
                    'secondaire' => 'Secondaire',
                    'college'    => 'Collège',
                    'universite' => 'Université',
                    'autre'      => 'Autre',
                ],
                'sort_order' => 290,
            ],
            [
                'identifier' => 'hab_industry',
                'question'   => "Dans quel secteur travaillez-vous?",
                'input_type' => 'text',
                'sort_order' => 300,
            ],
            [
                'identifier' => 'hab_has_ia_products',
                'question'   => "Avez-vous des produits assurance / placements sous le même toit?",
                'input_type' => 'consent',
                'sort_order' => 310,
            ],
            // Consents
            [
                'identifier' => 'hab_consent_profile',
                'question'   => "Acceptez-vous le consentement de profilage?",
                'input_type' => 'consent',
                'sort_order' => 320,
            ],
            [
                'identifier' => 'hab_consent_marketing',
                'question'   => "Acceptez-vous de recevoir des communications marketing?",
                'input_type' => 'consent',
                'sort_order' => 330,
            ],
            [
                'identifier' => 'hab_marketing_email',
                'question'   => "Souhaitez-vous recevoir le marketing par courriel?",
                'input_type' => 'radio',
                'options'    => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'sort_order' => 340,
            ],
            [
                'identifier' => 'hab_consent_credit',
                'question'   => "Autorisez-vous une vérification de crédit si nécessaire?",
                'input_type' => 'consent',
                'sort_order' => 350,
            ],
        ];

        foreach ($autoSteps as $step) {
            ChatStep::updateOrCreate(
                ['identifier' => $step['identifier'], 'chat_type' => 'auto'],
                array_merge($step, ['chat_type' => 'auto', 'is_active' => true])
            );
        }

        foreach ($habitationSteps as $step) {
            ChatStep::updateOrCreate(
                ['identifier' => $step['identifier'], 'chat_type' => 'habitation'],
                array_merge($step, ['chat_type' => 'habitation', 'is_active' => true])
            );
        }

        foreach ($bundleSteps as $step) {
            ChatStep::updateOrCreate(
                ['identifier' => $step['identifier'], 'chat_type' => 'bundle'],
                array_merge($step, ['chat_type' => 'bundle', 'is_active' => true])
            );
        }
    }
}
