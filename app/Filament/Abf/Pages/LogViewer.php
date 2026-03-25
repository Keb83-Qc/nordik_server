<?php

namespace App\Filament\Abf\Pages;

use Filament\Pages\Page;

class LogViewer extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Journal d\'erreurs';
    protected static ?string $title           = 'Journal d\'erreurs ABF';
    protected static ?int    $navigationSort  = 99;

    protected static string $view = 'filament.abf.pages.log-viewer';

    public function getViewData(): array
    {
        return ['entries' => $this->parseLog(200)];
    }

    private function parseLog(int $maxLines): array
    {
        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            return [];
        }

        // Lire les dernières $maxLines lignes sans charger tout le fichier
        $lines = $this->tailFile($path, $maxLines * 5); // buffer x5 pour capturer les traces
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            // Début d'une nouvelle entrée de log
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+\w+\.(ERROR|CRITICAL|EMERGENCY|WARNING|INFO|DEBUG):\s+(.+)/', $line, $m)) {
                if ($current !== null) {
                    $entries[] = $current;
                }
                $current = [
                    'date'    => $m[1],
                    'level'   => $m[2],
                    'message' => $m[3],
                    'context' => '',
                ];
            } elseif ($current !== null) {
                $current['context'] .= $line . "\n";
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        // Plus récent en premier, limiter à $maxLines entrées
        return array_slice(array_reverse($entries), 0, $maxLines);
    }

    private function tailFile(string $path, int $lines): array
    {
        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        $start = max(0, $totalLines - $lines);
        $result = [];

        $file->seek($start);
        while (! $file->eof()) {
            $result[] = rtrim($file->current());
            $file->next();
        }

        return $result;
    }
}
