<?php

namespace App\Livewire;

class IntakeTranslations
{
    protected static array $strings = [
        // Titres des étapes
        'step.identite'   => ['fr' => 'Vos informations',        'en' => 'Your information',         'es' => 'Su información',          'ht' => 'Enfòmasyon ou'],
        'step.adresse'    => ['fr' => 'Votre adresse',           'en' => 'Your address',             'es' => 'Su dirección',            'ht' => 'Adrès ou'],
        'step.famille'    => ['fr' => 'Situation familiale',     'en' => 'Family situation',         'es' => 'Situación familiar',      'ht' => 'Sitiyasyon fanmi'],
        'step.conjoint'   => ['fr' => 'Informations du conjoint','en' => 'Spouse information',       'es' => 'Información del cónyuge', 'ht' => 'Enfòmasyon konjwen'],
        'step.revenus'    => ['fr' => 'Revenus annuels',         'en' => 'Annual income',            'es' => 'Ingresos anuales',        'ht' => 'Revni anyèl'],
        'step.actifs'     => ['fr' => 'Vos actifs',              'en' => 'Your assets',              'es' => 'Sus activos',             'ht' => 'Atik ou yo'],
        'step.objectifs'  => ['fr' => 'Vos objectifs',           'en' => 'Your goals',               'es' => 'Sus objetivos',           'ht' => 'Objektif ou yo'],

        // Navigation
        'nav.next'    => ['fr' => 'Suivant',   'en' => 'Next',   'es' => 'Siguiente', 'ht' => 'Swivan'],
        'nav.prev'    => ['fr' => 'Précédent', 'en' => 'Previous','es' => 'Anterior',  'ht' => 'Anvan'],
        'nav.submit'  => ['fr' => 'Envoyer mon profil', 'en' => 'Submit my profile', 'es' => 'Enviar mi perfil', 'ht' => 'Voye pwofil mwen'],
        'nav.step_of' => ['fr' => 'Étape %d sur %d', 'en' => 'Step %d of %d', 'es' => 'Paso %d de %d', 'ht' => 'Etap %d sou %d'],

        // Champs identité
        'field.prenom'     => ['fr' => 'Prénom',                     'en' => 'First name',           'es' => 'Nombre',                  'ht' => 'Prenon'],
        'field.nom'        => ['fr' => 'Nom de famille',             'en' => 'Last name',            'es' => 'Apellido',                'ht' => 'Non fanmi'],
        'field.sexe'       => ['fr' => 'Genre',                      'en' => 'Gender',               'es' => 'Género',                  'ht' => 'Sèks'],
        'field.ddn'        => ['fr' => 'Date de naissance',          'en' => 'Date of birth',        'es' => 'Fecha de nacimiento',     'ht' => 'Dat nesans'],
        'field.courriel'   => ['fr' => 'Adresse courriel',           'en' => 'Email address',        'es' => 'Correo electrónico',      'ht' => 'Adrès imèl'],
        'field.cellulaire' => ['fr' => 'Numéro de cellulaire',       'en' => 'Cell phone number',    'es' => 'Número de celular',       'ht' => 'Nimewo selilè'],

        // Champs adresse
        'field.addr_civique'  => ['fr' => 'Numéro civique',  'en' => 'Street number', 'es' => 'Número de calle', 'ht' => 'Nimewo sivik'],
        'field.addr_rue'      => ['fr' => 'Rue',              'en' => 'Street',        'es' => 'Calle',           'ht' => 'Ri'],
        'field.addr_ville'    => ['fr' => 'Ville',            'en' => 'City',          'es' => 'Ciudad',          'ht' => 'Vil'],
        'field.addr_province' => ['fr' => 'Province',         'en' => 'Province',      'es' => 'Provincia',       'ht' => 'Pwovens'],
        'field.addr_postal'   => ['fr' => 'Code postal',      'en' => 'Postal code',   'es' => 'Código postal',   'ht' => 'Kòd postal'],

        // Situation familiale
        'field.etat_civil'   => ['fr' => 'État civil',         'en' => 'Marital status',     'es' => 'Estado civil',        'ht' => 'Eta sivil'],
        'field.nb_enfants'   => ['fr' => 'Nombre d\'enfants à charge', 'en' => 'Number of dependent children', 'es' => 'Número de hijos a cargo', 'ht' => 'Kantite timoun'],
        'etat.celibataire'   => ['fr' => 'Célibataire',        'en' => 'Single',             'es' => 'Soltero/a',           'ht' => 'Selibatè'],
        'etat.marie'         => ['fr' => 'Marié(e)',           'en' => 'Married',            'es' => 'Casado/a',            'ht' => 'Marye'],
        'etat.conjoint_fait' => ['fr' => 'Conjoint(e) de fait','en' => 'Common-law',         'es' => 'Unión libre',         'ht' => 'Konjwen'],
        'etat.separe'        => ['fr' => 'Séparé(e)',          'en' => 'Separated',          'es' => 'Separado/a',          'ht' => 'Separe'],
        'etat.divorce'       => ['fr' => 'Divorcé(e)',         'en' => 'Divorced',           'es' => 'Divorciado/a',        'ht' => 'Divòse'],
        'etat.veuf'          => ['fr' => 'Veuf / Veuve',       'en' => 'Widowed',            'es' => 'Viudo/a',             'ht' => 'Vèf / Vèv'],

        // Genres
        'sexe.homme' => ['fr' => 'Homme', 'en' => 'Male',   'es' => 'Hombre', 'ht' => 'Gason'],
        'sexe.femme' => ['fr' => 'Femme', 'en' => 'Female', 'es' => 'Mujer',  'ht' => 'Fanm'],

        // Revenus
        'field.revenu_client'   => ['fr' => 'Revenu brut annuel (vous)',     'en' => 'Annual gross income (you)',     'es' => 'Ingreso bruto anual (usted)',  'ht' => 'Revni brit anyèl (ou)'],
        'field.revenu_conjoint' => ['fr' => 'Revenu brut annuel (conjoint)', 'en' => 'Annual gross income (spouse)', 'es' => 'Ingreso bruto anual (cónyuge)','ht' => 'Revni brit anyèl (konjwen)'],

        // Actifs
        'field.a_propriete'      => ['fr' => 'Êtes-vous propriétaire?',              'en' => 'Do you own property?',           'es' => '¿Es propietario/a?',          'ht' => 'Eske ou pwopriyetè?'],
        'field.valeur_propriete' => ['fr' => 'Valeur estimée de la résidence ($)',   'en' => 'Estimated property value ($)',   'es' => 'Valor estimado de la propiedad ($)', 'ht' => 'Valè estimé kay la ($)'],
        'field.valeur_reer'      => ['fr' => 'Valeur REER ($)',                      'en' => 'RRSP value ($)',                 'es' => 'Valor RRSP ($)',              'ht' => 'Valè REER ($)'],
        'field.valeur_celi'      => ['fr' => 'Valeur CELI ($)',                      'en' => 'TFSA value ($)',                 'es' => 'Valor TFSA ($)',              'ht' => 'Valè CELI ($)'],
        'field.valeur_placements'=> ['fr' => 'Autres placements ($)',                'en' => 'Other investments ($)',          'es' => 'Otras inversiones ($)',       'ht' => 'Lòt envestisman ($)'],

        // Objectifs
        'field.age_retraite'         => ['fr' => 'Âge de retraite visé (vous)',    'en' => 'Target retirement age (you)',    'es' => 'Edad de jubilación deseada (usted)', 'ht' => 'Laj retrèt vize (ou)'],
        'field.age_retraite_conjoint'=> ['fr' => 'Âge de retraite visé (conjoint)','en' => 'Target retirement age (spouse)','es' => 'Edad de jubilación deseada (cónyuge)','ht' => 'Laj retrèt vize (konjwen)'],
        'field.objectifs_texte'      => ['fr' => 'Vos objectifs financiers (en quelques mots)', 'en' => 'Your financial goals (briefly)', 'es' => 'Sus objetivos financieros (brevemente)', 'ht' => 'Objektif finansye ou yo (kèk mo)'],

        // Placeholders
        'ph.prenom'     => ['fr' => 'Jean',       'en' => 'John',    'es' => 'Juan',   'ht' => 'Jan'],
        'ph.nom'        => ['fr' => 'Dupont',      'en' => 'Smith',   'es' => 'García', 'ht' => 'Dipon'],
        'ph.courriel'   => ['fr' => 'jean@example.com', 'en' => 'john@example.com', 'es' => 'juan@ejemplo.com', 'ht' => 'jan@egzanp.com'],
        'ph.cellulaire' => ['fr' => '514 000-0000','en' => '514 000-0000','es' => '514 000-0000','ht' => '514 000-0000'],
        'ph.objectifs'  => [
            'fr' => 'Ex: Prendre ma retraite à 60 ans, financer les études de mes enfants, réduire mes dettes...',
            'en' => 'E.g. Retire at 60, fund my children\'s education, reduce my debts...',
            'es' => 'Ej: Jubilarme a los 60, financiar la educación de mis hijos, reducir mis deudas...',
            'ht' => 'Eg: Pran retrèt nan 60 an, finanse etid pitit mwen, redwi dèt mwen...',
        ],

        // Autre
        'yes' => ['fr' => 'Oui', 'en' => 'Yes', 'es' => 'Sí', 'ht' => 'Wi'],
        'no'  => ['fr' => 'Non', 'en' => 'No',  'es' => 'No', 'ht' => 'Non'],
        'optional' => ['fr' => '(optionnel)', 'en' => '(optional)', 'es' => '(opcional)', 'ht' => '(opsyonèl)'],
    ];

    public static function get(string $key, string $locale = 'fr'): string
    {
        $entry = static::$strings[$key] ?? null;
        if (!$entry) return $key;
        return $entry[$locale] ?? $entry['fr'] ?? $key;
    }
}
