<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasChatSteps;
use App\Services\LeadDispatcher;
use Livewire\Component;

class QuoteHomeChat extends Component
{
    use HasChatSteps;

    public $occupancy;
    public $property_type;
    public $address;
    public $living_there;
    public $move_in_date;
    public $units_in_building;
    public $contents_amount;
    public $electric_baseboard;
    public $supp_heating;

    public $years_insured;
    public $years_with_insurer;
    public $current_insurer;

    public $first_name;
    public $last_name;
    public $gender;
    public $age;

    public $email;
    public $phone;
    public $phone_is_cell;

    public $marital_status;
    public $employment_status;
    public $education_level;
    public $industry;

    public $has_ia_products;

    public $consent_profile;
    public $consent_marketing;
    public $marketing_email;
    public $consent_credit;

    public $best_contact_time;
    public $hab_renewal_date;

    // ── Trait contract ──────────────────────────

    protected function chatType(): string { return 'habitation'; }
    protected function sessionKey(): string { return 'current_submission_id'; }
    protected function defaultAgentImage(): string { return asset('assets/img/agent-default.jpg'); }

    protected function validSteps(): array
    {
        return array_keys($this->stepOrder());
    }

    protected function stepOrder(): array
    {
        return [
            'occupancy'          => 'occupancy',
            'property_type'      => 'property_type',
            'hab_renewal_date'   => 'hab_renewal_date',
            'identity'           => ['first_name', 'last_name', 'gender'],
            'address'            => 'address',
            'living_there'       => 'living_there',
            'move_in_date'       => 'move_in_date',
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
            'best_contact_time'  => 'best_contact_time',
            'marital_status'     => 'marital_status',
            'employment_status'  => 'employment_status',
            'education_level'    => 'education_level',
            'industry'           => 'industry',
            'has_ia_products'    => 'has_ia_products',
            'consent_profile'    => 'consent_profile',
            'consent_marketing'  => 'consent_marketing',
            'marketing_email'    => 'marketing_email',
            'consent_credit'     => 'consent_credit',
        ];
    }

    protected function shouldSkipStep(string $step): bool
    {
        if ($step === 'move_in_date' && ($this->data['living_there'] ?? null) === 'no') {
            return true;
        }
        if ($step === 'marketing_email' && ($this->data['consent_marketing'] ?? null) !== 'accept') {
            return true;
        }
        return false;
    }

    protected function afterPersist(): void
    {
        $this->calculateStep();
    }

    protected function afterHydrate(): void
    {
        $this->calculateStep();
    }

    // ── Mount ───────────────────────────────────

    public function mount(LeadDispatcher $dispatcher)
    {
        $this->mountChat($dispatcher);
    }

    // ── Normalizers ─────────────────────────────

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

    // ── Step handlers ───────────────────────────

    public function save($field, $value)
    {
        if (in_array($field, [
            'living_there', 'electric_baseboard', 'supp_heating',
            'phone_is_cell', 'has_ia_products', 'marketing_email', 'consent_credit',
        ], true)) {
            $value = $this->normalizeYesNo($value);
        }

        if ($field === 'years_insured') {
            $value = $this->normalizeYearsInsured($value);
        }

        if (in_array($field, ['consent_profile', 'consent_marketing'], true)) {
            $value = match (strtolower(trim((string)$value))) {
                'accept' => 'accept',
                'refuse' => 'refuse',
                default  => $value,
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

    public function setBestContactTime($val)
    {
        if (!in_array($val, ['matin', 'apres_midi', 'soir', 'nimporte_quand'], true)) return;
        $this->persist('best_contact_time', $val);
    }

    public function submitHabRenewalDate()
    {
        $this->validate(['hab_renewal_date' => 'required|date']);
        $this->persist('hab_renewal_date', $this->hab_renewal_date);
    }

    public function submitIndustry()
    {
        $this->validate(['industry' => 'required|string|min:2|max:120']);
        $this->persist('industry', $this->industry);
    }

    public function render()
    {
        return view('livewire.quote-home-chat');
    }
}
