<?php

namespace App\Filament\Abf\Resources\AbfCaseAdminResource\Pages;

use App\Filament\Abf\Resources\AbfCaseAdminResource;
use Filament\Resources\Pages\ListRecords;

class ListAllAbfCases extends ListRecords
{
    protected static string $resource = AbfCaseAdminResource::class;

    public function mount(): void
    {
        parent::mount();
        // Rediriger les non-admins vers l'éditeur standalone
        if (! auth()->user()?->hasRoleByName(['admin', 'super_admin'])) {
            $this->redirect(route('abf.landing'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
