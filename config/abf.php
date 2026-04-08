<?php

return [

    // ── RRQ/QPP — Prestation de décès ────────────────────────────────────────
    // Montant forfaitaire one-time (plafond légal — inchangé depuis plusieurs années)
    'rrq_rpc_death_benefit_amount' => env('ABF_RRQ_RPC_DEATH_BENEFIT_AMOUNT', 2500),

    // ── RRQ/QPP — Rente de conjoint survivant 2025 ───────────────────────────
    'rrq_survivor_min_monthly'     => env('ABF_RRQ_SURVIVOR_MIN_MONTHLY', 472.00),
    'rrq_survivor_max_monthly'     => env('ABF_RRQ_SURVIVOR_MAX_MONTHLY', 1173.58),

    // ── RRQ/QPP — Rente d'orphelin 2025 ─────────────────────────────────────
    // Montant mensuel par enfant éligible (< 18 ans, ou < 25 ans si étudiant)
    'rrq_orphan_monthly'           => env('ABF_RRQ_ORPHAN_MONTHLY', 282.79),

    // ── SV/OAS — Sécurité de la vieillesse 2025 ──────────────────────────────
    // Indexé trimestriellement — mettre à jour chaque janvier
    'oas_monthly_65_74'            => env('ABF_OAS_MONTHLY_65_74', 727.67),
    'oas_monthly_75_plus'          => env('ABF_OAS_MONTHLY_75_PLUS', 800.44),

    // ── Hypothèses de calcul — Section D (Budget au décès) ───────────────────
    'default_real_return_rate_percent'  => env('ABF_DEFAULT_REAL_RETURN_RATE_PERCENT', 5),
    'default_income_replacement_years'  => env('ABF_DEFAULT_INCOME_REPLACEMENT_YEARS', 20),

];
