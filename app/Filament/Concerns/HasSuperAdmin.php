<?php

namespace App\Filament\Concerns;

trait HasSuperAdmin
{
    public function isSuperAdmin(): bool
    {
        $u = auth()->user();
        if (! $u) return false;

        // Spatie roles (FilamentShield)
        if (method_exists($u, 'hasRole') && $u->hasRole('super_admin')) {
            return true;
        }

        // Fallback si tu as un champ custom
        if (isset($u->is_super_admin) && (bool) $u->is_super_admin) {
            return true;
        }

        return false;
    }
}
