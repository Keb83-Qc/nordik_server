<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewSubmissionAdmin;
use App\Services\LeadDispatcher;

class QuoteHomeChat extends Component
{
    public $step = 'occupancy';
    public $data = [];
    public Submission $submission;

    public $advisorCode;
    public $agentName = 'Julie';
    public $agentImage;

    public $occupancy;
    public $property_type;
    public $address;
    public $living_there;       // yes|no
    public $move_in_date;
    public $units_in_building;
    public $contents_amount;
    public $electric_baseboard; // yes|no
    public $supp_heating;       // yes|no

    public $years_insured;      // 0|1_2|3_5|6_10|11_plus
    public $years_with_insurer;
    public $current_insurer;

    public $first_name;
    public $last_name;
    public $gender;
    public $age;

    public $email;
    public $phone;
    public $phone_is_cell;      // yes|no

    public $marital_status;
    public $employment_status;
    public $education_level;
    public $industry;

    public $has_ia_products;    // yes|no

    public $consent_profile;    // accept|refuse
    public $consent_marketing;  // accept|refuse
    public $marketing_email;    // yes|no
    public $consent_credit;     // yes|no

    protected $stepOrder = [
        'occupancy'          => 'occupancy',
        'property_type'      => 'property_type',
        'identity'           => ['first_name', 'last_name', 'gender'],
        'address'            => 'address',
        'living_there'       => 'living_there',
        'move_in_date'       => 'move_in_date',      // conditionnel
        'units_in_building'  => 'units_in_building',
        'contents_amount'    => 'contents_amount',
        'electric_baseboard' => 'electric_baseboard',
        'supp_heating'       => 'supp_heating',
        'years_insured'      => 'years_insured',
        'years_with_insurer' => 'years_with_insurer',
        'current_insurer'    => 'current_insurer',
        'age'                => 'age',
        'email'              => 'email',
        'phone'              => 'phone',
        'phone_is_cell'      => 'phone_is_cell',
        'marital_status'     => 'marital_status',
        'employment_status'  => 'employment_status',
        'education_level'    => 'education_level',
        'industry'           => 'industry',
        'has_ia_products'    => 'has_ia_products',
        'consent_profile'    => 'consent_profile',
        'consent_marketing'  => 'consent_marketing',
        'marketing_email'    => 'marketing_email',   // conditionnel
        'consent_credit'     => 'consent_credit',
    ];

    public function mount(LeadDispatcher $dispatcher)
    {
        if (!session('has_consented')) {
            return redirect()->route('consent.show', [
                'locale' => app()->getLocale(),
                'code'   => session('current_advisor_code'),
            ]);
        }

        if (!session()->has('current_advisor_code')) {
            $assignedAdvisor = $dispatcher->assignAdvisor();
            if ($assignedAdvisor) session(['current_advisor_code' => $assignedAdvisor->advisor_code]);
        }

        $this->advisorCode = session('current_advisor_code');
        $advisor = User::where('advisor_code', $this->advisorCode)->first();

        if ($advisor) {
            $this->agentName  = $advisor->first_name;
            $this->agentImage = $advisor->image_url;
        } else {
            $this->agentImage = asset('assets/img/agent-default.jpg');
        }

        if (session()->has('current_submission_id')) {
            $submission = Submission::find(session('current_submission_id'));
            if ($submission) {
                $this->submission = $submission;
                $this->data = $submission->data ?? [];
                $this->fillPropertiesFromData();
                $this->calculateStep();
            }
        }
    }

    private function normalizeYesNo($v): string
    {
        $v = strtolower(trim((string)$v));
        return ($v === 'yes' || $v === 'oui') ? 'yes' : 'no';
    }

    private function normalizeYearsInsured($v): string
    {
        $v = trim((string)$v);
        return match ($v) {
            '0', 'zero' => '0',
            '1_2', '1-2', '1 à 2', '1 a 2' => '1_2',
            '3_5', '3-5', '3 à 5', '3 a 5' => '3_5',
            '6_10', '6-10', '6 à 10', '6 a 10' => '6_10',
            '11_plus', '11+', '11 et plus', '11 ans et plus' => '11_plus',
            default => '11_plus',
        };
    }

    public function calculateStep()
    {
        $this->step = 'final';

        foreach ($this->stepOrder as $stepName => $requiredFields) {
            $missing = false;

            if (is_array($requiredFields)) {
                foreach ($requiredFields as $field) {
                    if (!isset($this->data[$field]) || $this->data[$field] === '') {
                        $missing = true;
                        break;
                    }
                }
            } else {
                if (!isset($this->data[$requiredFields]) || $this->data[$requiredFields] === '') {
                    $missing = true;
                }
            }

            // ✅ move_in_date seulement si living_there = yes
            if ($stepName === 'move_in_date') {
                if (($this->data['living_there'] ?? null) === 'no') {
                    $missing = false;
                }
            }

            // ✅ marketing_email seulement si consent_marketing = accept
            if ($stepName === 'marketing_email') {
                if (($this->data['consent_marketing'] ?? null) !== 'accept') {
                    $missing = false;
                }
            }

            if ($missing) {
                $this->step = $stepName;
                break;
            }
        }
    }

