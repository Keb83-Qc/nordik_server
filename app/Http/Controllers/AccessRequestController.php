<?php

namespace App\Http\Controllers;

use App\Settings\EmailSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AccessRequestController extends Controller
{
    public function create(string $locale)
    {
        app()->setLocale($locale);
        return view('access-request');
    }

    public function store(Request $request, string $locale)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
        ]);

        // Envoi email admin
        Mail::raw(
            "Nouvelle demande d'accès:\n\nNom: {$request->name}\nEmail: {$request->email}\nTéléphone: {$request->phone}",
            function ($message) {
                $message->to(app(EmailSettings::class)->security_access_request_to)
                    ->subject(‘Nouvelle demande d\’accès’);
            }
        );

        return back()->with('success', 'Votre demande a été envoyée.');
    }
}
