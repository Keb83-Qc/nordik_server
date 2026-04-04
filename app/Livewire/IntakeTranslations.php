<?php

namespace App\Livewire;

class IntakeTranslations
{
    protected static array $strings = [
        // Titres des étapes
        'step.identite'             => ['fr' => 'Vos informations',            'en' => 'Your information',           'es' => 'Su información',             'ht' => 'Enfòmasyon ou'],
        'step.adresse'              => ['fr' => 'Votre adresse',               'en' => 'Your address',               'es' => 'Su dirección',               'ht' => 'Adrès ou'],
        'step.famille'              => ['fr' => 'Situation familiale',         'en' => 'Family situation',           'es' => 'Situación familiar',         'ht' => 'Sitiyasyon fanmi'],
        'step.conjoint'             => ['fr' => 'Informations du conjoint',    'en' => 'Spouse information',         'es' => 'Información del cónyuge',    'ht' => 'Enfòmasyon konjwen'],
        'step.revenus'              => ['fr' => 'Revenus d\'emploi',           'en' => 'Employment income',          'es' => 'Ingresos de empleo',         'ht' => 'Revni travay'],
        'step.autres_revenus'       => ['fr' => 'Autres revenus',              'en' => 'Other income',               'es' => 'Otros ingresos',             'ht' => 'Lòt revni'],
        'step.epargne'              => ['fr' => 'Épargne et placements',       'en' => 'Savings & investments',      'es' => 'Ahorros e inversiones',      'ht' => 'Epay ak envestisman'],
        'step.actifs'               => ['fr' => 'Actifs',                      'en' => 'Assets',                     'es' => 'Activos',                    'ht' => 'Atik'],
        'step.dettes'               => ['fr' => 'Dettes et passifs',           'en' => 'Debts & liabilities',        'es' => 'Deudas y pasivos',           'ht' => 'Dèt ak pasif'],
        'step.assurance_vie'        => ['fr' => 'Assurance vie en vigueur',    'en' => 'Life insurance in force',    'es' => 'Seguro de vida vigente',     'ht' => 'Asirans vi aktyèl'],
        'step.assurance_invalidite' => ['fr' => 'Assurance invalidité',        'en' => 'Disability insurance',       'es' => 'Seguro de invalidez',        'ht' => 'Asirans andikap'],
        'step.assurance_mg'         => ['fr' => 'Assurance maladie grave',     'en' => 'Critical illness insurance', 'es' => 'Seguro de enfermedad grave', 'ht' => 'Asirans maladi grav'],
        'step.fonds_urgence'        => ['fr' => 'Fonds d\'urgence',            'en' => 'Emergency fund',             'es' => 'Fondo de emergencia',        'ht' => 'Fon ijans'],
        'step.retraite'             => ['fr' => 'Objectifs de retraite',       'en' => 'Retirement goals',           'es' => 'Objetivos de jubilación',    'ht' => 'Objektif retrèt'],
        'step.objectifs'            => ['fr' => 'Vos objectifs',               'en' => 'Your goals',                 'es' => 'Sus objetivos',              'ht' => 'Objektif ou yo'],
        'step.profil_investisseur'  => ['fr' => 'Profil d\'investisseur',      'en' => 'Investor profile',           'es' => 'Perfil de inversión',        'ht' => 'Pwofil envestisè'],

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

        // ── Autres revenus ────────────────────────────────────────────────────────
        'field.autre_revenu_type'         => ['fr' => 'Type de revenu',                   'en' => 'Income type',                    'es' => 'Tipo de ingreso',            'ht' => 'Kalite revni'],
        'field.montant_annuel'            => ['fr' => 'Montant annuel brut',              'en' => 'Annual gross amount',            'es' => 'Monto bruto anual',          'ht' => 'Montan anyèl brit'],
        'info.autres_revenus'             => ['fr' => 'Indiquez ici tout revenu autre qu\'un salaire d\'emploi (rentes, loyers, etc.). Laissez vide si aucun.', 'en' => 'Indicate any income other than employment income (annuities, rent, etc.). Leave blank if none.', 'es' => 'Indique cualquier ingreso que no sea salario (rentas, arrendamiento, etc.). Deje en blanco si no aplica.', 'ht' => 'Endike tout revni ki pa salè travay. Kite vid si pa gen.'],
        'revtype.rente_gouv'              => ['fr' => 'Rente gouvernementale (RRQ/RPC)',  'en' => 'Government pension (CPP/QPP)',   'es' => 'Pensión gubernamental',      'ht' => 'Rèt gouvènman'],
        'revtype.locatif'                 => ['fr' => 'Revenu locatif',                   'en' => 'Rental income',                  'es' => 'Ingreso por alquiler',       'ht' => 'Revni lokasyon'],
        'revtype.dividendes'              => ['fr' => 'Dividendes / intérêts',            'en' => 'Dividends / interest',           'es' => 'Dividendos / intereses',     'ht' => 'Dividann / enterè'],
        'revtype.pension'                 => ['fr' => 'Pension de retraite d\'employeur', 'en' => 'Employer pension',               'es' => 'Pensión de empleador',       'ht' => 'Pansyon travay'],
        'revtype.autonome'                => ['fr' => 'Travail autonome / entreprise',    'en' => 'Self-employment / business',     'es' => 'Trabajo autónomo / empresa', 'ht' => 'Travay endepandan'],
        'revtype.autre'                   => ['fr' => 'Autre',                            'en' => 'Other',                          'es' => 'Otro',                       'ht' => 'Lòt'],

        // ── Épargne ───────────────────────────────────────────────────────────────
        'info.epargne'                    => ['fr' => 'Indiquez les valeurs approximatives de vos autres comptes d\'épargne et placements.', 'en' => 'Enter the approximate values of your other savings accounts and investments.', 'es' => 'Indique los valores aproximados de sus otras cuentas de ahorro e inversiones.', 'ht' => 'Endike valè apwoksimatif lòt kont epay ou.'],
        'label.reer_ferr'                 => ['fr' => 'REER / FERR',                      'en' => 'RRSP / RRIF',                    'es' => 'RRSP / RRIF',                'ht' => 'REER / FERR'],
        'label.celiapp_pension'           => ['fr' => 'CELIAPP et fonds de pension',      'en' => 'FHSA and pension funds',         'es' => 'CELIAPP y fondos de pensión','ht' => 'CELIAPP ak fon pansyon'],
        'field.reer_conjoint'             => ['fr' => 'REER — conjoint(e) ($)',            'en' => 'RRSP — spouse ($)',              'es' => 'RRSP — cónyuge ($)',         'ht' => 'REER — konjwen ($)'],
        'field.ferr_client'               => ['fr' => 'FERR — vous ($)',                  'en' => 'RRIF — you ($)',                 'es' => 'RRIF — usted ($)',           'ht' => 'FERR — ou ($)'],
        'field.ferr_conjoint'             => ['fr' => 'FERR — conjoint(e) ($)',            'en' => 'RRIF — spouse ($)',              'es' => 'RRIF — cónyuge ($)',         'ht' => 'FERR — konjwen ($)'],
        'field.celiapp_client'            => ['fr' => 'CELIAPP — vous ($)',               'en' => 'FHSA — you ($)',                 'es' => 'FHSA — usted ($)',           'ht' => 'CELIAPP — ou ($)'],
        'field.celiapp_conjoint'          => ['fr' => 'CELIAPP — conjoint(e) ($)',        'en' => 'FHSA — spouse ($)',              'es' => 'FHSA — cónyuge ($)',         'ht' => 'CELIAPP — konjwen ($)'],
        'field.fonds_pension_client'      => ['fr' => 'Fonds de pension — vous ($)',      'en' => 'Pension fund — you ($)',         'es' => 'Fondo de pensión — usted ($)','ht' => 'Fon pansyon — ou ($)'],
        'field.fonds_pension_conjoint'    => ['fr' => 'Fonds de pension — conjoint(e) ($)','en' => 'Pension fund — spouse ($)',    'es' => 'Fondo de pensión — cónyuge ($)','ht' => 'Fon pansyon — konjwen ($)'],

        // ── Dettes ────────────────────────────────────────────────────────────────
        'info.dettes'                     => ['fr' => 'Indiquez le solde actuel de chaque type de dette. Laissez vide si vous n\'en avez pas.', 'en' => 'Enter the current balance of each debt type. Leave blank if none.', 'es' => 'Indique el saldo actual de cada tipo de deuda. Deje en blanco si no aplica.', 'ht' => 'Endike balans aktyèl chak dèt. Kite vid si pa genyen.'],
        'field.dette_hypotheque'          => ['fr' => 'Solde hypothèque ($)',              'en' => 'Mortgage balance ($)',           'es' => 'Saldo hipoteca ($)',          'ht' => 'Balans ipotèk ($)'],
        'field.dette_auto'                => ['fr' => 'Prêt auto ($)',                     'en' => 'Car loan ($)',                   'es' => 'Préstamo auto ($)',           'ht' => 'Prè machin ($)'],
        'field.dette_cartes'              => ['fr' => 'Total cartes de crédit ($)',        'en' => 'Total credit cards ($)',         'es' => 'Total tarjetas de crédito ($)','ht' => 'Total kat kredi ($)'],
        'field.dette_marge'               => ['fr' => 'Marge de crédit ($)',               'en' => 'Line of credit ($)',             'es' => 'Línea de crédito ($)',        'ht' => 'Mach de kredi ($)'],
        'field.dette_pret_perso'          => ['fr' => 'Prêts personnels ($)',              'en' => 'Personal loans ($)',             'es' => 'Préstamos personales ($)',    'ht' => 'Prè pèsonèl ($)'],
        'field.dette_autres'              => ['fr' => 'Autres dettes ($)',                 'en' => 'Other debts ($)',                'es' => 'Otras deudas ($)',            'ht' => 'Lòt dèt ($)'],

        // ── Assurances ────────────────────────────────────────────────────────────
        'info.assurance_vie'              => ['fr' => 'Indiquez les assurances vie actuellement en vigueur (non celles que vous souhaitez obtenir).', 'en' => 'Enter life insurance currently in force (not the coverage you wish to obtain).', 'es' => 'Indique los seguros de vida actualmente vigentes (no los que desea obtener).', 'ht' => 'Endike asirans vi ki an vigè kounye a.'],
        'info.assurance_invalidite'       => ['fr' => 'Indiquez votre assurance invalidité actuellement en vigueur.', 'en' => 'Enter your disability insurance currently in force.', 'es' => 'Indique su seguro de invalidez actualmente vigente.', 'ht' => 'Endike asirans andikap ki an vigè kounye a.'],
        'info.assurance_mg'               => ['fr' => 'Indiquez votre assurance maladie grave actuellement en vigueur.', 'en' => 'Enter your critical illness insurance currently in force.', 'es' => 'Indique su seguro de enfermedad grave actualmente vigente.', 'ht' => 'Endike asirans maladi grav ki an vigè kounye a.'],
        'field.ass_type'                  => ['fr' => 'Type de police',                   'en' => 'Policy type',                   'es' => 'Tipo de póliza',             'ht' => 'Kalite polis'],
        'field.ass_montant'               => ['fr' => 'Capital assuré ($)',               'en' => 'Coverage amount ($)',            'es' => 'Capital asegurado ($)',       'ht' => 'Kapital asirans ($)'],
        'field.ass_prime_annuelle'        => ['fr' => 'Prime annuelle ($)',               'en' => 'Annual premium ($)',             'es' => 'Prima anual ($)',             'ht' => 'Prim anyèl ($)'],
        'field.ass_prime_mensuelle'       => ['fr' => 'Prime mensuelle ($)',              'en' => 'Monthly premium ($)',            'es' => 'Prima mensual ($)',           'ht' => 'Prim mansyèl ($)'],
        'field.ass_inv_rente'             => ['fr' => 'Rente mensuelle assurée ($)',      'en' => 'Monthly benefit ($)',            'es' => 'Renta mensual asegurada ($)', 'ht' => 'Rèt mansyèl asirans ($)'],
        'asstype.t10'                     => ['fr' => 'Temporaire 10 ans',                'en' => 'Term 10',                       'es' => 'Temporal 10 años',           'ht' => 'Tanporè 10 an'],
        'asstype.t20'                     => ['fr' => 'Temporaire 20 ans',                'en' => 'Term 20',                       'es' => 'Temporal 20 años',           'ht' => 'Tanporè 20 an'],
        'asstype.t30'                     => ['fr' => 'Temporaire 30 ans',                'en' => 'Term 30',                       'es' => 'Temporal 30 años',           'ht' => 'Tanporè 30 an'],
        'asstype.permanente'              => ['fr' => 'Permanente entière',               'en' => 'Whole life',                    'es' => 'Vida entera',                'ht' => 'Lavi antye'],
        'asstype.universelle'             => ['fr' => 'Vie universelle',                  'en' => 'Universal life',                'es' => 'Vida universal',             'ht' => 'Lavi inivèsèl'],

        // ── Fonds d'urgence ───────────────────────────────────────────────────────
        'info.fonds_urgence'              => ['fr' => 'Indiquez le montant total actuellement disponible en épargne liquide (compte courant, épargne, etc.) pour les imprévus.', 'en' => 'Enter the total amount currently available in liquid savings (chequing, savings, etc.) for emergencies.', 'es' => 'Indique el monto total disponible en ahorros líquidos para emergencias.', 'ht' => 'Endike montan total ki disponib nan epay likid pou ijans.'],
        'field.fu_montant_actuel'         => ['fr' => 'Épargne liquide disponible ($)',   'en' => 'Available liquid savings ($)',   'es' => 'Ahorros líquidos disponibles ($)','ht' => 'Epay likid disponib ($)'],

        // ── Retraite ──────────────────────────────────────────────────────────────
        'info.retraite'                   => ['fr' => 'Indiquez le revenu mensuel net que vous souhaiteriez avoir à la retraite, ainsi que la rente mensuelle de votre régime d\'employeur si applicable.', 'en' => 'Enter the monthly net income you wish to have at retirement, and your employer pension monthly benefit if applicable.', 'es' => 'Indique el ingreso mensual neto que desea tener en la jubilación y la renta de su régimen de empleador si aplica.', 'ht' => 'Endike revni mansyèl nèt ou vle genyen nan retrèt, ak rèt mansyèl régim travay si aplikab.'],
        'field.rev_retraite_mensuel'      => ['fr' => 'Revenu mensuel net visé à la retraite — vous ($)', 'en' => 'Target monthly net retirement income — you ($)', 'es' => 'Ingreso mensual neto deseado en la jubilación — usted ($)', 'ht' => 'Revni mansyèl nèt vize pou retrèt — ou ($)'],
        'field.rev_retraite_conj_mensuel' => ['fr' => 'Revenu mensuel net visé — conjoint(e) ($)', 'en' => 'Target monthly net income — spouse ($)', 'es' => 'Ingreso mensual neto deseado — cónyuge ($)', 'ht' => 'Revni mansyèl nèt vize — konjwen ($)'],
        'field.regime_retraite_client'    => ['fr' => 'Rente mensuelle régime employeur — vous ($)', 'en' => 'Employer pension monthly benefit — you ($)', 'es' => 'Renta mensual régimen de empleador — usted ($)', 'ht' => 'Rèt mansyèl régim travay — ou ($)'],
        'field.regime_retraite_conjoint'  => ['fr' => 'Rente mensuelle régime employeur — conjoint(e) ($)', 'en' => 'Employer pension monthly benefit — spouse ($)', 'es' => 'Renta mensual régimen de empleador — cónyuge ($)', 'ht' => 'Rèt mansyèl régim travay — konjwen ($)'],

        // ── Profil d'investisseur ─────────────────────────────────────────────────
        'info.profil_investisseur'        => ['fr' => 'Ces informations aident votre conseiller à vous proposer une stratégie de placement adaptée à votre situation.', 'en' => 'This information helps your advisor propose an investment strategy tailored to your situation.', 'es' => 'Esta información ayuda a su asesor a proponer una estrategia de inversión adecuada.', 'ht' => 'Enfòmasyon sa yo ede konseyè ou pwpoze estrateji envestisman ki adapte.'],
        'field.profil_risque'             => ['fr' => 'Quelle est votre tolérance au risque?', 'en' => 'What is your risk tolerance?', 'es' => '¿Cuál es su tolerancia al riesgo?', 'ht' => 'Ki tolerans ou pou risk?'],
        'field.profil_horizon'            => ['fr' => 'Quel est votre horizon de placement?', 'en' => 'What is your investment horizon?', 'es' => '¿Cuál es su horizonte de inversión?', 'ht' => 'Ki orizon envestisman ou?'],
        'risque.prudent'                  => ['fr' => 'Prudent',    'en' => 'Conservative',  'es' => 'Prudente',    'ht' => 'Pridan'],
        'risque.modere'                   => ['fr' => 'Modéré',     'en' => 'Moderate',      'es' => 'Moderado',    'ht' => 'Modere'],
        'risque.equilibre'                => ['fr' => 'Équilibré',  'en' => 'Balanced',      'es' => 'Equilibrado', 'ht' => 'Balanse'],
        'risque.croissance'               => ['fr' => 'Croissance', 'en' => 'Growth',        'es' => 'Crecimiento', 'ht' => 'Kwasans'],
        'risque.audacieux'                => ['fr' => 'Audacieux',  'en' => 'Aggressive',    'es' => 'Agresivo',    'ht' => 'Aydasyè'],
        'horizon.court'                   => ['fr' => 'Court terme (< 3 ans)',  'en' => 'Short term (< 3 yrs)',  'es' => 'Corto plazo (< 3 años)',  'ht' => 'Kout tèm (< 3 an)'],
        'horizon.moyen'                   => ['fr' => 'Moyen terme (3–10 ans)', 'en' => 'Medium term (3–10 yrs)','es' => 'Mediano plazo (3–10 años)','ht' => 'Mwayèn tèm (3–10 an)'],
        'horizon.long'                    => ['fr' => 'Long terme (10+ ans)',   'en' => 'Long term (10+ yrs)',   'es' => 'Largo plazo (10+ años)',   'ht' => 'Long tèm (10+ an)'],

        // ── Labels génériques ─────────────────────────────────────────────────────
        'label.vous'    => ['fr' => 'Vous',        'en' => 'You',      'es' => 'Usted',    'ht' => 'Ou'],
        'label.conjoint'=> ['fr' => 'Conjoint(e)', 'en' => 'Spouse',   'es' => 'Cónyuge',  'ht' => 'Konjwen'],
        'label.choisir' => ['fr' => 'Choisir',     'en' => 'Choose',   'es' => 'Elegir',   'ht' => 'Chwazi'],

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
