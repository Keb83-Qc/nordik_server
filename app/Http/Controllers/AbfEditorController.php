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
            ->get(['id', 'slug', 'client_first_name', 'client_last_name', 'updated_at', 'status', 'results', 'payload']);

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

        $identifier = 'nouveau-' . $record->id;

        return response()->json([
            'ok'      => true,
            'id'      => $record->id,
            'url'     => route('abf.editor.show', ['record' => $identifier]),
            'save_url'=> route('abf.editor.save', ['record' => $identifier]),
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

        return redirect()->route('abf.editor.show', ['record' => 'nouveau-' . $record->id]);
    }

    public function show(string $record)
    {
        $abfCase = $this->resolveRecord($record);
        abort_unless($abfCase->advisor_user_id === auth()->id(), 403);

        return view('abf.editor', [
            'record'    => $abfCase,
            'abfParams' => AbfParameter::allAsMap(),
        ]);
    }

    public function save(Request $request, string $record)
    {
        $abfCase = $this->resolveRecord($record);
        abort_unless($abfCase->advisor_user_id === auth()->id(), 403);

        $payload = $request->input('payload', []);

        // Exiger au minimum le prénom ou le nom avant de sauvegarder
        $hasStep1 = ! empty($payload['client']['prenom']) || ! empty($payload['client']['nom']);
        if (! $hasStep1 && empty($abfCase->client_first_name)) {
            return response()->json(['ok' => false, 'reason' => 'step1_incomplete'], 422);
        }

        $calculator = app(AbfCaseCalculator::class);
        $results    = $calculator->calculate($payload);

        $abfCase->update([
            'payload'           => $payload,
            'results'           => $results,
            'client_first_name' => $payload['client']['prenom'] ?? $abfCase->client_first_name,
            'client_last_name'  => $payload['client']['nom']    ?? $abfCase->client_last_name,
            'client_birth_date' => $this->parseDob($payload)    ?? $abfCase->client_birth_date,
        ]);

        // Générer le slug si le nom vient d'être rempli et qu'on n'en a pas encore
        if (! $abfCase->slug && $abfCase->client_last_name) {
            $abfCase->generateSlug();
            $abfCase->refresh();
        }

        return response()->json([
            'ok'  => true,
            'url' => $abfCase->slug
                ? route('abf.editor.show', ['record' => $abfCase->slug])
                : route('abf.editor.show', ['record' => 'nouveau-' . $abfCase->id]),
            'save_url' => $abfCase->slug
                ? route('abf.editor.save', ['record' => $abfCase->slug])
                : route('abf.editor.save', ['record' => 'nouveau-' . $abfCase->id]),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveRecord(string $identifier): AbfCase
    {
        if (preg_match('/^nouveau-(\d+)$/', $identifier, $m)) {
            return AbfCase::findOrFail($m[1]);
        }

        return AbfCase::where('slug', $identifier)
            ->where('advisor_user_id', auth()->id())
            ->firstOrFail();
    }

    private function parseDob(array $payload): ?string
    {
        $client = $payload['client'] ?? [];
        $moisMap = [
            'Janvier' => '01', 'Février' => '02', 'Mars' => '03',
            'Avril'   => '04', 'Mai'     => '05', 'Juin' => '06',
            'Juillet' => '07', 'Août'    => '08', 'Septembre' => '09',
            'Octobre' => '10', 'Novembre'=> '11', 'Décembre'  => '12',
        ];

        $jour  = $client['ddn_jour']  ?? null;
        $mois  = $client['ddn_mois']  ?? null;
        $annee = $client['ddn_annee'] ?? null;

        if (! $jour || ! $mois || ! $annee) return null;

        $moisNum = $moisMap[$mois] ?? null;
        return $moisNum ? "{$annee}-{$moisNum}-" . str_pad($jour, 2, '0', STR_PAD_LEFT) : null;
    }
}
