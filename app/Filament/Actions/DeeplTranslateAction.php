<?php

namespace App\Filament\Actions;

use App\Models\Language;
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
 * - Lit depuis {prefix}.{defaultCode} (langue par défaut de la table languages)
 * - Traduit vers toutes les autres langues actives supportées par DeepL
 * - Remplit uniquement les langues vides (sauf si $overwriteExisting = true)
 */
class DeeplTranslateAction
{
    /**
     * Correspondance code language → code DeepL API.
     * Les langues absentes de cette map sont ignorées (non supportées par DeepL).
     */
    private const DEEPL_CODES = [
        'bg' => 'BG', 'cs' => 'CS', 'da' => 'DA', 'de' => 'DE',
        'el' => 'EL', 'en' => 'EN', 'es' => 'ES', 'et' => 'ET',
        'fi' => 'FI', 'fr' => 'FR', 'hu' => 'HU', 'id' => 'ID',
        'it' => 'IT', 'ja' => 'JA', 'ko' => 'KO', 'lt' => 'LT',
        'lv' => 'LV', 'nl' => 'NL', 'pl' => 'PL', 'pt' => 'PT',
        'ro' => 'RO', 'ru' => 'RU', 'sk' => 'SK', 'sl' => 'SL',
        'sv' => 'SV', 'tr' => 'TR', 'uk' => 'UK', 'zh' => 'ZH',
    ];

    /**
     * Retourne la map [code → DeepL code] des langues actives cibles (hors langue source).
     * Utilisée par les bulk actions du tableau.
     */
    public static function buildTargets(): array
    {
        $sourceCode = Language::defaultCode();
        $targets    = [];

        foreach (Language::activeCodes() as $code) {
            if ($code === $sourceCode) {
                continue; // on ne traduit pas vers la langue source
            }
            if (isset(self::DEEPL_CODES[$code])) {
                $targets[$code] = self::DEEPL_CODES[$code];
            }
            // langues non supportées par DeepL (ex: HT) sont ignorées silencieusement
        }

        return $targets;
    }

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

        $targets    = self::buildTargets();
        $sourceCode = Language::defaultCode();
        $targetList = implode(', ', array_map('strtoupper', array_keys($targets)));
        $tooltip    = strtoupper($sourceCode) . ' → ' . $targetList . ' via DeepL (langues vides uniquement)';

        return Action::make("deepl_{$fieldPrefix}")
            ->label('Traduire avec DeepL')
            ->icon('heroicon-o-language')
            ->color('info')
            ->tooltip($tooltip)
            ->action(function (Get $get, Set $set) use ($fieldPrefix, $isHtml, $overwriteExisting, $targets, $sourceCode) {

                $sourceText = trim((string) ($get("{$fieldPrefix}.{$sourceCode}") ?? ''));

                // Compat. : si la langue source est vide, essayer 'fr' comme fallback
                if ($sourceText === '' && $sourceCode !== 'fr') {
                    $sourceText = trim((string) ($get("{$fieldPrefix}.fr") ?? ''));
                }

                if ($sourceText === '') {
                    Notification::make()
                        ->warning()
                        ->title('Texte source vide')
                        ->body('Saisissez d\'abord le texte dans la langue par défaut avant de lancer la traduction.')
                        ->send();
                    return;
                }

                $deepLSourceCode = self::DEEPL_CODES[$sourceCode] ?? 'FR';

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
                            ? $deepl->translateHtmlPreserveTags($sourceText, $deepLSourceCode, $deepLCode)
                            : $deepl->translatePlain($sourceText, $deepLSourceCode, $deepLCode);

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
