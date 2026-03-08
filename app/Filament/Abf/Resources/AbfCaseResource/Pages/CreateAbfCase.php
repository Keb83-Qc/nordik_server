<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Pages;

use App\Filament\Abf\Resources\AbfCaseResource;
use App\Filament\Pages\BaseCreateRecord;
use Filament\Support\Enums\MaxWidth;

class CreateAbfCase extends BaseCreateRecord
{
    protected static string $resource = AbfCaseResource::class;

    protected static ?string $title = 'Création - Analyse des Besoins Financiers';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->normalizePayloadAndOwner($data);

        $calculator = app(\App\Services\AbfCaseCalculator::class);
        $data['results'] = $calculator->calculate($data['payload'] ?? []);

        return $data;
    }

    protected function normalizePayloadAndOwner(array $data): array
    {
        unset($data['payload']['results']);

        $data['advisor_user_id'] = auth()->id();
        $data['advisor_code'] = auth()->user()?->advisor_code;

        $data['payload'] = $data['payload'] ?? [];

        // Legacy compatibility: single client employer fields -> jobs[]
        $client = (array) data_get($data, 'payload.client', []);
        if (empty($client['jobs']) && (! empty($client['employer']) || ! empty($client['occupation']) || ! empty($client['annual_income']))) {
            data_set($data, 'payload.client.jobs', [[
                'employer' => $client['employer'] ?? null,
                'occupation' => $client['occupation'] ?? null,
                'annual_income' => $client['annual_income'] ?? null,
            ]]);
        }

        $data['payload']['death_budget'] ??= [];
        $data['payload']['death_budget']['survivor_monthly_expenses'] ??= [];
        $data['payload']['death_budget']['one_time_costs'] ??= [];
        $data['payload']['death_budget']['income_sources'] ??= [];

        return $data;
    }

    public function getExtraBodyAttributes(): array
    {
        $attributes = parent::getExtraBodyAttributes();
        $attributes['class'] = trim(($attributes['class'] ?? '') . ' abf-fullwidth');
        return $attributes;
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
