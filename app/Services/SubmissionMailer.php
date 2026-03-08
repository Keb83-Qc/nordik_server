<?php

namespace App\Services;

use App\Mail\NewSubmissionAdmin;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SubmissionMailer
{
    public static function sendSubmissionEmail(Submission $submission): void
    {
        $advisorEmail = !empty($submission->advisor_code)
            ? User::where('advisor_code', $submission->advisor_code)->value('email')
            : null;

        // ✅ IMPORTANT: utilise config() (chez toi env() retourne null en runtime)
        $brokerEmail = config('mail.submission_broker_to') ?: config('mail.from.address');

        $recipients = array_filter([$brokerEmail, $advisorEmail]);
        $recipients = array_values(array_unique($recipients));

        Mail::to($recipients)->send(new NewSubmissionAdmin($submission));
    }
}
