<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message; // <--- INDISPENSABLE pour la messagerie
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     *
     * Affiche la page de connexion
     */
    public function showLogin()
    {
        // Assurez-vous que le fichier resources/views/login.blade.php existe
        return view('auth.login');
    }

    /**
     * Gère la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Identifiants invalides.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) ($user->role_id ?? 0) === 6) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login', ['locale' => app()->getLocale()])
                ->withErrors(['email' => 'Votre compte est en attente de validation.']);
        }

        $isAdmin = method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['admin', 'super_admin'])
            : in_array((int) ($user->role_id ?? 0), [1, 2], true);

        $isAbf = method_exists($user, 'hasRole')
            ? $user->hasRole('abf')
            : false;

        $locale = app()->getLocale();

        $target = $isAdmin
            ? "/{$locale}/admin"
            : ($isAbf ? "/{$locale}/abf" : "/{$locale}/conseiller");

        return redirect()->intended($target);
    }

    /**
     * Gère l'inscription AJAX et envoie le message interne
     */
    public function registerAjax(Request $request)
    {
        // Honeypot rempli => bot
        if (!empty($request->input('website'))) {
            abort(422);
        }

        // Soumission trop rapide (< 3 secondes) => bot
        $formTime = (int) $request->input('form_time', 0);
        if ($formTime > 0 && (time() - $formTime) < 3) {
            abort(422);
        }

        // 1. Validation
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email:rfc,dns|max:255|unique:users,email',
            'phone'      => ['required', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]+$/'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                // --- GESTION DE LA POSITION (OPTIONNEL) ---
                $lastPosition = User::max('position');
                // ------------------------------------------

                // 2. CRÉATION DU CANDIDAT
                $password = Str::random(12);

                $newUser = User::create([
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                    'password'   => Hash::make($password),
                    'role_id'    => 6,
                    'position'   => 0,
                ]);

                // 3. ENVOI DU MESSAGE AUX SUPER ADMINS ET ADMINS
                $recipient = User::whereIn('role_id', [1, 2])
                    ->orderBy('role_id')
                    ->orderBy('id')
                    ->first();

                if ($recipient) {
                    Message::create([
                        'sender_id'   => $newUser->id,
                        'receiver_id' => $recipient->id,
                        'subject'     => 'Nouvelle inscription : ' . $newUser->first_name . ' ' . $newUser->last_name,
                        'body'        => "Une nouvelle demande d'inscription a été reçue.<br><br>" .
                            "<strong>Nom :</strong> {$newUser->last_name}<br>" .
                            "<strong>Prénom :</strong> {$newUser->first_name}<br>" .
                            "<strong>Email :</strong> {$newUser->email}<br>" .
                            "<strong>Téléphone :</strong> {$newUser->phone}<br><br>" .
                            "Merci de valider ou refuser ce compte ci-dessous.",
                        'is_read'    => false,
                        'created_at' => now(),
                        'data'       => [
                            'action_type'  => 'registration_request',
                            'applicant_id' => $newUser->id,
                        ],
                    ]);
                }
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }
}
