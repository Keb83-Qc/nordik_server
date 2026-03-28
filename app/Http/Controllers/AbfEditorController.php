<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Models\AbfParameter;
use App\Services\AbfCaseCalculator;
use Illuminate\Http\Request;

class AbfEditorController extends Controller
{
    public function landing()
    {
        $recentCases = AbfCase::where('advisor_user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'client_first_name', 'client_last_name', 'updated_at', 'status', 'results']);

        return view('abf.editor', [
            'record'      => null,
            'recentCases' => $recentCases,
            'abfParams'   => AbfParameter::allAsMap(),
        ]);
    }

    public function createJson()
    {
        $record = AbfCase::create([
            'advisor_user_id' => auth()->id(),
            'advisor_code'    => auth()->user()?->advisor_code,
            'payload'         => [],
            'results'         => [],
        ]);

        return response()->json([
            'ok'  => true,
            'url' => route('abf.editor.show', $record),
        ]);
    }

    public function create()
    {
        $record = AbfCase::create([
            'advisor_user_id' => auth()->id(),
            'advisor_code'    => auth()->user()?->advisor_code,
            'payload'         => [],
            'results'         => [],
        ]);

        return redirect()->route('abf.editor.show', $record);
    }

    public function show(AbfCase $record)
    {
        abort_unless($record->advisor_user_id === auth()->id(), 403);

        return view('abf.editor', [
            'record'    => $record,
            'abfParams' => AbfParameter::allAsMap(),
        ]);
    }

    public function save(Request $request, AbfCase $record)
    {
        abort_unless($record->advisor_user_id === auth()->id(), 403);

        $payload = $request->input('payload', []);

        $calculator = app(AbfCaseCalculator::class);
        $results    = $calculator->calculate($payload);

        $record->update([
            'payload'           => $payload,
            'results'           => $results,
            'client_first_name' => $payload['client']['prenom'] ?? null,
            'client_last_name'  => $payload['client']['nom']    ?? null,
            'client_birth_date' => $this->parseDob($payload),
        ]);

        return response()->json(['ok' => true]);
    }

    private function parseDob(array $payload): ?string
    {
        $client = $payload['client'] ?? [];
        $moisMap = [
            'Janvier' => '01', 'Février' => '02', 'Mars' => '03',
            'Avril' => '04', 'Mai' => '05', 'Juin' => '06',
            'Juillet' => '07', 'Août' => '08', 'Septembre' => '09',
            'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12',
        ];

        $jour  = $client['ddn_jour']  ?? null;
        $mois  = $client['ddn_mois']  ?? null;
        $annee = $client['ddn_annee'] ?? null;

        if (! $jour || ! $mois || ! $annee) return null;

        $moisNum = $moisMap[$mois] ?? null;
        return $moisNum ? "{$annee}-{$moisNum}-" . str_pad($jour, 2, '0', STR_PAD_LEFT) : null;
    }
}
