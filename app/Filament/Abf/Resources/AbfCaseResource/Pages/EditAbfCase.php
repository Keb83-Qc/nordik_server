<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Pages;

use App\Filament\Abf\Resources\AbfCaseResource;
use App\Filament\Pages\BaseEditRecord;
use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;

class EditAbfCase extends BaseEditRecord
{
    protected static string $resource = AbfCaseResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        if (! $this->record) {
            return 'Analyse de Besoin Financier';
        }
        $first = (string) data_get($this->record->payload, 'client.first_name', '');
        $last  = (string) data_get($this->record->payload, 'client.last_name', '');
        $name  = trim("$first $last");

        return $name
            ? "Analyse de Besoin Financier — $name"
            : 'Analyse de Besoin Financier';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $client = (array) data_get($data, 'payload.client', []);

        if (empty($client['jobs']) && (! empty($client['employer']) || ! empty($client['occupation']) || ! empty($client['annual_income']))) {
            data_set($data, 'payload.client.jobs', [[
                'employer' => $client['employer'] ?? null,
                'occupation' => $client['occupation'] ?? null,
                'annual_income' => $client['annual_income'] ?? null,
            ]]);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['payload']['results']);

        // Force ownership metadata (avoid orphan records / 404 in resource query)
        $data['advisor_user_id'] = auth()->id();
        $data['advisor_code'] = auth()->user()?->advisor_code;

        $data['payload'] = $data['payload'] ?? [];
        $data['payload']['death_budget'] ??= [];
        $data['payload']['death_budget']['survivor_monthly_expenses'] ??= [];
        $data['payload']['death_budget']['one_time_costs'] ??= [];
        $data['payload']['death_budget']['income_sources'] ??= [];

        // Mirror client jobs total into payload.client.annual_income when jobs are used
        $jobs = (array) data_get($data, 'payload.client.jobs', []);
        $jobsTotal = 0.0;
        foreach ($jobs as $job) {
            $jobsTotal += (float) ($job['annual_income'] ?? 0);
        }
        if ($jobsTotal > 0) {
            data_set($data, 'payload.client.annual_income', round($jobsTotal, 2));
        }

        $calculator = app(\App\Services\AbfCaseCalculator::class);
        $data['results'] = $calculator->calculate($data['payload'] ?? []);

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

    /**
     * Sauvegarde silencieuse appelée par le JS toutes les 30 s.
     * Utilise la même logique que mutateFormDataBeforeSave() mais sans redirection ni notification.
     */
    public function autoSave(): void
    {
        try {
            $data = $this->mutateFormDataBeforeSave($this->data ?? []);
            $this->record->update($data);
            $this->dispatch('abf-auto-saved');
        } catch (\Throwable) {
            // Échec silencieux — ne pas interrompre l'utilisateur
        }
    }

    public function getFooter(): ?View
    {
        return view('filament.abf.partials.auto-save-bar', [
            'mode'     => 'edit',
            'recordId' => $this->record?->id,
        ]);
    }

    protected function getCustomHeaderActions(): array
    {
        return [
            Actions\Action::make('pdf')
                ->label('Télécharger PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn ($record) => route('abf.pdf', [
                    'locale' => app()->getLocale(),
                    'abfCase' => $record,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
