<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __('Un lien de réinitialisation a été envoyé à votre adresse courriel.'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __('Aucun compte trouvé avec cette adresse courriel.')]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login', ['locale' => app()->getLocale()])
                ->with('status', __('Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __('Ce lien de réinitialisation est invalide ou expiré.')]);
    }
}
