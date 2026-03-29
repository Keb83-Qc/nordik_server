<?php

namespace App\Filament\Concerns;

use App\Models\Language;
use Filament\Forms;

/**
 * Trait réutilisable pour générer des onglets de traduction dynamiquement
 * depuis la table `languages` (au lieu de les hardcoder FR/EN/ES/HT).
 *
 * Ajouter une langue dans Filament → elle apparaît automatiquement
 * dans tous les formulaires qui utilisent ce trait.
 */
trait HasTranslationTabs
{
    // ─── Emoji drapeaux ───────────────────────────────────────────────────────

    protected static function langFlag(string $code): string
    {
        return match (strtolower($code)) {
            'fr'    => '🇫🇷',
            'en'    => '🇬🇧',
            'es'    => '🇪🇸',
            'ht'    => '🇭🇹',
            'pt'    => '🇵🇹',
            'de'    => '🇩🇪',
            'it'    => '🇮🇹',
            'nl'    => '🇳🇱',
            'pl'    => '🇵🇱',
            'ru'    => '🇷🇺',
            'zh'    => '🇨🇳',
            'ja'    => '🇯🇵',
            'ko'    => '🇰🇷',
            'ar'    => '🇸🇦',
            'tr'    => '🇹🇷',
            'uk'    => '🇺🇦',
            default => '🌐',
        };
    }

    // ─── Générateur d'onglets ─────────────────────────────────────────────────

    /**
     * Génère des onglets de traduction pour un champ JSON depuis la table languages.
     *
     * @param string $fieldPrefix   Préfixe JSON : 'question', 'label', 'consent_title'...
     * @param string $inputType     'textarea' (défaut) ou 'text'
     * @param int    $rows          Nb lignes (textarea seulement)
     * @param int    $maxLength     Longueur max du champ
     * @param string $helperText    Texte d'aide affiché uniquement sur la langue par défaut
     */
    protected static function translationTabs(
        string $fieldPrefix,
        string $inputType = 'textarea',
        int    $rows = 2,
        int    $maxLength = 500,
        string $helperText = ''
    ): array {
        $languages   = Language::where('is_active', true)->orderBy('sort_order')->get();
        $defaultCode = Language::defaultCode();

        return $languages->map(function (Language $lang) use (
            $fieldPrefix, $inputType, $rows, $maxLength, $helperText, $defaultCode
        ) {
            $isDefault = $lang->code === $defaultCode;
            $flag      = self::langFlag($lang->code);

            if ($inputType === 'text') {
                $field = Forms\Components\TextInput::make("{$fieldPrefix}.{$lang->code}")
                    ->label($lang->name . ($isDefault ? ' *' : ''))
                    ->required($isDefault)
                    ->maxLength($maxLength);
            } else {
                $field = Forms\Components\Textarea::make("{$fieldPrefix}.{$lang->code}")
                    ->label($lang->name . ($isDefault ? ' *' : ''))
                    ->required($isDefault)
                    ->rows($rows)
                    ->maxLength($maxLength);

                if ($isDefault && $helperText !== '') {
                    $field = $field->helperText($helperText);
                }
            }

            return Forms\Components\Tabs\Tab::make("{$flag} {$lang->name}")
                ->schema([$field]);

        })->toArray();
    }

    // ─── Statut de traduction pour les colonnes de table ──────────────────────

    /**
     * Retourne "X/Y" : nombre de langues actives remplies sur le total.
     * @param array $translatable  Tableau JSON du champ (ex: ['fr' => '...', 'en' => '...'])
     */
    protected static function translationCount(array $translatable): string
    {
        $langs  = Language::activeCodes();
        $filled = array_filter($langs, fn ($l) => !empty(trim($translatable[$l] ?? '')));
        return count($filled) . '/' . count($langs);
    }

    /**
     * Retourne 'success' si toutes les langues actives sont remplies, 'warning' sinon.
     */
    protected static function translationColor(array $translatable): string
    {
        $langs  = Language::activeCodes();
        $filled = array_filter($langs, fn ($l) => !empty(trim($translatable[$l] ?? '')));
        return count($filled) === count($langs) ? 'success' : 'warning';
    }

    /**
     * Retourne un tooltip "🇫🇷 ✅  🇬🇧 🔴  ..." par langue active.
     */
    protected static function translationTooltip(array $translatable): string
    {
        $languages = Language::where('is_active', true)->orderBy('sort_order')->get();

        return $languages->map(fn (Language $l) =>
            self::langFlag($l->code) . ' ' . (! empty(trim($translatable[$l->code] ?? '')) ? '✅' : '🔴')
        )->implode('  ');
    }
}
