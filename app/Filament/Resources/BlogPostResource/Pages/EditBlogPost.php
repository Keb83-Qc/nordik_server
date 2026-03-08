<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Pages\BaseEditRecord;
use App\Filament\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Models\Language;
use App\Services\DeeplTranslator;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class EditBlogPost extends BaseEditRecord
{
    protected static string $resource = BlogPostResource::class;

    public function isSuperAdmin(): bool
    {
        $u = auth()->user();
        if (! $u) return false;

        if (method_exists($u, 'hasRole') && $u->hasRole('super_admin')) return true;

        if (isset($u->is_super_admin) && (bool) $u->is_super_admin) return true;

        return false;
    }

    public function estimateCharsSource(): int
    {
        /** @var BlogPost $post */
        $post = $this->getRecord();

        $frTitle = (string) $post->getTranslation('title', 'fr', false);
        $frBody  = (string) $post->getTranslation('content', 'fr', false);

        $plain = trim(strip_tags($frTitle . "\n" . $frBody));
        return mb_strlen($plain);
    }

    protected function getCustomHeaderActions(): array
    {
        if (! $this->isSuperAdmin()) {
            return [];
        }

        return [
            Actions\Action::make('deepl_quota')
                ->label('Quota DeepL')
                ->icon('heroicon-m-signal')
                ->color('gray')
                ->action(function (DeeplTranslator $deepl) {
                    $u = $deepl->usage();

                    $used = (int) ($u['character_count'] ?? 0);
                    $lim  = (int) ($u['character_limit'] ?? 0);

                    Notification::make()
                        ->title('DeepL quota')
                        ->body("Utilisé: {$used} / {$lim}")
                        ->success()
                        ->send();
                }),

            $this->translateAction('es', 'FR → Espagnol (ES)', 'success'),
            $this->translateAction('ht', 'FR → Créole (HT)', 'warning'),
        ];
    }

    private function translateAction(string $target, string $label, string $color): Actions\Action
    {
        return Actions\Action::make('translate_' . $target)
            ->label($label)
            ->icon('heroicon-m-language')
            ->color($color)
            ->modalHeading('Traduction DeepL (manuel)')
            ->modalSubmitActionLabel('Confirmer la traduction')
            ->modalCancelActionLabel('Annuler')
            ->form([
                Placeholder::make('chars')
                    ->label('')
                    ->content(fn() => 'Caractères estimés (source FR, HTML retiré): ' . $this->estimateCharsSource()),

                Toggle::make('translate_title')
                    ->label('Traduire le titre')
                    ->default(true),

                Toggle::make('translate_slug')
                    ->label('Régénérer le slug (depuis le titre traduit)')
                    ->default(true),

                Toggle::make('translate_content')
                    ->label('Traduire le contenu (HTML conservé)')
                    ->default(true),

                Toggle::make('overwrite_existing')
                    ->label('Écraser si déjà traduit')
                    ->default(false),
            ])
            ->action(function (array $data, DeeplTranslator $deepl) use ($target) {
                /** @var BlogPost $post */
                $post = $this->getRecord();

                $overwrite = (bool) ($data['overwrite_existing'] ?? false);
                $doTitle   = (bool) ($data['translate_title'] ?? true);
                $doSlug    = (bool) ($data['translate_slug'] ?? true);
                $doContent = (bool) ($data['translate_content'] ?? true);

                $srcTitle = (string) $post->getTranslation('title', 'fr', false);
                $srcBody  = (string) $post->getTranslation('content', 'fr', false);

                // TITRE
                $translatedTitle = null;

                if ($doTitle) {
                    $existing = (string) $post->getTranslation('title', $target, false);

                    if ($overwrite || blank($existing)) {
                        $translatedTitle = trim($deepl->translatePlain($srcTitle, 'fr', $target));
                        $post->setTranslation('title', $target, $translatedTitle);
                    }
                }

                // SLUG (depuis titre traduit si dispo, sinon titre cible existant)
                if ($doSlug) {
                    $existingSlug = (string) $post->getTranslation('slug', $target, false);

                    if ($overwrite || blank($existingSlug)) {
                        $titleForSlug = $translatedTitle
                            ?? (string) $post->getTranslation('title', $target, false)
                            ?? $srcTitle;

                        $slug = BlogPost::makeSeoSlug((string) $titleForSlug, $target);
                        $post->setTranslation('slug', $target, $slug);
                    }
                }

                // CONTENU (HTML conservé)
                if ($doContent) {
                    $existingBody = (string) $post->getTranslation('content', $target, false);

                    if ($overwrite || blank($existingBody)) {
                        $translatedHtml = $deepl->translateHtmlPreserveTags($srcBody, 'fr', $target);
                        $post->setTranslation('content', $target, $translatedHtml);
                    }
                }

                $post->save();

                Notification::make()
                    ->title('Traduction appliquée')
                    ->success()
                    ->send();
            });
    }
}
