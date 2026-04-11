<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Services\AbfCaseCalculator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

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

            $html = view('pdf.abf-report', [
                'case'       => $abfCase,
                'results'    => $results,
                'sections'   => $sections,
                'coverPhoto' => $coverPhoto,
            ])->render();

            $tempDir = storage_path('app/mpdf');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'default_font'  => 'dejavusans',
                'tempDir'       => $tempDir,
            ]);

            $mpdf->WriteHTML($html);

            Log::channel('daily')->info('ABF PDF généré', [
                'case_id'  => $abfCase->id,
                'user_id'  => auth()->id(),
                'filename' => $filename,
            ]);

            return response($mpdf->Output($filename, Destination::STRING_RETURN), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

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
