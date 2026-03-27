<?php

namespace App\Http\Controllers;

use App\Models\AbfCase;
use App\Services\AbfCaseCalculator;
use Illuminate\Http\Request;

class AbfEditorController extends Controller
{
    public function landing()
    {
        $recentCases = AbfCase::where('advisor_user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'payload', 'updated_at']);

        return view('abf.editor', [
            'record'      => null,
            'recentCases' => $recentCases,
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
        // Only the owning advisor can access
        abort_unless($record->advisor_user_id === auth()->id(), 403);

        return view('abf.editor', compact('record'));
    }

    public function save(Request $request, AbfCase $record)
    {
        abort_unless($record->advisor_user_id === auth()->id(), 403);

        $payload = $request->input('payload', []);

        // Recalculate results
        $calculator = app(AbfCaseCalculator::class);
        $results = $calculator->calculate($payload);

        $record->update([
            'payload' => $payload,
            'results' => $results,
        ]);

        return response()->json(['ok' => true]);
    }
}
