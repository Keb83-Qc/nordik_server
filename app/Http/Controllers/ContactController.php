<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $sujet = $request->input('sujet', '');
        $objet_par_defaut   = '';
        $message_par_defaut = '';

        if ($sujet === 'avec_tarification') {
            $objet_par_defaut   = __('ContactController.subject_with_pricing');
            $message_par_defaut = __('ContactController.message_with_pricing');
        } elseif ($sujet === 'sans_tarification') {
            $objet_par_defaut   = __('ContactController.subject_without_pricing');
            $message_par_defaut = __('ContactController.message_without_pricing');
        } elseif (str_contains($sujet, 'Event_')) {
            $id = str_replace('Event_', '', $sujet);
            $objet_par_defaut = __('ContactController.subject_event', ['id' => $id]);
        }

        return view('pages.contact', [
            'objet_par_defaut'   => $objet_par_defaut,
            'message_par_defaut' => $message_par_defaut,
            'header_title'       => __('ContactController.header_title'),
            'header_subtitle'    => __('ContactController.header_subtitle'),
            'header_bg'          => asset('assets/img/contact/Entete-page-blog1.jpg'),
            'seo_title'          => __('ContactController.meta_title'),
            'seo_description'    => __('ContactController.header_subtitle'),
        ]);
    }

    public function send(Request $request)
    {
        // Honeypot rempli → bot
        if (!empty($request->input('website'))) {
            abort(422);
        }

        // Soumission trop rapide (< 3 secondes) → bot
        $formTime = (int) $request->input('form_time', 0);
        if ($formTime > 0 && (time() - $formTime) < 3) {
            abort(422);
        }

        // Validation
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email:rfc', 'max:255'],
            'subject'    => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $recipient = config('mail.contact_recipient', env('MAIL_CONTACT_TO', 'admin@vipgpi.ca'));

        try {
            Mail::to($recipient)->send(new ContactFormMail(
                senderName:  $data['first_name'],
                senderEmail: $data['email'],
                subject:     $data['subject'],
                messageBody: $data['message'],
            ));
        } catch (\Throwable $e) {
            Log::error('ContactForm mail failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return back()
                ->withInput()
                ->withErrors(['_send' => __('ContactController.error_send')]);
        }

        return back()->with('success', __('ContactController.success_sent'));
    }
}
