<?php

namespace App\Http\Controllers;

use App\Mail\IntakeInviteMail;
use App\Models\AbfIntake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class IntakeController extends Controller
{
    // ─── PUBLIC ───────────────────────────────────────────────────────────────

    /**
     * Affiche le formulaire d'intake (ou la page de vérification du code).
     */
    public function show(string $advisorSlug, string $token)
    {
        $advisor = User::where('slug', $advisorSlug)->firstOrFail();
        $intake  = AbfIntake::where('token', $token)
            ->where('advisor_user_id', $advisor->id)
            ->firstOrFail();

        if ($intake->isExpired() && !$intake->isCompleted()) {
            return view('intake.expired', compact('advisor', 'intake'));
        }

        if ($intake->isCompleted()) {
            return view('intake.merci', compact('advisor', 'intake'));
        }

        $verified = session("intake_verified_{$token}", false);

        return view('intake.show', compact('advisor', 'intake', 'verified'));
    }

    /**
     * Vérifie le code d'accès et démarre le wizard.
     */
    public function verify(Request $request, string $advisorSlug, string $token)
    {
        $advisor = User::where('slug', $advisorSlug)->firstOrFail();
        $intake  = AbfIntake::where('token', $token)
            ->where('advisor_user_id', $advisor->id)
            ->firstOrFail();

        if (strtoupper(trim($request->access_code)) !== strtoupper($intake->access_code)) {
            return back()->withErrors(['access_code' => __('intake.wrong_code', [], $intake->locale)]);
        }

        session(["intake_verified_{$token}" => true]);

        // Marquer en cours si encore pending
        if ($intake->status === 'pending') {
            $intake->update(['status' => 'in_progress']);
        }

        return redirect()->route('intake.show', [
            'advisorSlug' => $advisorSlug,
            'token'       => $token,
        ]);
    }

    /**
     * Page de remerciement (accessible directement après soumission).
     */
    public function merci(string $advisorSlug, string $token)
    {
        $advisor = User::where('slug', $advisorSlug)->firstOrFail();
        $intake  = AbfIntake::where('token', $token)
            ->where('advisor_user_id', $advisor->id)
            ->firstOrFail();

        return view('intake.merci', compact('advisor', 'intake'));
    }

    // ─── AUTH ─────────────────────────────────────────────────────────────────

    /**
     * Génère un nouveau lien d'intake pour le conseiller.
     * Appelé via AJAX depuis la page liste-bilan.
     */
    public function create(Request $request, string $advisorSlug)
    {
        $advisor = User::where('slug', $advisorSlug)->firstOrFail();

        // Seul le conseiller propriétaire ou admin peut créer
        $user = auth()->user();
        if ($user->id !== $advisor->id && !$user->hasRoleByName(['admin', 'super_admin'])) {
            abort(403);
        }

        $request->validate([
            'client_first_name' => ['nullable', 'string', 'max:100'],
            'client_last_name'  => ['nullable', 'string', 'max:100'],
            'client_email'      => ['nullable', 'email', 'max:255'],
            'locale'            => ['nullable', 'string', 'max:5'],
        ]);

        $intake = AbfIntake::create([
            'advisor_user_id'   => $advisor->id,
            'token'             => Str::uuid()->toString(),
            'access_code'       => strtoupper(Str::random(3) . '-' . Str::random(3)),
            'client_first_name' => $request->client_first_name,
            'client_last_name'  => $request->client_last_name,
            'client_email'      => $request->client_email,
            'locale'            => $request->locale ?? 'fr',
            'status'            => 'pending',
        ]);

        $intake->load('advisor');

        $url        = $intake->url;
        $accessCode = $intake->access_code;
        $emailSent  = false;

        if ($intake->client_email) {
            try {
                Mail::to($intake->client_email)->send(new IntakeInviteMail($intake));
                $emailSent = true;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("IntakeInviteMail error: " . $e->getMessage());
            }
        }

        return response()->json([
            'url'         => $url,
            'access_code' => $accessCode,
            'email_sent'  => $emailSent,
        ]);
    }
}
