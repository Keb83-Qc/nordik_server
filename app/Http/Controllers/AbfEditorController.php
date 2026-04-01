<?php

namespace App\Http\Controllers;

use App\Models\AbfAnnouncement;
use App\Models\AbfCase;
use App\Models\AbfParameter;
use App\Models\User;
use App\Services\AbfCaseCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbfEditorController extends Controller
{
    public function landing(string $advisorSlug = '')
    {
        $advisorSlug = $advisorSlug ?: (request()->route('advisorSlug') ?? auth()->user()?->slug ?? 'conseiller');
        $advisor = $this->resolveAdvisor($advisorSlug);

        try {
            $recentCases = AbfCase::where('advisor_user_id', $advisor->id)
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get(['id', 'slug', 'client_first_name', 'client_last_name', 'updated_at', 'status', 'results', 'payload']);
        } catch (\Throwable $e) {
            $recentCases = AbfCase::where('advisor_user_id', $advisor->id)
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get(['id', 'updated_at', 'status', 'results']);
        }

        try {
            $abfParams = AbfParameter::allAsMap();
        } catch (\Throwable $e) {
            $abfParams = [];
        }

        try {
            $seenIds = DB::table('abf_announcement_reads')
                ->where('user_id', auth()->id())
                ->pluck('announcement_id');

            $announcements = AbfAnnouncement::active()
                ->whereNotIn('id', $seenIds)
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->get(['id', 'title', 'body', 'published_at', 'created_at']);
        } catch (\Throwable $e) {
            $announcements = collect();
        }

        return view('abf.editor', [
            'record'        => null,
            'recentCases'   => $recentCases,
            'abfParams'     => $abfParams,
            'announcements' => $announcements,
            'advisorSlug'   => $advisor->slug,
        ]);
    }

    public function markAnnouncementSeen(string $advisorSlug = '', int $id = 0): JsonResponse
    {
        try {
            DB::table('abf_announcement_reads')->updateOrInsert(
                ['user_id' => auth()->id(), 'announcement_id' => $id],
                ['seen_at' => now()]
            );
        } catch (\Throwable $e) {
            // Table pas encore migrée — pas critique
        }

        return response()->json(['ok' => true]);
    }

    public function createJson(string $advisorSlug = '')
    {
        $advisorSlug = $advisorSlug ?: (request()->route('advisorSlug') ?? auth()->user()?->slug ?? 'conseiller');
        $advisorUser = $this->resolveAdvisor($advisorSlug);

        $record = AbfCase::create([
            'advisor_user_id' => $advisorUser->id,
            'advisor_code'    => $advisorUser->advisor_code,
            'payload'         => [],
            'results'         => [],
        ]);

        $identifier = 'nouveau-' . $record->id;

        return response()->json([
            'ok'      => true,
            'id'      => $record->id,
            'url'     => route('abf.editor.show', ['advisorSlug' => $advisorSlug, 'record' => $identifier]),
            'save_url'=> route('abf.editor.save', ['advisorSlug' => $advisorSlug, 'record' => $identifier]),
        ]);
    }

    public function create(string $advisorSlug = '')
    {
        $advisorSlug = $advisorSlug ?: (request()->route('advisorSlug') ?? auth()->user()?->slug ?? 'conseiller');
        $advisorUser = $this->resolveAdvisor($advisorSlug);

        $record = AbfCase::create([
            'advisor_user_id' => $advisorUser->id,
            'advisor_code'    => $advisorUser->advisor_code,
            'payload'         => [],
            'results'         => [],
        ]);

        return redirect()->route('abf.editor.show', [
            'advisorSlug' => $advisorSlug,
            'record'      => 'nouveau-' . $record->id,
        ]);
    }

    public function show(string $advisorSlug = '', string $record = '')
    {
        // Compatibilité cache de routes : si advisorSlug absent, record = premier param
        if ($record === '' && $advisorSlug !== '') {
            $record      = $advisorSlug;
            $advisorSlug = request()->route('advisorSlug') ?? auth()->user()?->slug ?? '';
        }
        $abfCase = $this->resolveRecord($record, $advisorSlug);
        $this->authorizeCase($abfCase);

        try {
            $abfParams = AbfParameter::allAsMap();
        } catch (\Throwable $e) {
            $abfParams = [];
        }

        return view('abf.editor', [
            'record'      => $abfCase,
            'abfParams'   => $abfParams,
            'advisorSlug' => $advisorSlug,
        ]);
    }

    public function save(Request $request, string $advisorSlug = '', string $record = '')
    {
        if ($record === '' && $advisorSlug !== '') {
            $record      = $advisorSlug;
            $advisorSlug = request()->route('advisorSlug') ?? auth()->user()?->slug ?? '';
        }
        $abfCase = $this->resolveRecord($record, $advisorSlug);
        $this->authorizeCase($abfCase);

        $payload = $request->input('payload', []);

        // Exiger au minimum le prénom ou le nom avant de sauvegarder
        $hasStep1 = ! empty($payload['client']['prenom']) || ! empty($payload['client']['nom']);
        if (! $hasStep1 && empty($abfCase->client_first_name)) {
            return response()->json(['ok' => false, 'reason' => 'step1_incomplete'], 422);
        }

        $calculator = app(AbfCaseCalculator::class);
        $results    = $calculator->calculate($payload);

        $progressPct = $results['progress']['percent'] ?? 0;
        $status = $progressPct >= 100 ? 'completed' : ($abfCase->status === 'signed' ? 'signed' : 'draft');

        $abfCase->update([
            'payload'           => $payload,
            'results'           => $results,
            'client_first_name' => $payload['client']['prenom'] ?? $abfCase->client_first_name,
            'client_last_name'  => $payload['client']['nom']    ?? $abfCase->client_last_name,
            'client_birth_date' => $this->parseDob($payload)    ?? $abfCase->client_birth_date,
            'progress_percent'  => $progressPct,
            'status'            => $status,
        ]);

        // Générer le slug si le nom vient d'être rempli et qu'on n'en a pas encore
        if (! $abfCase->slug && $abfCase->client_last_name) {
            $abfCase->generateSlug();
            $abfCase->refresh();
        }

        $caseIdentifier = $abfCase->slug ?: 'nouveau-' . $abfCase->id;

        return response()->json([
            'ok'      => true,
            'url'     => route('abf.editor.show', ['advisorSlug' => $advisorSlug, 'record' => $caseIdentifier]),
            'save_url'=> route('abf.editor.save', ['advisorSlug' => $advisorSlug, 'record' => $caseIdentifier]),
            'pdf_url' => route('abf.pdf', ['locale' => app()->getLocale(), 'abfCase' => $abfCase->id]),
        ]);
    }

    public function saveParams(Request $request, string $advisorSlug = '')
    {
        $data = $request->input('params', []);

        $allowed = [
            'fonds_urgence' => ['type', 'mois'],
            'deces'         => ['funerailles', 'rr_pct', 'rr_type', 'salaire_type', 'frequence'],
            'invalidite'    => ['type', 'rr_pct', 'salaire_type'],
            'maladie_grave' => ['niveau'],
            'retraite'      => ['rr_pct', 'frequence', 'calcul'],
            'hypotheses'    => ['inflation'],
            'portefeuilles' => ['prudent', 'modere', 'equilibre', 'croissance', 'audacieux'],
            'abf'           => ['province_defaut'],
        ];

        foreach ($allowed as $group => $keys) {
            foreach ($keys as $key) {
                if (isset($data[$group][$key])) {
                    AbfParameter::setValue($group, $key, $data[$group][$key]);
                }
            }
        }

        return response()->json(['ok' => true, 'params' => AbfParameter::allAsMap()]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Résout le conseiller depuis le slug de la route.
     * Admin/super_admin peuvent accéder à n'importe quel slug.
     */
    private function resolveAdvisor(string $slug): User
    {
        $user = auth()->user();
        if ($user->slug === $slug) {
            return $user;
        }
        if ($user->hasRoleByName(['admin', 'super_admin'])) {
            return User::where('slug', $slug)->firstOrFail();
        }
        abort(403);
    }

    /**
     * Vérifie que l'utilisateur connecté peut accéder au dossier ABF.
     */
    private function authorizeCase(AbfCase $case): void
    {
        $user = auth()->user();
        if ($case->advisor_user_id === $user->id) return;
        if ($user->hasRoleByName(['admin', 'super_admin'])) return;
        abort(403);
    }

    private function resolveRecord(string $identifier, string $advisorSlug = ''): AbfCase
    {
        if (preg_match('/^nouveau-(\d+)$/', $identifier, $m)) {
            return AbfCase::findOrFail($m[1]);
        }

        // Résolution par slug scopé au conseiller de la route
        $query = AbfCase::where('slug', $identifier);
        if ($advisorSlug) {
            $advisor = User::where('slug', $advisorSlug)->first();
            if ($advisor) {
                $query->where('advisor_user_id', $advisor->id);
            }
        } else {
            $query->where('advisor_user_id', auth()->id());
        }

        return $query->firstOrFail();
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
