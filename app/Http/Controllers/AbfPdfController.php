<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Services\AbfCaseCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AbfPdfController extends Controller
{
    public function generate(string $locale, AbfCase $abfCase)
    {
        abort_unless($abfCase->advisor_user_id === auth()->id(), 403);

        $calculator = app(\App\Services\AbfCaseCalculator::class);
        $results = $calculator->calculate($abfCase->payload ?? []);

        $first = data_get($abfCase->payload, 'client.first_name', 'client');
        $last  = data_get($abfCase->payload, 'client.last_name', '');

        $filename = 'abf-' . \Illuminate\Support\Str::slug(trim($first . ' ' . $last)) . '-' . $abfCase->id . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.abf-report', [
            'case' => $abfCase,
            'results' => $results,
        ]);

        return $pdf->download($filename);
    }
}
