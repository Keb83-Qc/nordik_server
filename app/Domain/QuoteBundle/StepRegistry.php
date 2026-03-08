<?php

namespace App\Domain\QuoteBundle;

final class StepRegistry
{
    public static function common(): array
    {
        return [
            'common_identity' => ['bucket' => 'common', 'required' => ['first_name','last_name','gender']],
            'common_age'      => ['bucket' => 'common', 'required' => 'age'],
            'common_email'    => ['bucket' => 'common', 'required' => 'email'],
            'common_phone'    => ['bucket' => 'common', 'required' => 'phone'],
        ];
    }

    public static function profile(): array
    {
        return [
            'hab_marital_status'     => ['bucket' => 'habitation', 'required' => 'marital_status'],
            'hab_employment_status'  => ['bucket' => 'habitation', 'required' => 'employment_status'],
            'hab_education_level'    => ['bucket' => 'habitation', 'required' => 'education_level'],
            'hab_industry'           => ['bucket' => 'habitation', 'required' => 'industry'],
            'hab_has_ia_products'    => ['bucket' => 'habitation', 'required' => 'has_ia_products'],
        ];
    }

    public static function auto(): array
    {
        return [
            'auto_year'              => ['bucket' => 'auto', 'required' => 'year'],
            'auto_brand'             => ['bucket' => 'auto', 'required' => 'brand'],
            'auto_model'             => ['bucket' => 'auto', 'required' => 'model'],
            'auto_renewal_date'      => ['bucket' => 'auto', 'required' => 'renewal_date'],
            'auto_usage'             => ['bucket' => 'auto', 'required' => 'usage'],
            'auto_km_annuel'         => ['bucket' => 'auto', 'required' => 'km_annuel'],
            'auto_existing_products' => ['bucket' => 'auto', 'required' => 'existing_products'],
            'auto_license_number'    => ['bucket' => 'auto', 'required' => 'license_number'],
        ];
    }

    public static function habitation(): array
    {
        return [
            'hab_occupancy'          => ['bucket' => 'habitation', 'required' => 'occupancy'],
            'hab_property_type'      => ['bucket' => 'habitation', 'required' => 'property_type'],
            'hab_address'            => ['bucket' => 'habitation', 'required' => 'address'],
            'hab_living_there'       => ['bucket' => 'habitation', 'required' => 'living_there'],
            'hab_move_in_date'       => ['bucket' => 'habitation', 'required' => 'move_in_date'],
            'hab_units_in_building'  => ['bucket' => 'habitation', 'required' => 'units_in_building'],
            'hab_contents_amount'    => ['bucket' => 'habitation', 'required' => 'contents_amount'],
            'hab_electric_baseboard' => ['bucket' => 'habitation', 'required' => 'electric_baseboard'],
            'hab_supp_heating'       => ['bucket' => 'habitation', 'required' => 'supp_heating'],
            'hab_years_insured'      => ['bucket' => 'habitation', 'required' => 'years_insured'],
            'hab_years_with_insurer' => ['bucket' => 'habitation', 'required' => 'years_with_insurer'],
            'hab_current_insurer'    => ['bucket' => 'habitation', 'required' => 'current_insurer'],
            'hab_consent_profile'    => ['bucket' => 'habitation', 'required' => 'consent_profile'],
            'hab_consent_marketing'  => ['bucket' => 'habitation', 'required' => 'consent_marketing'],
            'hab_marketing_email'    => ['bucket' => 'habitation', 'required' => 'marketing_email'],
            'hab_consent_credit'     => ['bucket' => 'habitation', 'required' => 'consent_credit'],
        ];
    }
}