    public function persist($key, $value)
    {
        $this->data[$key] = $value;

        if (!isset($this->submission)) {
            $this->submission = Submission::create([
                'type' => 'habitation',
                'advisor_code' => $this->advisorCode,
                'data' => $this->data,
            ]);

            // ✅ session unifiée
            session(['current_submission_id' => $this->submission->id]);
        } else {
            $this->submission->update(['data' => $this->data]);
        }

        $this->calculateStep();
        $this->dispatch('scroll-down');
    }

    public function save($field, $value)
    {
        // ✅ Normaliser yes/no
        if (in_array($field, [
            'living_there',
            'electric_baseboard',
            'supp_heating',
            'phone_is_cell',
            'has_ia_products',
            'marketing_email',
            'consent_credit',
        ], true)) {
            $value = $this->normalizeYesNo($value);
        }

        // ✅ Normaliser years_insured
        if ($field === 'years_insured') {
            $value = $this->normalizeYearsInsured($value);
        }

        // ✅ Normaliser consents
        if (in_array($field, ['consent_profile', 'consent_marketing'], true)) {
            $value = match (strtolower(trim((string)$value))) {
                'accept' => 'accept',
                'refuse' => 'refuse',
                default => $value,
            };
        }

        $this->persist($field, $value);
    }

    public function submitIdentity()
    {
        $this->validate([
            'first_name' => 'required|string|min:2|max:60',
            'last_name'  => 'required|string|min:2|max:60',
            'gender'     => 'required|string|in:homme,femme,autre,prefer_not',
        ]);

        $this->data['first_name'] = $this->first_name;
        $this->data['last_name']  = $this->last_name;
        $this->persist('gender', $this->gender);
    }

    public function submitAddress()
    {
        $this->validate(['address' => 'required|string|min:5|max:200']);
        $this->persist('address', $this->address);
    }

    public function submitMoveInDate()
    {
        $this->validate(['move_in_date' => 'required|date']);
        $this->persist('move_in_date', $this->move_in_date);
    }

    public function submitUnits()
    {
        $this->validate(['units_in_building' => 'required|integer|min:1|max:999']);
        $this->persist('units_in_building', $this->units_in_building);
    }

    public function submitContentsAmount()
    {
        $this->validate(['contents_amount' => 'required|integer|min:0|max:2000000']);
        $this->persist('contents_amount', $this->contents_amount);
    }

    public function submitYearsWithInsurer()
    {
        $this->validate(['years_with_insurer' => 'required|integer|min:0|max:100']);
        $this->persist('years_with_insurer', $this->years_with_insurer);
    }

    public function submitCurrentInsurer()
    {
        $this->validate(['current_insurer' => 'required|string|min:2|max:120']);
        $this->persist('current_insurer', $this->current_insurer);
    }

    public function submitAge()
    {
        $this->validate(['age' => 'required|integer|min:16|max:120']);
        $this->persist('age', $this->age);
    }

    public function submitEmail()
    {
        $this->validate(['email' => 'required|email|max:160']);
        $this->persist('email', $this->email);
    }

    public function submitPhone()
    {
        $this->validate(['phone' => 'required|string|min:10|max:30']);
        $this->persist('phone', $this->phone);
    }

    public function submitIndustry()
    {
        $this->validate(['industry' => 'required|string|min:2|max:120']);
        $this->persist('industry', $this->industry);
    }

    public function goToStep($name)
    {
        $this->step = $name;
        $this->dispatch('scroll-down');
    }

    private function fillPropertiesFromData()
    {
        foreach ($this->data as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }
    }

    public function finalize()
    {
        if (!isset($this->submission)) return;

        $recipients = array_filter([
            config('mail.submission_broker_to') ?: config('mail.from.address'),
            User::where('advisor_code', $this->advisorCode)->value('email'),
        ]);
        $recipients = array_values(array_unique($recipients));

        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new NewSubmissionAdmin($this->submission));
                Log::info("Soumission Habitation {$this->submission->id} envoyée à : " . implode(', ', $recipients));
            } catch (\Exception $e) {
                Log::error("Erreur Mail Soumission Habitation {$this->submission->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("Aucun destinataire pour Soumission Habitation {$this->submission->id}");
        }

        session(['last_advisor_code' => $this->advisorCode]);

        session()->forget([
            'current_submission_id',
            'current_submission_id_home',
            'current_advisor_code',
        ]);

        return redirect()->route('quote.success', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        return view('livewire.quote-home-chat');
    }
}
