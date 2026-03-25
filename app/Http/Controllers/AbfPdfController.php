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

            $first    = data_get($abfCase->payload, 'client.first_name', 'client');
            $last     = data_get($abfCase->payload, 'client.last_name', '');
            $filename = 'abf-' . Str::slug(trim($first . ' ' . $last)) . '-' . $abfCase->id . '.pdf';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.abf-report', [
                'case'    => $abfCase,
                'results' => $results,
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
