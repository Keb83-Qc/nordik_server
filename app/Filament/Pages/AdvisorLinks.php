<?php

namespace App\Filament\Pages;

use App\Mail\AdvisorLinkShare;
use App\Models\SystemLog;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdvisorLinks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Liens de Consentement';
    protected static ?string $title = 'Liens de Consentement des Conseillers';

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class);
    }

    protected static string $view = 'filament.pages.advisor-links';

    // ✅ MASS SEND STATE
    public array $selectedAdvisors = [];
    public bool $selectAll = false;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->isSuperAdmin() || $user->hasRoleByName('admin');
    }

    protected function getAdvisorsQuery()
    {
        return User::query()
            ->whereNotNull('advisor_code')
            ->where('advisor_code', '!=', '')
            ->where('id', '!=', 0) // ignore robot
            ->orderBy('first_name');
    }

    protected function getViewData(): array
    {
        return [
            'advisors' => $this->getAdvisorsQuery()->get(),
        ];
    }

    // ✅ “Tout sélectionner”
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedAdvisors = $this->getAdvisorsQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedAdvisors = [];
        }
    }

    // ✅ garder le checkbox “Tout sélectionner” sync
    public function updatedSelectedAdvisors(): void
    {
        $total = (int) $this->getAdvisorsQuery()->count();
        $this->selectAll = $total > 0 && count($this->selectedAdvisors) === $total;
    }

    /**
     * Action appelée par le bouton "Envoyer" (unitaire)
     */
    public function sendLink(int $advisorId): void
    {
        $advisor = User::find($advisorId);

        if (!$advisor || !$advisor->email) {
            Notification::make()
                ->title('Erreur')
                ->body('Conseiller introuvable ou sans courriel.')
                ->danger()
                ->send();
            return;
        }

        // ✅ FIX: locale obligatoire dans la route
        $locale = session('locale', config('app.fallback_locale', 'fr'));
        $link = route('consent.show', [
            'locale' => $locale,
            'code'   => $advisor->advisor_code,
        ]);

        try {
            Mail::to($advisor->email)->send(new AdvisorLinkShare($advisor, $link));

            Notification::make()
                ->title('Envoyé !')
                ->body("Le lien a été envoyé à {$advisor->email}")
                ->success()
                ->send();

            SystemLog::record('info', "Lien de consentement envoyé à {$advisor->full_name}");
        } catch (\Exception $e) {
            Notification::make()
                ->title("Erreur d'envoi")
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ Envoi en masse (sélection)
     */
    public function sendBulkLinks(): void
    {
        $ids = array_values(array_filter(array_map('intval', $this->selectedAdvisors)));

        if (count($ids) === 0) {
            Notification::make()
                ->title('Aucun conseiller sélectionné')
                ->warning()
                ->send();
            return;
        }

        $advisors = $this->getAdvisorsQuery()
            ->whereIn('id', $ids)
            ->get();

        $sent = 0;
        foreach ($advisors as $advisor) {
            if (!$advisor->email) {
                continue;
            }

            // réutilise le même code (et donc le fix locale)
            $this->sendLink((int) $advisor->id);
            $sent++;
        }

        Notification::make()
            ->title('Envoi en masse terminé')
            ->body("Courriels envoyés: {$sent}")
            ->success()
            ->send();

        // reset sélection
        $this->selectedAdvisors = [];
        $this->selectAll = false;

        SystemLog::record('info', "Envoi en masse de liens de consentement (count={$sent})");
    }
}
