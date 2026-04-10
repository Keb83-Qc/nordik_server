<?php

namespace App\Filament\Resources\WebVitalLogResource\Pages;

use App\Filament\Resources\WebVitalLogResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageWebVitalLogs extends ManageRecords
{
    protected static string $resource = WebVitalLogResource::class;

    public function getTabs(): array
    {
        return [
            'tous' => Tab::make('Tous')
                ->query(fn(Builder $query) => $query->where('message', 'like', '[WebVital]%'))
                ->icon('heroicon-m-chart-bar'),

            'lcp' => Tab::make('LCP')
                ->query(fn(Builder $query) => $query
                    ->where('message', 'like', '[WebVital]%')
                    ->whereJsonContains('context->metric', 'LCP'))
                ->icon('heroicon-m-eye')
                ->badgeColor('info'),

            'inp' => Tab::make('INP')
                ->query(fn(Builder $query) => $query
                    ->where('message', 'like', '[WebVital]%')
                    ->whereJsonContains('context->metric', 'INP'))
                ->icon('heroicon-m-cursor-arrow-rays')
                ->badgeColor('warning'),

            'cls' => Tab::make('CLS')
                ->query(fn(Builder $query) => $query
                    ->where('message', 'like', '[WebVital]%')
                    ->whereJsonContains('context->metric', 'CLS'))
                ->icon('heroicon-m-arrows-pointing-out')
                ->badgeColor('success'),

            'fcp' => Tab::make('FCP')
                ->query(fn(Builder $query) => $query
                    ->where('message', 'like', '[WebVital]%')
                    ->whereJsonContains('context->metric', 'FCP'))
                ->icon('heroicon-m-bolt')
                ->badgeColor('primary'),

            'ttfb' => Tab::make('TTFB')
                ->query(fn(Builder $query) => $query
                    ->where('message', 'like', '[WebVital]%')
                    ->whereJsonContains('context->metric', 'TTFB'))
                ->icon('heroicon-m-server')
                ->badgeColor('gray'),
        ];
    }
}
