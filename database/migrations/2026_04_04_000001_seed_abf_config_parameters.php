<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Group: celi ────────────────────────────────────────────────
        DB::table('abf_parameters')->updateOrInsert(
            ['group' => 'celi', 'key' => 'plafonds'],
            [
                'label'      => 'Plafonds annuels CELI',
                'type'       => 'json',
                'value'      => '[{"annee":2009,"montant":5000},{"annee":2010,"montant":5000},{"annee":2011,"montant":5000},{"annee":2012,"montant":5000},{"annee":2013,"montant":5500},{"annee":2014,"montant":5500},{"annee":2015,"montant":10000},{"annee":2016,"montant":5500},{"annee":2017,"montant":5500},{"annee":2018,"montant":5500},{"annee":2019,"montant":6000},{"annee":2020,"montant":6000},{"annee":2021,"montant":6000},{"annee":2022,"montant":6000},{"annee":2023,"montant":6500},{"annee":2024,"montant":7000},{"annee":2025,"montant":7000},{"annee":2026,"montant":7000}]',
                'sort_order' => 1,
            ]
        );

        // ── Group: sv ─────────────────────────────────────────────────
        $svParams = [
            ['key' => 'max_mensuel_65_74',  'label' => 'Max mensuel SV 65-74 ans',            'type' => 'number',  'value' => '727.67',  'sort_order' => 1],
            ['key' => 'max_mensuel_75_plus', 'label' => 'Max mensuel SV 75 ans et plus',       'type' => 'number',  'value' => '800.44',  'sort_order' => 2],
            ['key' => 'srg_seul_max',        'label' => 'SRG maximum — personne seule',         'type' => 'number',  'value' => '1086.88', 'sort_order' => 3],
            ['key' => 'srg_couple_max',      'label' => 'SRG maximum — conjoint',               'type' => 'number',  'value' => '654.23',  'sort_order' => 4],
            ['key' => 'seuil_recuperation',  'label' => 'Seuil de récupération SV',             'type' => 'number',  'value' => '93454',   'sort_order' => 5],
            ['key' => 'taux_recuperation',   'label' => 'Taux de récupération SV (%)',          'type' => 'percent', 'value' => '15',      'sort_order' => 6],
            ['key' => 'bonification_report', 'label' => 'Bonification par mois de report SV (%)', 'type' => 'percent', 'value' => '0.6',  'sort_order' => 7],
        ];

        foreach ($svParams as $param) {
            DB::table('abf_parameters')->updateOrInsert(
                ['group' => 'sv', 'key' => $param['key']],
                [
                    'label'      => $param['label'],
                    'type'       => $param['type'],
                    'value'      => $param['value'],
                    'sort_order' => $param['sort_order'],
                ]
            );
        }

        // ── Group: rente_orphelin ─────────────────────────────────────
        $orphelinParams = [
            ['key' => 'rrq_simple', 'label' => "Orphelin d'un parent",         'type' => 'number', 'value' => '290.00',  'sort_order' => 1],
            ['key' => 'rrq_double', 'label' => 'Orphelin des deux parents',     'type' => 'number', 'value' => '728.00',  'sort_order' => 2],
            ['key' => 'cpp',        'label' => 'CPP/RPC orphelin',              'type' => 'number', 'value' => '294.12',  'sort_order' => 3],
        ];

        foreach ($orphelinParams as $param) {
            DB::table('abf_parameters')->updateOrInsert(
                ['group' => 'rente_orphelin', 'key' => $param['key']],
                [
                    'label'      => $param['label'],
                    'type'       => $param['type'],
                    'value'      => $param['value'],
                    'sort_order' => $param['sort_order'],
                ]
            );
        }

        // ── Group: fiscalite ──────────────────────────────────────────
        $fiscaliteParams = [
            ['key' => 'brackets_fed',        'label' => 'Paliers fédéraux',                  'type' => 'json',    'value' => '[{"max":58523,"rate":14},{"max":117045,"rate":20.5},{"max":181440,"rate":26},{"max":258482,"rate":29},{"max":null,"rate":33}]', 'sort_order' => 1],
            ['key' => 'brackets_qc',         'label' => 'Paliers Québec',                    'type' => 'json',    'value' => '[{"max":54345,"rate":14},{"max":108680,"rate":19},{"max":132245,"rate":24},{"max":null,"rate":25.75}]', 'sort_order' => 2],
            ['key' => 'fed_base_max',        'label' => 'Montant personnel de base fédéral max', 'type' => 'number', 'value' => '16452',   'sort_order' => 3],
            ['key' => 'fed_base_min',        'label' => 'Montant personnel de base fédéral min', 'type' => 'number', 'value' => '14829',   'sort_order' => 4],
            ['key' => 'fed_base_seuil_bas',  'label' => 'Seuil bas montant de base fédéral',    'type' => 'number', 'value' => '173205',  'sort_order' => 5],
            ['key' => 'fed_base_seuil_haut', 'label' => 'Seuil haut montant de base fédéral',   'type' => 'number', 'value' => '235675',  'sort_order' => 6],
            ['key' => 'fed_credit_rate',     'label' => 'Taux du crédit fédéral (%)',            'type' => 'percent', 'value' => '15',     'sort_order' => 7],
            ['key' => 'qc_base',             'label' => 'Montant personnel de base Québec',      'type' => 'number', 'value' => '18952',   'sort_order' => 8],
            ['key' => 'qc_credit_rate',      'label' => 'Taux du crédit Québec (%)',             'type' => 'percent', 'value' => '14',     'sort_order' => 9],
            ['key' => 'rrq_exemption',       'label' => 'Exemption de base RRQ ($)',             'type' => 'number', 'value' => '3500',    'sort_order' => 10],
            ['key' => 'rrq_ceil1',           'label' => 'Plafond cotisable RRQ 1 ($)',           'type' => 'number', 'value' => '74600',   'sort_order' => 11],
            ['key' => 'rrq_rate1',           'label' => 'Taux cotisation RRQ 1 (%)',             'type' => 'percent', 'value' => '5.4',    'sort_order' => 12],
            ['key' => 'rrq_ceil2',           'label' => 'Plafond cotisable RRQ 2 ($)',           'type' => 'number', 'value' => '85000',   'sort_order' => 13],
            ['key' => 'rrq_rate2',           'label' => 'Taux cotisation RRQ 2 (%)',             'type' => 'percent', 'value' => '1.0',    'sort_order' => 14],
            ['key' => 'ae_ceil',             'label' => "Plafond cotisable AE (\$)",             'type' => 'number', 'value' => '68900',   'sort_order' => 15],
            ['key' => 'ae_rate',             'label' => 'Taux cotisation AE (%)',                'type' => 'percent', 'value' => '1.30',   'sort_order' => 16],
            ['key' => 'rqap_ceil',           'label' => 'Plafond cotisable RQAP ($)',            'type' => 'number', 'value' => '103000',  'sort_order' => 17],
            ['key' => 'rqap_rate',           'label' => 'Taux cotisation RQAP (%)',              'type' => 'percent', 'value' => '0.430',  'sort_order' => 18],
        ];

        foreach ($fiscaliteParams as $param) {
            DB::table('abf_parameters')->updateOrInsert(
                ['group' => 'fiscalite', 'key' => $param['key']],
                [
                    'label'      => $param['label'],
                    'type'       => $param['type'],
                    'value'      => $param['value'],
                    'sort_order' => $param['sort_order'],
                ]
            );
        }

        // ── Group: abf — province_defaut ──────────────────────────────
        DB::table('abf_parameters')->updateOrInsert(
            ['group' => 'abf', 'key' => 'province_defaut'],
            [
                'label'      => 'Province par défaut',
                'type'       => 'text',
                'value'      => 'Québec',
                'sort_order' => 3,
            ]
        );
    }

    public function down(): void
    {
        // Data migration only — no rollback
    }
};
