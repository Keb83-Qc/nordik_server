<?php

return [
    // Estimation simple pour "Prestations de décès RRQ/RPC" (modifiable plus tard par province/règle métier)
    'rrq_rpc_death_benefit_amount' => env('ABF_RRQ_RPC_DEATH_BENEFIT_AMOUNT', 2500),

    // Hypothèses par défaut pour la section D (modifiables par le conseiller dans le wizard)
    'default_real_return_rate_percent' => env('ABF_DEFAULT_REAL_RETURN_RATE_PERCENT', 5),
    'default_income_replacement_years' => env('ABF_DEFAULT_INCOME_REPLACEMENT_YEARS', 20),
];
