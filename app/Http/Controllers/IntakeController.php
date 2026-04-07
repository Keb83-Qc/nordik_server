<?php

namespace App\Http\Controllers;

use App\Mail\IntakeInviteMail;
use App\Models\AbfIntake;
use App\Models\SystemLog;
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

        $sessionKey    = "intake_verified_{$token}";
        $sessionValue  = session($sessionKey, false);
        $statusOk      = $intake->status === 'in_progress';
        $verified      = $sessionValue || $statusOk;

        SystemLog::record('debug', "[intake.show] token={$token}", [
            'intake_id'     => $intake->id,
            'status'        => $intake->status,
            'session_key'   => $sessionKey,
            'session_value' => $sessionValue,
            'status_ok'     => $statusOk,
            'verified'      => $verified,
            'session_id'    => session()->getId(),
            'ip'            => request()->ip(),
            'user_agent'    => mb_substr(request()->userAgent() ?? '', 0, 200),
        ], SystemLog::SOURCE_PUBLIC);

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

        $entered = strtoupper(trim($request->access_code ?? ''));
        $stored  = strtoupper($intake->access_code);

        if ($entered !== $stored) {
            SystemLog::record('warning', "[intake.verify] code incorrect token={$token}", [
                'intake_id'    => $intake->id,
                'entered_len'  => strlen($entered),
                'stored_len'   => strlen($stored),
                'ip'           => request()->ip(),
                'user_agent'   => mb_substr(request()->userAgent() ?? '', 0, 200),
            ], SystemLog::SOURCE_PUBLIC);

            $msg = match($intake->locale) {
                'en'    => 'Invalid access code. Please check the code in your email.',
                'es'    => 'Código de acceso incorrecto. Verifique el código en su correo.',
                'ht'    => 'Kòd aksè a pa bon. Verifye kòd la nan imèl ou.',
                default => 'Code d\'accès incorrect. Vérifiez le code dans votre courriel.',
            };
            return back()->withErrors(['access_code' => $msg]);
        }

        session(["intake_verified_{$token}" => true]);

        // Marquer en cours si encore pending
        $statusBefore = $intake->status;
        if ($intake->status === 'pending') {
            $intake->update(['status' => 'in_progress']);
        }

        SystemLog::record('info', "[intake.verify] code accepté token={$token}", [
            'intake_id'      => $intake->id,
            'status_before'  => $statusBefore,
            'status_after'   => $intake->fresh()->status,
            'session_id'     => session()->getId(),
            'ip'             => request()->ip(),
            'user_agent'     => mb_substr(request()->userAgent() ?? '', 0, 200),
        ], SystemLog::SOURCE_PUBLIC);

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

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    /**
     * Génère un code d'accès 6 caractères sans caractères ambigus.
     * Exclut : 0/O, 1/I/L pour éviter les confusions visuelles dans les emails.
     */
    private static function generateAccessCode(): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            if ($i === 3) $code .= '-';
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
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
            'access_code'       => self::generateAccessCode(),
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
