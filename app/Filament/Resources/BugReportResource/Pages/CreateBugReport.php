<?php

namespace App\Filament\Resources\BugReportResource\Pages;

use App\Filament\Resources\BugReportResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateBugReport extends CreateRecord
{
    protected static string $resource = BugReportResource::class;

    protected function getTitle(): string
    {
        return 'Nouveau rapport ou suggestion';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rapport envoyé avec succès !';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Envoie vers le super_admin (premier trouvé)
        $superAdmin = User::role('super_admin')->first();

        return [
            'sender_id'   => auth()->id(),
            'receiver_id' => $superAdmin?->id,
            'subject'     => $data['subject'],
            'body'        => $data['body'],
            'is_read'     => false,
            'status'      => 'pending',
            'data'        => [
                'type'     => 'bug_report',
                'category' => $data['category'] ?? 'bug',
                'priority' => $data['priority'] ?? 'medium',
                'url'      => $data['url'] ?? null,
            ],
        ];
    }
}
