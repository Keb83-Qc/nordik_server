<?php

namespace App\Filament\Abf\Support;

use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

final class BilanUi
{
    public static function headerRow(string $prefix): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make("{$prefix}_hdr_lbl")->label(false)->content(''),
            Placeholder::make("{$prefix}_hdr_you")->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOUS</div>')),
            Placeholder::make("{$prefix}_hdr_spouse")->label(false)->content(new HtmlString('<div class="abf-bilan-colhead">VOTRE CONJOINT</div>')),
        ]);
    }

    public static function moneyRow(string $label, string $basePath, string $keyPrefix): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make("{$keyPrefix}_label")
                ->label(false)
                ->content($label)
                ->extraAttributes(['class' => 'abf-bilan-label']),
            TextInput::make("{$basePath}.client")
                ->label(false)
                ->numeric()
                ->inputMode('decimal')
                ->prefix('$')
                ->minValue(0)
                ->extraInputAttributes(['class' => 'abf-underline-input']),
            TextInput::make("{$basePath}.spouse")
                ->label(false)
                ->numeric()
                ->inputMode('decimal')
                ->prefix('$')
                ->minValue(0)
                ->extraInputAttributes(['class' => 'abf-underline-input'])
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    /**
     * $totalResolver(Get $get, string $person): float
     */
    public static function totalRow(string $label, string $keyPrefix, Closure $totalResolver): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make("{$keyPrefix}_label")
                ->label(false)
                ->content($label)
                ->extraAttributes(['class' => 'abf-bilan-total-label']),
            Placeholder::make("{$keyPrefix}_total_client")
                ->label(false)
                ->content(fn(Get $get) => Money::dollars0((float) $totalResolver($get, 'client')))
                ->extraAttributes(['class' => 'abf-bilan-total-value']),
            Placeholder::make("{$keyPrefix}_total_spouse")
                ->label(false)
                ->content(fn(Get $get) => Money::dollars0((float) $totalResolver($get, 'spouse')))
                ->extraAttributes(['class' => 'abf-bilan-total-value'])
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    public static function dualMoneyInputRow(string $label, string $basePath): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make(md5($label . $basePath) . '_lbl')->label(false)->content($label),
            TextInput::make("{$basePath}.client")->label(false)->numeric()->prefix('$')->minValue(0),
            TextInput::make("{$basePath}.spouse")->label(false)->numeric()->prefix('$')->minValue(0)
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    public static function dualPercentInputRow(string $label, string $basePath): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make(md5($label . $basePath) . '_lbl')->label(false)->content($label),
            TextInput::make("{$basePath}.client")->label(false)->numeric()->suffix('%'),
            TextInput::make("{$basePath}.spouse")->label(false)->numeric()->suffix('%')
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    public static function dualYearsInputRow(string $label, string $basePath): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make(md5($label . $basePath) . '_lbl')->label(false)->content($label),
            TextInput::make("{$basePath}.client")->label(false)->numeric()->suffix('ans'),
            TextInput::make("{$basePath}.spouse")->label(false)->numeric()->suffix('ans')
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    /**
     * $resolver(Get $get, string $person): float
     */
    public static function dualMoneyComputedRow(string $label, Closure $resolver): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make(md5($label) . '_lbl')->label(false)->content($label),
            Placeholder::make(md5($label) . '_c')->label(false)->content(fn(Get $get) => Money::dollars((float) $resolver($get, 'client'))),
            Placeholder::make(md5($label) . '_s')->label(false)->content(fn(Get $get) => Money::dollars((float) $resolver($get, 'spouse')))
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }

    /**
     * $resolver(Get $get, string $person): float
     */
    public static function dualNumberComputedRow(string $label, Closure $resolver, int $decimals = 2): Grid
    {
        return Grid::make(3)->schema([
            Placeholder::make(md5($label) . '_lbl')->label(false)->content($label),
            Placeholder::make(md5($label) . '_c')->label(false)->content(fn(Get $get) => number_format((float) $resolver($get, 'client'), $decimals, '.', ' ')),
            Placeholder::make(md5($label) . '_s')->label(false)->content(fn(Get $get) => number_format((float) $resolver($get, 'spouse'), $decimals, '.', ' '))
                ->visible(fn(Get $get) => (bool) $get('payload.has_spouse')),
        ]);
    }
}
