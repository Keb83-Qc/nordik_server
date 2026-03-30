<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    // ─── Setup : afficher le QR code ──────────────────────────────────────────

    public function setup(Request $request)
    {
        $user   = auth()->user();
        $google = app('pragmarx.google2fa');

        // Si déjà configuré et confirmé → rediriger
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            return redirect()->intended(route('abf.landing', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']));
        }

        // Générer un secret temporaire en session s'il n'existe pas encore
        if (! $request->session()->has('2fa_pending_secret')) {
            $request->session()->put('2fa_pending_secret', $google->generateSecretKey());
        }

        $secret = $request->session()->get('2fa_pending_secret');

        $qrUrl = $google->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Générer le SVG du QR code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $qrSvg = (new \BaconQrCode\Writer($renderer))->writeString($qrUrl);

        return view('auth.two-factor.setup', compact('secret', 'qrSvg'));
    }

    // ─── Enable : confirmer le code et sauvegarder ────────────────────────────

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = auth()->user();
        $google = app('pragmarx.google2fa');
        $secret = $request->session()->get('2fa_pending_secret');

        if (! $secret || ! $google->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Code invalide. Vérifiez votre application d\'authentification.']);
        }

        $user->two_factor_secret       = encrypt($secret);
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('2fa_pending_secret');
        $request->session()->put('2fa_verified', true);

        return redirect()->intended(route('abf.landing', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']))
            ->with('success', '2FA activé avec succès sur votre compte.');
    }

    // ─── Verify : afficher le formulaire de vérification ─────────────────────

    public function verify(Request $request)
    {
        $user = auth()->user();

        // Pas de 2FA configuré → setup
        if (! $user->two_factor_secret || ! $user->two_factor_confirmed_at) {
            return redirect()->route('2fa.setup');
        }

        return view('auth.two-factor.verify');
    }

    // ─── Check : valider le code TOTP ─────────────────────────────────────────

    public function check(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = auth()->user();
        $google = app('pragmarx.google2fa');

        try {
            $secret = decrypt($user->two_factor_secret);
        } catch (\Throwable) {
            return back()->withErrors(['code' => 'Erreur de configuration 2FA. Contactez un administrateur.']);
        }

        if (! $google->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Code invalide. Réessayez.']);
        }

        $request->session()->put('2fa_verified', true);

        return redirect()->intended(route('abf.landing', ['advisorSlug' => auth()->user()->slug ?? 'conseiller']));
    }

    // ─── Disable : désactiver le 2FA pour l'utilisateur ──────────────────────

    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = auth()->user();
        $google = app('pragmarx.google2fa');

        try {
            $secret = decrypt($user->two_factor_secret);
        } catch (\Throwable) {
            return back()->withErrors(['code' => 'Erreur de configuration.']);
        }

        if (! $google->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Code invalide.']);
        }

        $user->two_factor_secret       = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget('2fa_verified');

        return back()->with('success', '2FA désactivé sur votre compte.');
    }
}
