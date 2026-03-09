<?php

namespace App\Domain\QuoteBundle;

final class StepValidation
{
    public static function rules(string $step): array
    {
        return match ($step) {
            'common_identity' => [
                'first_name' => 'required|string|min:2|max:60',
                'last_name'  => 'required|string|min:2|max:60',
                'gender'     => 'required|string|in:homme,femme,autre,prefer_not',
            ],
            'common_age' => ['age' => 'required|integer|min:16|max:120'],
            'common_email' => ['email' => 'required|email|max:160'],
            'common_phone' => ['phone' => 'required|string|min:10|max:30'],

            'auto_renewal_date' => ['renewal_date' => 'required|date'],
            'auto_license_number' => ['license_number' => 'nullable|string|max:60'],

            'hab_renewal_date' => ['hab_renewal_date' => 'required|date'],
            'hab_address' => ['address' => 'required|string|min:5|max:200'],
            'hab_move_in_date' => ['move_in_date' => 'required|date'],
            'hab_units_in_building' => ['units_in_building' => 'required|integer|min:1|max:999'],
            'hab_contents_amount' => ['contents_amount' => 'required|integer|min:0|max:2000000'],
            'hab_years_with_insurer' => ['years_with_insurer' => 'required|integer|min:0|max:100'],
            'hab_current_insurer' => ['current_insurer' => 'required|string|min:2|max:120'],
            'hab_industry' => ['industry' => 'required|string|min:2|max:120'],

            default => [],
        };
    }
}
