<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Services\AbfCaseCalculator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AbfPdfController extends Controller
{
    public function generate(string $locale, AbfCase $abfCase)
    {
        abort_unless($abfCase->advisor_user_id === auth()->id(), 403);

        try {
            $calculator = app(AbfCaseCalculator::class);
            $results    = $calculator->calculate($abfCase->payload ?? []);

            $clientFirst  = data_get($abfCase->payload, 'client.first_name', '');
            $clientLast   = data_get($abfCase->payload, 'client.last_name', 'Client');
            $hasSpousePdf = (bool) data_get($abfCase->payload, 'has_spouse', false);
            $spouseFirst  = $hasSpousePdf ? data_get($abfCase->payload, 'spouse.first_name', '') : '';
            $spouseLast   = $hasSpousePdf ? data_get($abfCase->payload, 'spouse.last_name', '') : '';

            $clientPart = Str::slug(trim($clientLast . ' ' . $clientFirst), '_');
            $spousePart = ($hasSpousePdf && ($spouseLast || $spouseFirst))
                ? '_' . Str::slug(trim($spouseLast . ' ' . $spouseFirst), '_')
                : '';
            $datePart  = now()->format('Y-m-d');
            $filename  = 'ABF_' . $clientPart . $spousePart . '_' . $datePart . '.pdf';

            $sections   = (array) data_get($abfCase->payload, 'rapport.sections', []);
            $photoFile  = data_get($abfCase->payload, 'rapport.photo');
            $photoPath  = $photoFile ? public_path('assets/img/abf-covers/' . basename((string) $photoFile)) : null;
            $coverPhoto = ($photoPath && file_exists($photoPath))
                ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoPath))
                : null;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.abf-report', [
                'case'       => $abfCase,
                'results'    => $results,
                'sections'   => $sections,
                'coverPhoto' => $coverPhoto,
            ]);

            Log::channel('daily')->info('ABF PDF généré', [
                'case_id'  => $abfCase->id,
                'user_id'  => auth()->id(),
                'filename' => $filename,
            ]);

            return $pdf->download($filename);

        } catch (\Throwable $e) {
            Log::channel('daily')->error('ABF PDF — erreur de génération', [
                'case_id' => $abfCase->id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
                'class'   => get_class($e),
                'trace'   => collect(explode("\n", $e->getTraceAsString()))
                                ->take(15)
                                ->implode("\n"),
            ]);

            throw $e;
        }
    }
}
