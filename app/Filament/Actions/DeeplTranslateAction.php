<?php

namespace App\Filament\Actions;

use App\Services\DeeplTranslator;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

/**
 * Action DeepL réutilisable pour les formulaires Filament.
 *
 * Usage dans un Section::headerActions() :
 *   DeeplTranslateAction::forField('question')
 *   DeeplTranslateAction::forField('consent_text', isHtml: true)
 *   DeeplTranslateAction::forField('label')
 *
 * - Lit toujours depuis {prefix}.fr (requis)
 * - Traduit vers EN, ES, HT — remplit uniquement les langues vides
 * - Option $overwriteExisting pour tout écraser
 */
class DeeplTranslateAction
{
    /**
     * @param string $fieldPrefix       Préfixe du champ JSON (question, label, consent_title, consent_text)
     * @param bool   $isHtml            true = utilise translateHtmlPreserveTags, false = translatePlain
     * @param bool   $overwriteExisting true = écrase toutes les langues, false = remplit seulement les vides
     */
    public static function forField(
        string $fieldPrefix,
        bool $isHtml = false,
        bool $overwriteExisting = false
    ): Action {

        // Langues cibles — même ordre que nos onglets
        // HT = Créole haïtien : DeepL peut l'échouer silencieusement, on gère l'exception
        $targets = ['en' => 'EN', 'es' => 'ES', 'ht' => 'HT'];

        return Action::make("deepl_{$fieldPrefix}")
            ->label('Traduire avec DeepL')
            ->icon('heroicon-o-language')
            ->color('info')
            ->tooltip('FR → EN, ES, HT via DeepL (langues vides uniquement)')
            ->action(function (Get $get, Set $set) use ($fieldPrefix, $isHtml, $overwriteExisting, $targets) {

                $frText = trim((string) ($get("{$fieldPrefix}.fr") ?? ''));

                if ($frText === '') {
                    Notification::make()
                        ->warning()
                        ->title('Texte français vide')
                        ->body('Saisissez d\'abord le texte en français avant de lancer la traduction.')
                        ->send();
                    return;
                }

                /** @var DeeplTranslator $deepl */
                $deepl   = app(DeeplTranslator::class);
                $success = [];
                $skipped = [];
                $failed  = [];

                foreach ($targets as $lang => $deepLCode) {
                    $existing = trim((string) ($get("{$fieldPrefix}.{$lang}") ?? ''));

                    // Ne pas écraser si déjà traduit (sauf si $overwriteExisting)
                    if (!$overwriteExisting && $existing !== '') {
                        $skipped[] = strtoupper($lang);
                        continue;
                    }

                    try {
                        $translated = $isHtml
                            ? $deepl->translateHtmlPreserveTags($frText, 'FR', $deepLCode)
                            : $deepl->translatePlain($frText, 'FR', $deepLCode);

                        $set("{$fieldPrefix}.{$lang}", $translated);
                        $success[] = strtoupper($lang);

                    } catch (\Throwable $e) {
                        $failed[] = strtoupper($lang);
                    }
                }

                // ─── Notification résumé ───────────────────────────────────
                if (!empty($success)) {
                    $lines = ['✅ Traduit : ' . implode(', ', $success)];
                    if (!empty($skipped)) {
                        $lines[] = '⏭ Ignoré (déjà traduit) : ' . implode(', ', $skipped);
                    }
                    if (!empty($failed)) {
                        $lines[] = '⚠️ Échec : ' . implode(', ', $failed);
                    }

                    Notification::make()
                        ->success()
                        ->title('Traduction terminée')
                        ->body(implode("\n", $lines))
                        ->send();

                } elseif (!empty($skipped) && empty($failed)) {
                    Notification::make()
                        ->info()
                        ->title('Déjà traduit')
                        ->body('Toutes les langues sont déjà remplies. Activez "Écraser" pour forcer la retraduction.')
                        ->send();

                } else {
                    Notification::make()
                        ->danger()
                        ->title('Erreur DeepL')
                        ->body('Vérifiez votre clé API DeepL dans le fichier .env (DEEPL_API_KEY).')
                        ->send();
                }
            });
    }
}
