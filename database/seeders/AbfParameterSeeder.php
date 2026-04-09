<?php

namespace Database\Seeders;

use App\Models\AbfParameter;
use Illuminate\Database\Seeder;

class AbfParameterSeeder extends Seeder
{
    public function run(): void
    {
        $params = [

            // ── Hypothèses générales ─────────────────────────────────────
            ['group' => 'hypotheses', 'key' => 'inflation',      'label' => 'Taux d\'inflation',                    'type' => 'percent', 'value' => '2.10', 'sort_order' => 1, 'description' => 'Taux d\'inflation annuel par défaut (%)'],
            ['group' => 'hypotheses', 'key' => 'ev_client',      'label' => 'Espérance de vie — Client',            'type' => 'number',  'value' => '94',   'sort_order' => 2, 'description' => 'Âge de décès estimé par défaut pour le client'],
            ['group' => 'hypotheses', 'key' => 'ev_conjoint',    'label' => 'Espérance de vie — Conjoint(e)',       'type' => 'number',  'value' => '96',   'sort_order' => 3, 'description' => 'Âge de décès estimé par défaut pour le conjoint'],

            // ── Rendements portefeuilles ─────────────────────────────────
            ['group' => 'portefeuilles', 'key' => 'prudent',     'label' => 'Rendement — Prudent',                 'type' => 'percent', 'value' => '3.00', 'sort_order' => 1, 'description' => 'Taux de rendement réel annuel pour profil Prudent (%)'],
            ['group' => 'portefeuilles', 'key' => 'modere',      'label' => 'Rendement — Modéré',                  'type' => 'percent', 'value' => '3.30', 'sort_order' => 2, 'description' => 'Taux de rendement réel annuel pour profil Modéré (%)'],
            ['group' => 'portefeuilles', 'key' => 'equilibre',   'label' => 'Rendement — Équilibré',               'type' => 'percent', 'value' => '3.70', 'sort_order' => 3, 'description' => 'Taux de rendement réel annuel pour profil Équilibré (%)'],
            ['group' => 'portefeuilles', 'key' => 'croissance',  'label' => 'Rendement — Croissance',              'type' => 'percent', 'value' => '4.00', 'sort_order' => 4, 'description' => 'Taux de rendement réel annuel pour profil Croissance (%)'],
            ['group' => 'portefeuilles', 'key' => 'audacieux',   'label' => 'Rendement — Audacieux',               'type' => 'percent', 'value' => '4.30', 'sort_order' => 5, 'description' => 'Taux de rendement réel annuel pour profil Audacieux (%)'],

            // ── Décès ────────────────────────────────────────────────────
            ['group' => 'deces', 'key' => 'funerailles',         'label' => 'Frais funéraires par défaut',         'type' => 'number',  'value' => '10000', 'sort_order' => 1, 'description' => 'Montant estimé des frais funéraires ($)'],
            ['group' => 'deces', 'key' => 'rr_pct',              'label' => 'Remplacement du revenu — Décès (%)',  'type' => 'percent', 'value' => '70',    'sort_order' => 2, 'description' => 'Pourcentage du revenu à remplacer après un décès (%)'],
            ['group' => 'deces', 'key' => 'rr_type',             'label' => 'Type de remplacement revenu',         'type' => 'select',  'value' => 'family','sort_order' => 3, 'description' => 'Calcul sur revenu familial ou individuel', 'options' => ['family' => 'Familial', 'individual' => 'Individuel']],
            ['group' => 'deces', 'key' => 'salaire_type',        'label' => 'Salaire brut ou net',                 'type' => 'select',  'value' => 'gross', 'sort_order' => 4, 'description' => 'Base de calcul du revenu', 'options' => ['gross' => 'Brut', 'net' => 'Net']],
            ['group' => 'deces', 'key' => 'frequence',           'label' => 'Fréquence de revenu',                 'type' => 'select',  'value' => 'yearly','sort_order' => 5, 'description' => 'Fréquence du revenu saisi', 'options' => ['yearly' => 'Annuel', 'monthly' => 'Mensuel']],

            // ── Invalidité ───────────────────────────────────────────────
            ['group' => 'invalidite', 'key' => 'type',           'label' => 'Méthode de calcul — Invalidité',     'type' => 'select',  'value' => 'incomeReplacement', 'sort_order' => 1, 'description' => 'Base de calcul pour l\'invalidité', 'options' => ['incomeReplacement' => 'Remplacement du revenu', 'expensesCoverage' => 'Dépenses courantes']],
            ['group' => 'invalidite', 'key' => 'salaire_type',   'label' => 'Salaire brut ou net — Invalidité',   'type' => 'select',  'value' => 'gross',              'sort_order' => 2, 'description' => 'Base de calcul du salaire', 'options' => ['gross' => 'Brut', 'net' => 'Net']],
            ['group' => 'invalidite', 'key' => 'rr_pct',         'label' => 'Remplacement du revenu — Invalidité (%)', 'type' => 'percent', 'value' => '70',          'sort_order' => 3, 'description' => 'Pourcentage du revenu à remplacer en cas d\'invalidité (%)'],

            // ── Maladie grave ────────────────────────────────────────────
            ['group' => 'maladie_grave', 'key' => 'niveau',      'label' => 'Niveau de protection — Maladie grave','type' => 'select', 'value' => 'comfort', 'sort_order' => 1, 'description' => 'Niveau de confort financier visé', 'options' => ['none' => 'Aucun', 'base' => 'Base', 'comfort' => 'Confort', 'premium' => 'Supérieur']],

            // ── Retraite ─────────────────────────────────────────────────
            ['group' => 'retraite', 'key' => 'rr_pct',           'label' => 'Remplacement du revenu — Retraite (%)', 'type' => 'percent', 'value' => '70',  'sort_order' => 1, 'description' => 'Pourcentage du revenu à remplacer à la retraite (%)'],
            ['group' => 'retraite', 'key' => 'frequence',        'label' => 'Fréquence de revenu — Retraite',     'type' => 'select',  'value' => 'yearly', 'sort_order' => 2, 'description' => 'Fréquence du revenu saisi pour la retraite', 'options' => ['yearly' => 'Annuel', 'monthly' => 'Mensuel']],
            ['group' => 'retraite', 'key' => 'calcul',           'label' => 'Méthode de calcul — Retraite',       'type' => 'select',  'value' => 'average','sort_order' => 3, 'description' => 'Mode de calcul du revenu de retraite', 'options' => ['average' => 'Moyenne', 'total' => 'Total']],

            // ── Fonds d\'urgence ─────────────────────────────────────────
            ['group' => 'fonds_urgence', 'key' => 'type',        'label' => 'Base de calcul — Fonds d\'urgence',  'type' => 'select',  'value' => 'income', 'sort_order' => 1, 'description' => 'Comment calculer le fonds d\'urgence', 'options' => ['income' => 'Revenu mensuel', 'expenses' => 'Dépenses mensuelles', 'amount' => 'Montant fixe', 'none' => 'Aucun']],
            ['group' => 'fonds_urgence', 'key' => 'mois',        'label' => 'Nombre de mois — Fonds d\'urgence',  'type' => 'number',  'value' => '3',      'sort_order' => 2, 'description' => 'Nombre de mois de réserve recommandé'],

            // ── RRQ / RPC ────────────────────────────────────────────────
            ['group' => 'rrq', 'key' => 'rente_45_sans_conjoint','label' => 'RRQ — Rente 45 ans sans conjoint',   'type' => 'number',  'value' => '719.50',  'sort_order' => 1, 'description' => 'Montant mensuel estimé de la rente RRQ à 45 ans sans conjoint survivant ($)'],
            ['group' => 'rrq', 'key' => 'rente_45_avec_conjoint','label' => 'RRQ — Rente 45 ans avec conjoint',  'type' => 'number',  'value' => '1129.95', 'sort_order' => 2, 'description' => 'Montant mensuel estimé de la rente RRQ à 45 ans avec conjoint survivant ($)'],
            ['group' => 'rrq', 'key' => 'rente_45_invalidite',   'label' => 'RRQ — Rente invalidité 45 ans',     'type' => 'number',  'value' => '1134.61', 'sort_order' => 3, 'description' => 'Montant mensuel estimé de la rente d\'invalidité RRQ à 45 ans ($)'],
            ['group' => 'rrq', 'key' => 'cpp_fixe',              'label' => 'CPP — Prestation de décès fixe',    'type' => 'number',  'value' => '217.83',  'sort_order' => 4, 'description' => 'Montant fixe de la prestation de décès CPP/RPC ($)'],
            ['group' => 'rrq', 'key' => 'prestation_deces',      'label' => 'RRQ/RPC — Prestation de décès',     'type' => 'number',  'value' => '2500',    'sort_order' => 5, 'description' => 'Estimation de la prestation de décès RRQ/RPC ($)'],
            ['group' => 'rrq', 'key' => 'mga',                   'label' => 'RRQ — Maximum des gains admissibles (MGA)', 'type' => 'number', 'value' => '71300',  'sort_order' => 6, 'description' => 'Maximum des gains admissibles RRQ/QPP ($) — utilisé pour le calcul automatique de la rente RRQ en section Retraite'],
            ['group' => 'rrq', 'key' => 'rente_max_65',          'label' => 'RRQ — Rente maximale mensuelle à 65 ans',   'type' => 'number', 'value' => '1433.00','sort_order' => 7, 'description' => 'Rente mensuelle maximale RRQ/QPP à 65 ans ($) — utilisée pour le calcul automatique en section Retraite'],
            ['group' => 'rrq', 'key' => 'sv_mensuel_65',         'label' => 'SV — Prestation mensuelle 65 à 74 ans',     'type' => 'number', 'value' => '727.67', 'sort_order' => 8, 'description' => 'Montant mensuel de la Sécurité de la vieillesse pour 65-74 ans ($)'],
            ['group' => 'rrq', 'key' => 'sv_mensuel_75',         'label' => 'SV — Prestation mensuelle 75 ans et +',     'type' => 'number', 'value' => '800.44', 'sort_order' => 9, 'description' => 'Montant mensuel de la Sécurité de la vieillesse pour 75 ans et plus ($)'],

            // ── ABF général ──────────────────────────────────────────────
            ['group' => 'abf', 'key' => 'default_return_rate',       'label' => 'Taux de rendement réel par défaut (%)',    'type' => 'percent', 'value' => '5',  'sort_order' => 1, 'description' => 'Taux de rendement réel utilisé dans la section D du budget décès'],
            ['group' => 'abf', 'key' => 'default_income_replacement_years', 'label' => 'Durée de remplacement du revenu par défaut', 'type' => 'number', 'value' => '20', 'sort_order' => 2, 'description' => 'Nombre d\'années de remplacement du revenu par défaut (section D)'],
        ];

        foreach ($params as $param) {
            $options = $param['options'] ?? null;
            unset($param['options']);

            AbfParameter::updateOrCreate(
                ['group' => $param['group'], 'key' => $param['key']],
                array_merge($param, ['options' => $options])
            );
        }
    }
}
