<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Filesystem\Filesystem;

class TranslationsManager extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationLabel = 'Traductions';

    protected static string $view = 'filament.pages.translations-manager';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('page_TranslationsManager');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('page_TranslationsManager');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'GestionLangues';
    }

    public ?string $locale = null;
    public ?string $file = null;

    /** @var array<string, string|array> */
    public array $translations = [];

    public function mount(): void
    {
        $this->form->fill([
            'locale' => app()->getLocale(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('locale')
                ->label('Langue')
                ->options($this->getLocales())
                ->reactive()
                ->afterStateUpdated(fn() => $this->loadFileList()),

            Forms\Components\Select::make('file')
                ->label('Fichier')
                ->options(fn() => $this->getFilesForLocale($this->locale))
                ->reactive()
                ->afterStateUpdated(fn() => $this->loadTranslations()),

            Forms\Components\KeyValue::make('translations')
                ->label('Clés / valeurs (niveau 1)')
                ->keyLabel('Clé')
                ->valueLabel('Traduction')
                ->reorderable()
                ->addActionLabel('Ajouter une clé')
                ->columnSpanFull(),
        ];
    }

    protected function getFormModel(): string
    {
        return 'array';
    }

    protected function getLocales(): array
    {
        // Option 1: lire DB (recommandé)
        // return \App\Models\Language::where('active', 1)->pluck('code', 'code')->toArray();

        // Option 2: lire les dossiers existants
        $fs = app(Filesystem::class);
        $base = resource_path('lang');
        if (!$fs->isDirectory($base)) return [];

        $dirs = collect($fs->directories($base))
            ->map(fn($p) => basename($p))
            ->sort()
            ->values();

        return $dirs->mapWithKeys(fn($d) => [$d => strtoupper($d)])->toArray();
    }

    protected function getFilesForLocale(?string $locale): array
    {
        if (!$locale) return [];

        $fs = app(Filesystem::class);
        $dir = resource_path("lang/{$locale}");
        if (!$fs->isDirectory($dir)) return [];

        $files = collect($fs->files($dir))
            ->filter(fn($f) => $f->getExtension() === 'php')
            ->map(fn($f) => $f->getFilename())
            ->sort()
            ->values();

        return $files->mapWithKeys(fn($f) => [$f => $f])->toArray();
    }

    public function loadTranslations(): void
    {
        if (!$this->locale || !$this->file) {
            $this->translations = [];
            $this->form->fill(['translations' => []]);
            return;
        }

        $path = resource_path("lang/{$this->locale}/{$this->file}");
        if (!file_exists($path)) {
            $this->translations = [];
            $this->form->fill(['translations' => []]);
            return;
        }

        $data = include $path;

        // On supporte seulement array
        if (!is_array($data)) $data = [];

        // KeyValue = mieux avec string => string; on garde seulement niveau 1
        $flat = [];
        foreach ($data as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                $flat[(string)$k] = (string)$v;
            }
        }

        $this->translations = $flat;
        $this->form->fill(['translations' => $this->translations]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $locale = $state['locale'] ?? null;
        $file = $state['file'] ?? null;
        $translations = $state['translations'] ?? [];

        if (!$locale || !$file || !is_array($translations)) {
            Notification::make()->title('Sélectionne une langue et un fichier.')->danger()->send();
            return;
        }

        $path = resource_path("lang/{$locale}/{$file}");

        // Écrit un fichier PHP propre
        $export = var_export($translations, true);
        $content = "<?php\n\nreturn {$export};\n";

        file_put_contents($path, $content);

        Notification::make()->title('Traductions enregistrées')->success()->send();
    }

    protected function loadFileList(): void
    {
        // Force refresh de la liste des fichiers quand locale change
        $this->file = null;
        $this->form->fill(['file' => null, 'translations' => []]);
    }
}
