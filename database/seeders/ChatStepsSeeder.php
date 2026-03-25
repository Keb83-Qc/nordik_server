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
                'question'   => [
                    'fr' => "C'est noté. À qui ai-je le plaisir de m'adresser ? (Prénom et Nom)",
                    'en' => "Noted. Who do I have the pleasure of speaking with? (First and Last name)",
                    'es' => "Anotado. ¿Con quién tengo el gusto de hablar? (Nombre y Apellido)",
                    'ht' => "Mwen note sa. Avèk kiyès mwen gen plezi pale? (Prenon ak Non)",
                ],
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'age',
                'question'   => [
                    'fr' => "C'est noté. Quel âge avez-vous ?",
                    'en' => "Noted. How old are you?",
                    'es' => "Anotado. ¿Qué edad tiene?",
                    'ht' => "Mwen note sa. Ki laj ou?",
                ],
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'email',
                'question'   => [
                    'fr' => "Parfait. À quelle adresse courriel puis-je vous envoyer la soumission ?",
                    'en' => "Perfect. What email address can I send the quote to?",
                    'es' => "Perfecto. ¿A qué correo puedo enviarle la cotización?",
                    'ht' => "Trè byen. Nan ki adrès imel mwen ka voye sitasyon an ba ou?",
                ],
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'phone',
                'question'   => [
                    'fr' => "Dernière étape, votre numéro de téléphone ?",
                    'en' => "Last step, your phone number?",
                    'es' => "Último paso: ¿su número de teléfono?",
                    'ht' => "Dènye etap: nimewo telefòn ou?",
                ],
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'best_contact_time',
                'question'   => [
                    'fr' => "Quel est le meilleur moment pour vous contacter ?",
                    'en' => "What is the best time to reach you?",
                    'es' => "¿Cuál es el mejor momento para contactarlo?",
                    'ht' => "Ki moman ki pi bon pou kontakte w?",
                ],
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
                'question'   => [
                    'fr' => "Quelle est l'année du véhicule ?",
                    'en' => "What is the vehicle's year?",
                    'es' => "¿Cuál es el año del vehículo?",
                    'ht' => "Ki ane machin nan?",
                ],
                'input_type' => 'select',
                'sort_order' => 60,
            ],
            [
                'identifier' => 'brand',
                'question'   => [
                    'fr' => "Quelle est la marque ?",
                    'en' => "What is the make?",
                    'es' => "¿Cuál es la marca?",
                    'ht' => "Ki mak la?",
                ],
                'input_type' => 'select',
                'sort_order' => 70,
            ],
            [
                'identifier' => 'model',
                'question'   => [
                    'fr' => "Et le modèle ?",
                    'en' => "And the model?",
                    'es' => "¿Y el modelo?",
                    'ht' => "Epi modèl la?",
                ],
                'input_type' => 'select',
                'sort_order' => 80,
            ],
            [
                'identifier' => 'renewal_date',
                'question'   => [
                    'fr' => "Quelle est votre date de renouvellement prévue ?",
                    'en' => "What is your expected renewal date?",
                    'es' => "¿Cuál es su fecha prevista de renovación?",
                    'ht' => "Ki dat renouvèlman ou prevwa?",
                ],
                'input_type' => 'date',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'usage',
                'question'   => [
                    'fr' => "C'est pour quel usage ?",
                    'en' => "What will it be used for?",
                    'es' => "¿Para qué uso será?",
                    'ht' => "Pou ki itilizasyon?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'personnel'  => 'Personnel',
                    'commercial' => 'Commercial',
                ],
                'sort_order' => 100,
            ],
            [
                'identifier' => 'km_annuel',
                'question'   => [
                    'fr' => "Environ combien de kilomètres faites-vous par année ?",
                    'en' => "About how many kilometers do you drive per year?",
                    'es' => "Aproximadamente, ¿cuántos kilómetros conduce al año?",
                    'ht' => "Apeprè konbyen kilomèt ou fè pa ane?",
                ],
                'input_type' => 'number',
                'sort_order' => 110,
            ],
            [
                'identifier' => 'address',
                'question'   => [
                    'fr' => "Parfait. Quelle est votre adresse de résidence ?",
                    'en' => "Perfect. What is your home address?",
                    'es' => "Perfecto. ¿Cuál es su dirección de residencia?",
                    'ht' => "Trè byen. Ki adrès rezidans ou?",
                ],
                'input_type' => 'text',
                'sort_order' => 120,
            ],
            [
                'identifier' => 'existing_products',
                'question'   => [
                    'fr' => "Avez-vous déjà des produits d'assurances et/ou de placements ?",
                    'en' => "Do you already have insurance and/or investment products?",
                    'es' => "¿Ya tiene productos de seguros y/o de inversión?",
                    'ht' => "Èske ou deja gen pwodwi asirans ak/oswa envestisman?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'assurance'  => 'Assurances',
                    'placements' => 'Placements',
                    'both'       => 'Les deux',
                    'none'       => 'Aucun',
                ],
                'sort_order' => 130,
            ],
            [
                'identifier' => 'profession',
                'question'   => [
                    'fr' => "Super. Quelle est votre profession ?",
                    'en' => "Great. What is your occupation?",
                    'es' => "Genial. ¿Cuál es su ocupación?",
                    'ht' => "Super. Ki travay ou fè?",
                ],
                'input_type' => 'text',
                'sort_order' => 140,
            ],
            [
                'identifier' => 'license_number',
                'question'   => [
                    'fr' => "Dernière petite chose : si vous avez votre numéro de permis, cela accélère le traitement. Sinon, vous pouvez passer cette étape !",
                    'en' => "One last thing: if you have your driver's license number, it speeds up processing. Otherwise, you can skip this step!",
                    'es' => "Una última cosa: si tiene su número de licencia de conducir, acelera el trámite. Si no, ¡puede omitir este paso!",
                    'ht' => "Yon ti dènye bagay: si ou gen nimewo pèmi w, sa ap akselere tretman an. Sinon, ou ka sote etap sa a!",
                ],
                'input_type' => 'text',
                'sort_order' => 150,
            ],
            [
                'identifier' => 'consent_profile',
                'question'   => [
                    'fr' => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                    'en' => "Consent: collect/use/share certain information to know you better.",
                    'es' => "Consentimiento: recopilar/usar/compartir cierta información para conocerle mejor.",
                    'ht' => "Konsantman: ranmase/itilize/pataje kèk enfòmasyon pou pi byen konnen w.",
                ],
                'input_type' => 'consent',
                'sort_order' => 160,
            ],
            [
                'identifier' => 'consent_marketing',
                'question'   => [
                    'fr' => "Consentement : vous faire part de promotions/produits/services/évènements.",
                    'en' => "Consent: share promotions/products/services/events with you.",
                    'es' => "Consentimiento: compartir promociones/productos/servicios/eventos con usted.",
                    'ht' => "Konsantman: pataje pwomosyon/pwodui/sèvis/evènman avèk ou.",
                ],
                'input_type' => 'consent',
                'sort_order' => 170,
            ],
            [
                'identifier' => 'marketing_email',
                'question'   => [
                    'fr' => "Souhaitez-vous recevoir ces communications par courriel?",
                    'en' => "Would you like to receive these communications by email?",
                    'es' => "¿Desea recibir estas comunicaciones por correo electrónico?",
                    'ht' => "Èske ou ta renmen resevwa kominikasyon sa yo nan imèl?",
                ],
                'input_type' => 'consent',
                'sort_order' => 180,
            ],
            [
                'identifier' => 'consent_credit',
                'question'   => [
                    'fr' => "Nous permettez-vous de recueillir l'information de votre dossier de crédit (aucun impact)?",
                    'en' => "Do you allow us to collect information from your credit file (no impact)?",
                    'es' => "¿Nos permite obtener información de su historial crediticio (sin impacto)?",
                    'ht' => "Èske w pèmèt nou pran enfòmasyon nan dosye kredi w (pa gen okenn enpak)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 190,
            ],
        ];

        // ─── HABITATION ───────────────────────────────────────────────────────
        $habitationSteps = [
            [
                'identifier' => 'identity',
                'question'   => [
                    'fr' => "Quel est votre prénom, nom de famille, et vous êtes?",
                    'en' => "What is your first name, last name, and you are?",
                    'es' => "¿Cuál es tu nombre, apellido y qué eres?",
                    'ht' => "Ki premye non w, siyati w, epi kisa ou ye?",
                ],
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'age',
                'question'   => [
                    'fr' => "Quel est votre âge?",
                    'en' => "What is your age?",
                    'es' => "¿Qué edad tienes?",
                    'ht' => "Ki laj ou genyen?",
                ],
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'email',
                'question'   => [
                    'fr' => "Quelle est votre adresse courriel?",
                    'en' => "What is your email address?",
                    'es' => "¿Cuál es tu correo electrónico?",
                    'ht' => "Ki adrès imèl ou?",
                ],
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'phone',
                'question'   => [
                    'fr' => "Quel est votre numéro de téléphone?",
                    'en' => "What is your phone number?",
                    'es' => "¿Cuál es tu número de teléfono?",
                    'ht' => "Ki nimewo telefòn ou?",
                ],
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'phone_is_cell',
                'question'   => [
                    'fr' => "Est-ce un téléphone cellulaire?",
                    'en' => "Is this a cell phone?",
                    'es' => "¿Es un teléfono celular?",
                    'ht' => "Èske se yon telefòn selilè?",
                ],
                'input_type' => 'consent',
                'sort_order' => 50,
            ],
            [
                'identifier' => 'best_contact_time',
                'question'   => [
                    'fr' => "Quel est le meilleur moment pour vous contacter ?",
                    'en' => "What is the best time to reach you?",
                    'es' => "¿Cuál es el mejor momento para contactarlo?",
                    'ht' => "Ki moman ki pi bon pou kontakte w?",
                ],
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
                'question'   => [
                    'fr' => "Êtes-vous locataire ou propriétaire?",
                    'en' => "Are you a tenant or an owner?",
                    'es' => "¿Eres inquilino o propietario?",
                    'ht' => "Èske w se lokatè oswa pwopriyetè?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'locataire'    => 'Locataire',
                    'proprietaire' => 'Propriétaire',
                ],
                'sort_order' => 70,
            ],
            [
                'identifier' => 'property_type',
                'question'   => [
                    'fr' => "Quel type d'habitation souhaitez-vous assurer?",
                    'en' => "What type of property do you want to insure?",
                    'es' => "¿Qué tipo de vivienda deseas asegurar?",
                    'ht' => "Ki kalite kay ou vle asire?",
                ],
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
                'question'   => [
                    'fr' => "Quelle est l'adresse de l'habitation à assurer?",
                    'en' => "What is the address of the property to be insured?",
                    'es' => "¿Cuál es la dirección de la vivienda a asegurar?",
                    'ht' => "Ki adrès kay ou vle asire a?",
                ],
                'input_type' => 'text',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'hab_renewal_date',
                'question'   => [
                    'fr' => "Quelle est votre date de renouvellement d'assurance habitation ?",
                    'en' => "What is your home insurance renewal date?",
                    'es' => "¿Cuál es la fecha de renovación de su seguro de hogar?",
                    'ht' => "Ki dat renouvèlman asirans kay ou a?",
                ],
                'input_type' => 'date',
                'sort_order' => 100,
            ],
            [
                'identifier' => 'living_there',
                'question'   => [
                    'fr' => "Vivez-vous présentement à cette adresse?",
                    'en' => "Do you currently live at this address?",
                    'es' => "¿Vives actualmente en esta dirección?",
                    'ht' => "Èske w ap viv nan adrès sa a kounye a?",
                ],
                'input_type' => 'consent',
                'sort_order' => 110,
            ],
            [
                'identifier' => 'years_at_address',
                'question'   => [
                    'fr' => "Depuis combien d'années résidez-vous à cette adresse?",
                    'en' => "How many years have you been living at this address?",
                    'es' => "¿Desde hace cuántos años resides en esta dirección?",
                    'ht' => "Depi konbyen ane ou ap viv nan adrès sa a?",
                ],
                'input_type' => 'number',
                'sort_order' => 120,
            ],
            [
                'identifier' => 'units_in_building',
                'question'   => [
                    'fr' => "Combien y a-t-il d'unités dans l'immeuble? (si maison : 1)",
                    'en' => "How many units are in the building? (if a house: 1)",
                    'es' => "¿Cuántas unidades hay en el edificio? (si es una casa: 1)",
                    'ht' => "Konbyen inite ki genyen nan bilding nan? (si se yon kay: 1)",
                ],
                'input_type' => 'number',
                'sort_order' => 130,
            ],
            [
                'identifier' => 'contents_amount',
                'question'   => [
                    'fr' => "Pour quel montant souhaitez-vous assurer tous vos biens?",
                    'en' => "For what amount would you like to insure all your belongings?",
                    'es' => "¿Por qué monto deseas asegurar todos tus bienes?",
                    'ht' => "Pou ki montan ou ta renmen asire tout byen ou yo?",
                ],
                'input_type' => 'number',
                'sort_order' => 140,
            ],
            [
                'identifier' => 'electric_baseboard',
                'question'   => [
                    'fr' => "Les plinthes électriques sont-elles le chauffage principal?",
                    'en' => "Are electric baseboards the main heating source?",
                    'es' => "¿Son los zócalos eléctricos la calefacción principal?",
                    'ht' => "Èske plint elektrik yo se chofaj prensipal la?",
                ],
                'input_type' => 'consent',
                'sort_order' => 150,
            ],
            [
                'identifier' => 'supp_heating',
                'question'   => [
                    'fr' => "Avez-vous un chauffage d'appoint (poêle, foyer, etc.)?",
                    'en' => "Do you have supplemental heating (stove, fireplace, etc.)?",
                    'es' => "¿Tienes calefacción adicional (estufa, chimenea, etc.)?",
                    'ht' => "Èske w gen yon chofaj siplemantè (recho, fwaye, elatriye)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 160,
            ],
            [
                'identifier' => 'years_insured',
                'question'   => [
                    'fr' => "Depuis combien d'années possédez-vous de l'assurance habitation?",
                    'en' => "For how many years have you had home insurance?",
                    'es' => "¿Desde hace cuántos años tienes seguro de hogar?",
                    'ht' => "Depi konbyen ane ou genyen asirans kay?",
                ],
                'input_type' => 'select',
                'options'    => [
                    '0'   => '0 an',
                    '1-2' => '1-2 ans',
                    '3-5' => '3-5 ans',
                    '6-10'=> '6-10 ans',
                    '11+' => '11 ans et plus',
                ],
                'sort_order' => 170,
            ],
            [
                'identifier' => 'years_with_insurer',
                'question'   => [
                    'fr' => "Depuis combien d'années êtes-vous avec votre assureur actuel?",
                    'en' => "How many years have you been with your current insurer?",
                    'es' => "¿Cuántos años llevas con tu aseguradora actual?",
                    'ht' => "Depi konbyen ane ou avèk asirè w la kounye a?",
                ],
                'input_type' => 'number',
                'sort_order' => 180,
            ],
            [
                'identifier' => 'current_insurer',
                'question'   => [
                    'fr' => "Quel est votre assureur actuel?",
                    'en' => "Who is your current insurer?",
                    'es' => "¿Cuál es tu aseguradora actual?",
                    'ht' => "Kiyès ki asirè ou kounye a?",
                ],
                'input_type' => 'text',
                'sort_order' => 190,
            ],
            [
                'identifier' => 'marital_status',
                'question'   => [
                    'fr' => "Quel est votre état civil?",
                    'en' => "What is your marital status?",
                    'es' => "¿Cuál es tu estado civil?",
                    'ht' => "Ki estati sivil ou?",
                ],
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
                'question'   => [
                    'fr' => "Quel est votre statut professionnel?",
                    'en' => "What is your employment status?",
                    'es' => "¿Cuál es tu situación laboral?",
                    'ht' => "Ki sitiyasyon pwofesyonèl ou?",
                ],
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
                'question'   => [
                    'fr' => "Quel est le dernier diplôme obtenu?",
                    'en' => "What is the highest degree you have obtained?",
                    'es' => "¿Cuál es el último diploma que obtuviste?",
                    'ht' => "Ki dènye diplòm ou jwenn?",
                ],
                'input_type' => 'select',
                'options'    => [
                    'secondaire' => 'Secondaire',
                    'college'    => 'Collège/Cégep',
                    'universite' => 'Université',
                    'autre'      => 'Autre',
                ],
                'sort_order' => 220,
            ],
            [
                'identifier' => 'industry',
                'question'   => [
                    'fr' => "Quel est votre secteur d'activité?",
                    'en' => "What is your industry?",
                    'es' => "¿En qué sector trabajas?",
                    'ht' => "Nan ki sektè w ap travay?",
                ],
                'input_type' => 'text',
                'sort_order' => 230,
            ],
            [
                'identifier' => 'has_ia_products',
                'question'   => [
                    'fr' => "Vous (ou votre conjoint(e)) détenez des produits d'assurance / placements (rabais possible)?",
                    'en' => "Do you (or your spouse) hold insurance/investment products (possible discount)?",
                    'es' => "¿Tú (o tu cónyuge) tienen productos de seguros/inversiones (posible descuento)?",
                    'ht' => "Èske w (oswa konjwen w) gen pwodui asirans / envestisman (rabè posib)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 240,
            ],
            [
                'identifier' => 'consent_profile',
                'question'   => [
                    'fr' => "Consentement : recueillir/utiliser/communiquer certains renseignements pour mieux vous connaître.",
                    'en' => "Consent: collect/use/share certain information to know you better.",
                    'es' => "Consentimiento: recopilar/usar/compartir cierta información para conocerle mejor.",
                    'ht' => "Konsantman: ranmase/itilize/pataje kèk enfòmasyon pou pi byen konnen w.",
                ],
                'input_type' => 'consent',
                'sort_order' => 250,
            ],
            [
                'identifier' => 'consent_marketing',
                'question'   => [
                    'fr' => "Consentement : vous faire part de promotions/produits/services/évènements.",
                    'en' => "Consent: share promotions/products/services/events with you.",
                    'es' => "Consentimiento: compartir promociones/productos/servicios/eventos contigo.",
                    'ht' => "Konsantman: pataje pwomosyon/pwodui/sèvis/evènman avèk ou.",
                ],
                'input_type' => 'consent',
                'sort_order' => 260,
            ],
            [
                'identifier' => 'marketing_email',
                'question'   => [
                    'fr' => "Souhaitez-vous recevoir ces communications par courriel?",
                    'en' => "Would you like to receive these communications by email?",
                    'es' => "¿Deseas recibir estas comunicaciones por correo electrónico?",
                    'ht' => "Èske ou ta renmen resevwa kominikasyon sa yo nan imèl?",
                ],
                'input_type' => 'consent',
                'sort_order' => 270,
            ],
            [
                'identifier' => 'consent_credit',
                'question'   => [
                    'fr' => "Nous permettez-vous de recueillir l'information de votre dossier de crédit (aucun impact)?",
                    'en' => "Do you allow us to collect information from your credit file (no impact)?",
                    'es' => "¿Nos permites obtener información de tu historial crediticio (sin impacto)?",
                    'ht' => "Èske w pèmèt nou pran enfòmasyon nan dosye kredi w (pa gen okenn enpak)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 280,
            ],
        ];

        // ─── BUNDLE (Auto + Habitation) ────────────────────────────────────────
        $bundleSteps = [
            // --- Commun ---
            [
                'identifier' => 'common_identity',
                'question'   => [
                    'fr' => "Parfait. Quel est votre prénom, nom et votre genre?",
                    'en' => "Let's start with your information.",
                    'es' => "Empecemos con sus datos.",
                    'ht' => "Ann kòmanse ak enfòmasyon ou yo.",
                ],
                'input_type' => 'text',
                'sort_order' => 10,
            ],
            [
                'identifier' => 'common_age',
                'question'   => [
                    'fr' => "Quel est votre âge?",
                    'en' => "What is your age?",
                    'es' => "¿Cuál es su edad?",
                    'ht' => "Ki laj ou?",
                ],
                'input_type' => 'number',
                'sort_order' => 20,
            ],
            [
                'identifier' => 'common_email',
                'question'   => [
                    'fr' => "Quel est votre courriel?",
                    'en' => "What is your email?",
                    'es' => "¿Cuál es su correo?",
                    'ht' => "Ki imel ou?",
                ],
                'input_type' => 'email',
                'sort_order' => 30,
            ],
            [
                'identifier' => 'common_phone',
                'question'   => [
                    'fr' => "Quel est votre numéro de téléphone?",
                    'en' => "What is your phone number?",
                    'es' => "¿Cuál es su teléfono?",
                    'ht' => "Ki nimewo telefòn ou?",
                ],
                'input_type' => 'phone',
                'sort_order' => 40,
            ],
            [
                'identifier' => 'common_best_contact_time',
                'question'   => [
                    'fr' => "Quel est le meilleur moment pour vous contacter ?",
                    'en' => "What is the best time to reach you?",
                    'es' => "¿Cuál es el mejor momento para contactarlo?",
                    'ht' => "Ki pi bon moman pou kontakte w?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'matin'          => 'Matin (8h - 12h)',
                    'apres_midi'     => 'Après-midi (12h - 17h)',
                    'soir'           => 'Soir (17h - 20h)',
                    'nimporte_quand' => "N'importe quand",
                ],
                'sort_order' => 50,
            ],
            // --- Auto ---
            [
                'identifier' => 'auto_year',
                'question'   => [
                    'fr' => "Quelle est l'année de votre véhicule?",
                    'en' => "What is the vehicle year?",
                    'es' => "¿Año del vehículo?",
                    'ht' => "Ki ane machin nan?",
                ],
                'input_type' => 'select',
                'sort_order' => 60,
            ],
            [
                'identifier' => 'auto_brand',
                'question'   => [
                    'fr' => "Quelle est la marque?",
                    'en' => "What is the vehicle brand?",
                    'es' => "¿Marca del vehículo?",
                    'ht' => "Ki mak machin nan?",
                ],
                'input_type' => 'select',
                'sort_order' => 70,
            ],
            [
                'identifier' => 'auto_model',
                'question'   => [
                    'fr' => "Quel est le modèle?",
                    'en' => "What is the model?",
                    'es' => "¿Modelo?",
                    'ht' => "Ki modèl la?",
                ],
                'input_type' => 'select',
                'sort_order' => 80,
            ],
            [
                'identifier' => 'auto_renewal',
                'question'   => [
                    'fr' => "Quelle est la date de renouvellement?",
                    'en' => "What is your renewal date?",
                    'es' => "¿Fecha de renovación?",
                    'ht' => "Ki dat renouvèlman an?",
                ],
                'input_type' => 'date',
                'sort_order' => 90,
            ],
            [
                'identifier' => 'auto_usage',
                'question'   => [
                    'fr' => "Quel est l'usage du véhicule?",
                    'en' => "Main usage?",
                    'es' => "¿Uso principal?",
                    'ht' => "Ki itilizasyon prensipal la?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'personnel'  => 'Personnel',
                    'commercial' => 'Commercial',
                ],
                'sort_order' => 100,
            ],
            [
                'identifier' => 'auto_km',
                'question'   => [
                    'fr' => "Combien de kilomètres par année?",
                    'en' => "How many kilometers per year?",
                    'es' => "¿Kilómetros por año?",
                    'ht' => "Konbyen kilomèt pa ane?",
                ],
                'input_type' => 'number',
                'sort_order' => 110,
            ],
            [
                'identifier' => 'auto_profession',
                'question'   => [
                    'fr' => "Quelle est votre profession?",
                    'en' => "What is your profession?",
                    'es' => "¿Profesión?",
                    'ht' => "Ki metye ou?",
                ],
                'input_type' => 'text',
                'sort_order' => 120,
            ],
            [
                'identifier' => 'auto_existing_products',
                'question'   => [
                    'fr' => "Avez-vous déjà des produits (assurances / placements)?",
                    'en' => "Do you already have iA products?",
                    'es' => "¿Ya tiene productos con iA?",
                    'ht' => "Èske ou deja gen pwodwi iA?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'assurance'  => 'Assurance',
                    'placement'  => 'Placement',
                    'both'       => 'Les deux',
                    'none'       => 'Aucun',
                ],
                'sort_order' => 130,
            ],
            [
                'identifier' => 'auto_license',
                'question'   => [
                    'fr' => "Quel est votre numéro de permis de conduire?",
                    'en' => "Driver's license number (optional)",
                    'es' => "Número de licencia (opcional)",
                    'ht' => "Nimewo pèmi (opsyonèl)",
                ],
                'input_type' => 'text',
                'sort_order' => 140,
            ],
            // --- Habitation ---
            [
                'identifier' => 'hab_occupancy',
                'question'   => [
                    'fr' => "Êtes-vous locataire ou propriétaire?",
                    'en' => "Are you a tenant or an owner?",
                    'es' => "¿Es inquilino o propietario?",
                    'ht' => "Ou se lokatè oswa pwopriyetè?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'locataire'    => 'Locataire',
                    'proprietaire' => 'Propriétaire',
                ],
                'sort_order' => 150,
            ],
            [
                'identifier' => 'hab_property_type',
                'question'   => [
                    'fr' => "Quel est le type de propriété?",
                    'en' => "Property type?",
                    'es' => "¿Tipo de propiedad?",
                    'ht' => "Ki kalite kay?",
                ],
                'input_type' => 'radio',
                'options'    => [
                    'maison'      => 'Maison',
                    'condo'       => 'Condo',
                    'appartement' => 'Appartement',
                ],
                'sort_order' => 160,
            ],
            [
                'identifier' => 'hab_address',
                'question'   => [
                    'fr' => "Quelle est votre adresse?",
                    'en' => "Home address?",
                    'es' => "¿Dirección de la vivienda?",
                    'ht' => "Ki adrès kay la?",
                ],
                'input_type' => 'text',
                'sort_order' => 170,
            ],
            [
                'identifier' => 'hab_renewal_date',
                'question'   => [
                    'fr' => "Quelle est votre date de renouvellement d'assurance habitation ?",
                    'en' => "What is your home insurance renewal date?",
                    'es' => "¿Cuál es la fecha de renovación de su seguro de hogar?",
                    'ht' => "Ki dat renouvèlman asirans kay ou a?",
                ],
                'input_type' => 'date',
                'sort_order' => 180,
            ],
            [
                'identifier' => 'hab_living_there',
                'question'   => [
                    'fr' => "Vivez-vous à cette adresse?",
                    'en' => "Do you live at this address?",
                    'es' => "¿Vive en esta dirección?",
                    'ht' => "Èske ou rete nan adrès sa a?",
                ],
                'input_type' => 'consent',
                'sort_order' => 190,
            ],
            [
                'identifier' => 'hab_years_at_address',
                'question'   => [
                    'fr' => "Depuis combien d'années êtes-vous à cette adresse?",
                    'en' => "How many years at this address?",
                    'es' => "¿Cuántos años en esta dirección?",
                    'ht' => "Depi konbyen ane nan adrès sa a?",
                ],
                'input_type' => 'number',
                'sort_order' => 200,
            ],
            [
                'identifier' => 'hab_units_in_building',
                'question'   => [
                    'fr' => "Combien d'unités y a-t-il dans l'immeuble?",
                    'en' => "How many units in the building?",
                    'es' => "¿Cuántas unidades hay en el edificio?",
                    'ht' => "Konbyen inite ki genyen nan bilding nan?",
                ],
                'input_type' => 'number',
                'sort_order' => 210,
            ],
            [
                'identifier' => 'hab_contents_amount',
                'question'   => [
                    'fr' => "Quel est le montant approximatif de vos biens (contenu)?",
                    'en' => "Approximate contents amount?",
                    'es' => "¿Monto aproximado de sus bienes?",
                    'ht' => "Ki valè apeprè tout byen ou yo?",
                ],
                'input_type' => 'number',
                'sort_order' => 220,
            ],
            [
                'identifier' => 'hab_electric_baseboard',
                'question'   => [
                    'fr' => "Le chauffage principal est-il à plinthes électriques?",
                    'en' => "Are electric baseboards the main heating?",
                    'es' => "¿Son los zócalos eléctricos la calefacción principal?",
                    'ht' => "Èske plint elektrik yo se chofaj prensipal la?",
                ],
                'input_type' => 'consent',
                'sort_order' => 230,
            ],
            [
                'identifier' => 'hab_supp_heating',
                'question'   => [
                    'fr' => "Avez-vous un chauffage d'appoint (poêle, foyer, etc.)?",
                    'en' => "Do you have supplemental heating?",
                    'es' => "¿Tienes calefacción adicional?",
                    'ht' => "Èske w gen yon chofaj siplemantè?",
                ],
                'input_type' => 'consent',
                'sort_order' => 240,
            ],
            [
                'identifier' => 'hab_years_insured',
                'question'   => [
                    'fr' => "Depuis combien d'années êtes-vous assuré(e) en habitation?",
                    'en' => "Years with home insurance?",
                    'es' => "¿Años con seguro de hogar?",
                    'ht' => "Depi konbyen ane ou gen asirans kay?",
                ],
                'input_type' => 'select',
                'options'    => [
                    '0'   => '0 an',
                    '1-2' => '1 à 2 ans',
                    '3-5' => '3 à 5 ans',
                    '6-10'=> '6 à 10 ans',
                    '11+' => '11 ans et plus',
                ],
                'sort_order' => 250,
            ],
            [
                'identifier' => 'hab_years_with_insurer',
                'question'   => [
                    'fr' => "Depuis combien d'années êtes-vous avec votre assureur actuel?",
                    'en' => "Years with current insurer?",
                    'es' => "¿Años con el asegurador actual?",
                    'ht' => "Depi konbyen ane ou avèk asirè w la kounye a?",
                ],
                'input_type' => 'number',
                'sort_order' => 260,
            ],
            [
                'identifier' => 'hab_current_insurer',
                'question'   => [
                    'fr' => "Quel est le nom de votre assureur actuel?",
                    'en' => "Who is your current insurer?",
                    'es' => "¿Cuál es tu asegurador actual?",
                    'ht' => "Kiyès ki asirè ou kounye a?",
                ],
                'input_type' => 'text',
                'sort_order' => 270,
            ],
            [
                'identifier' => 'hab_marital_status',
                'question'   => [
                    'fr' => "Quel est votre état civil?",
                    'en' => "What is your marital status?",
                    'es' => "¿Cuál es tu estado civil?",
                    'ht' => "Ki estati sivil ou?",
                ],
                'input_type' => 'select',
                'options'    => [
                    'celibataire' => 'Célibataire',
                    'conjoint'    => 'Conjoint(e)',
                    'marie'       => 'Marié(e)',
                    'autre'       => 'Autre',
                ],
                'sort_order' => 280,
            ],
            [
                'identifier' => 'hab_employment_status',
                'question'   => [
                    'fr' => "Quel est votre statut professionnel?",
                    'en' => "What is your employment status?",
                    'es' => "¿Cuál es tu situación laboral?",
                    'ht' => "Ki sitiyasyon pwofesyonèl ou?",
                ],
                'input_type' => 'select',
                'options'    => [
                    'employe'              => 'Employé(e)',
                    'travailleur_autonome' => 'Travailleur autonome',
                    'etudiant'             => 'Étudiant(e)',
                    'retraite'             => 'Retraité(e)',
                    'sans_emploi'          => 'Sans emploi',
                ],
                'sort_order' => 290,
            ],
            [
                'identifier' => 'hab_education_level',
                'question'   => [
                    'fr' => "Quel est votre dernier niveau de scolarité?",
                    'en' => "What is your highest education level?",
                    'es' => "¿Cuál es tu último nivel de educación?",
                    'ht' => "Ki dènye nivo edikasyon ou?",
                ],
                'input_type' => 'select',
                'options'    => [
                    'secondaire' => 'Secondaire',
                    'college'    => 'Collège',
                    'universite' => 'Université',
                    'autre'      => 'Autre',
                ],
                'sort_order' => 300,
            ],
            [
                'identifier' => 'hab_industry',
                'question'   => [
                    'fr' => "Dans quel secteur travaillez-vous?",
                    'en' => "What is your industry?",
                    'es' => "¿En qué sector trabajas?",
                    'ht' => "Nan ki sektè w ap travay?",
                ],
                'input_type' => 'text',
                'sort_order' => 310,
            ],
            [
                'identifier' => 'hab_ia_products',
                'question'   => [
                    'fr' => "Avez-vous des produits assurance / placements sous le même toit?",
                    'en' => "Do you have insurance/investment products (possible discount)?",
                    'es' => "¿Tienes productos de seguros/inversiones (posible descuento)?",
                    'ht' => "Èske w gen pwodui asirans / envestisman (rabè posib)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 320,
            ],
            // --- Consentements communs ---
            [
                'identifier' => 'common_consent_profile',
                'question'   => [
                    'fr' => "Acceptez-vous le consentement de profilage?",
                    'en' => "Consent: collect/use/share certain information to know you better.",
                    'es' => "Consentimiento: recopilar/usar/compartir información para conocerle mejor.",
                    'ht' => "Konsantman: ranmase/itilize/pataje enfòmasyon pou pi byen konnen w.",
                ],
                'input_type' => 'consent',
                'sort_order' => 330,
            ],
            [
                'identifier' => 'common_consent_marketing',
                'question'   => [
                    'fr' => "Acceptez-vous de recevoir des communications marketing?",
                    'en' => "Consent: share promotions/products/services/events with you.",
                    'es' => "Consentimiento: compartir promociones/productos/servicios/eventos.",
                    'ht' => "Konsantman: pataje pwomosyon/pwodui/sèvis/evènman avèk ou.",
                ],
                'input_type' => 'consent',
                'sort_order' => 340,
            ],
            [
                'identifier' => 'common_marketing_email',
                'question'   => [
                    'fr' => "Souhaitez-vous recevoir le marketing par courriel?",
                    'en' => "Would you like to receive these communications by email?",
                    'es' => "¿Deseas recibir estas comunicaciones por correo electrónico?",
                    'ht' => "Èske ou ta renmen resevwa kominikasyon sa yo nan imèl?",
                ],
                'input_type' => 'consent',
                'sort_order' => 350,
            ],
            [
                'identifier' => 'hab_consent_credit',
                'question'   => [
                    'fr' => "Autorisez-vous une vérification de crédit si nécessaire?",
                    'en' => "Do you allow us to collect information from your credit file (no impact)?",
                    'es' => "¿Nos permite obtener información de su historial crediticio (sin impacto)?",
                    'ht' => "Èske w pèmèt nou pran enfòmasyon nan dosye kredi w (pa gen okenn enpak)?",
                ],
                'input_type' => 'consent',
                'sort_order' => 360,
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
