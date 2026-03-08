<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $sujet = $request->input('sujet', '');
        $objet_par_defaut = "";
        $message_par_defaut = "";

        // Logique de pré-remplissage traduite
        if ($sujet === 'avec_tarification') {
            $objet_par_defaut = __('ContactController.subject_with_pricing');
            $message_par_defaut = __('ContactController.message_with_pricing');
        } elseif ($sujet === 'sans_tarification') {
            $objet_par_defaut = __('ContactController.subject_without_pricing');
            $message_par_defaut = __('ContactController.message_without_pricing');
        } elseif (str_contains($sujet, 'Event_')) {
            $id = str_replace('Event_', '', $sujet);
            // On passe le paramètre ID à la traduction
            $objet_par_defaut = __('ContactController.subject_event', ['id' => $id]);
        }

        return view('pages.contact', [
            'objet_par_defaut' => $objet_par_defaut,
            'message_par_defaut' => $message_par_defaut,
            'header_title' => __('ContactController.header_title'),
            'header_subtitle' => __('ContactController.header_subtitle'),
            'header_bg' => asset('assets/img/contact/Entete-page-blog1.jpg'),
            'title' => __('ContactController.meta_title')
        ]);
    }

    public function send(Request $request)
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

        // Ici vous ajouterez la logique d'envoi d'email plus tard
        // Mail::to('admin@vipgpi.ca')->send(new ContactForm($request->all()));

        return back()->with('success', __('ContactController.success_sent'));
    }
}
