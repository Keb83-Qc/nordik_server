<?php

namespace App\Services\Abf;

final class BalanceSheet
{
    public function sumAssets(array $assets, ?string $owner): float
    {
        return collect($assets)
            ->filter(fn($row) => $owner ? (($row['owner'] ?? 'client') === $owner) : true)
            ->sum(fn($row) => (float) ($row['value'] ?? 0));
    }

    public function sumLiabilities(array $liabilities, ?string $owner): float
    {
        return collect($liabilities)
            ->filter(fn($row) => $owner ? (($row['owner'] ?? 'client') === $owner) : true)
            ->sum(fn($row) => (float) ($row['balance'] ?? 0));
    }
}
