<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasChatSteps;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Services\LeadDispatcher;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class QuoteAutoChat extends Component
{
    use HasChatSteps;

    // Lists
    public $brands;
    public $models = [];

    // Inputs
    public $vehicle_year;
    public $vehicle_brand; // brand_id
    public $vehicle_model; // model_id
    public $renewal_date;
    public $usage;
    public $first_name;
    public $last_name;
    public $address;
    public $email;
    public $phone;
    public $age;
    public $license_number;
    public $profession;
    public $existing_products;
    public $best_contact_time;
    public $consent_profile;
    public $consent_marketing;
    public $marketing_email;
    public $consent_credit;

    // ── Trait contract ──────────────────────────

    protected function chatType(): string { return 'auto'; }
    protected function sessionKey(): string { return 'current_submission_id'; }
    protected function defaultAgentImage(): string { return asset('assets/img/VIP_Logo_Gold_Gradient10.png'); }

    protected function validSteps(): array
    {
        return array_keys($this->stepOrder());
    }

    protected function stepOrder(): array
    {
        return [
            'identity'          => ['first_name', 'last_name'],
            'age'               => 'age',
            'email'             => 'email',
            'phone'             => 'phone',
            'best_contact_time' => 'best_contact_time',
            'year'              => 'year',
            'brand'             => 'brand',
            'model'             => 'model',
            'renewal_date'      => 'renewal_date',
            'usage'             => 'usage',
            'km_annuel'         => 'km_annuel',
            'address'           => 'address',
            'existing_products' => 'existing_products',
            'profession'        => 'profession',
            'license_number'    => 'license_number',
            'consent_profile'   => 'consent_profile',
            'consent_marketing' => 'consent_marketing',
            'marketing_email'   => 'marketing_email',
            'consent_credit'    => 'consent_credit',
        ];
    }

    protected function shouldSkipStep(string $step): bool
    {
        // marketing_email ne s'affiche que si le consentement marketing est accepté
        if ($step === 'marketing_email') {
            return ($this->data['consent_marketing'] ?? null) !== 'accept';
        }
        return false;
    }

    protected function afterPersist(): void
    {
        $this->calculateStep();

        if ($this->step === 'model' && !empty($this->data['brand_id'])) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->data['brand_id'])
                ->orderBy('name')->get();
        }
    }

    protected function afterHydrate(): void
    {
        // Mapping champs UI
        if (isset($this->data['year']))     $this->vehicle_year  = $this->data['year'];
        if (isset($this->data['brand_id'])) $this->vehicle_brand = $this->data['brand_id'];
        if (isset($this->data['model_id'])) $this->vehicle_model = $this->data['model_id'];

        if (!empty($this->vehicle_brand)) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->vehicle_brand)
                ->orderBy('name')->get();
        }

        $this->calculateStep();
    }

    // ── Mount ───────────────────────────────────

    public function mount(LeadDispatcher $dispatcher)
    {
        $this->brands = Cache::remember('vehicle_brands', 3600, function () {
            return VehicleBrand::orderBy('name')->get();
        });

        $this->mountChat($dispatcher);
    }

    // ── Step handlers ───────────────────────────

    public function updatedVehicleYear($val)
    {
        if (!empty($val)) $this->persist('year', $val);
    }

    public function updatedVehicleBrand($val)
    {
        $brand = VehicleBrand::find($val);
        if (!$brand) return;

        $this->data['brand_id'] = $brand->id;
        $this->vehicle_brand    = $brand->id;

        unset($this->data['model'], $this->data['model_id']);
        $this->vehicle_model = null;

        $this->models = VehicleModel::where('vehicle_brand_id', $brand->id)
            ->orderBy('name')->get();

        $this->persist('brand', $brand->name);
    }

    public function updatedVehicleModel($val)
    {
        $model = VehicleModel::find($val);
        if (!$model) return;

        $this->data['model_id'] = $model->id;
        $this->vehicle_model    = $model->id;

        $this->persist('model', $model->name);
    }

    public function submitRenewalDate()
    {
        $this->validate(['renewal_date' => 'required|date']);
        $this->persist('renewal_date', $this->renewal_date);
    }

    public function save($field, $value)
    {
        if ($field === 'usage') {
            $value = strtolower((string)$value);
            $value = ($value === 'commercial') ? 'commercial' : 'personnel';
        }
        $this->persist($field, $value);
    }

    public function setKm($val)
    {
        $this->persist('km_annuel', $val);
    }

    public function submitAddress()
    {
        $this->validate(['address' => 'required|string|min:5|max:200']);
        $this->persist('address', $this->address);
    }

    public function submitIdentity()
    {
        $this->validate([
            'first_name' => 'required|string|min:2|max:60',
            'last_name'  => 'required|string|min:2|max:60',
        ]);

        $this->data['first_name'] = $this->first_name;
        $this->persist('last_name', $this->last_name);
    }

    public function submitAge()
    {
        $this->validate(['age' => 'required|integer|min:16|max:100']);
        $this->persist('age', $this->age);
    }

    public function submitProfession()
    {
        $this->validate(['profession' => 'required|string|min:2|max:120']);
        $this->persist('profession', $this->profession);
    }

    public function setExistingProducts($val)
    {
        if (!in_array($val, ['assurance', 'placement', 'both', 'none'], true)) return;
        $this->persist('existing_products', $val);
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

    public function submitLicense()
    {
        $val = !empty($this->license_number) ? $this->license_number : 'not_provided';
        $this->persist('license_number', $val);
    }

    public function skipLicense()
    {
        $this->persist('license_number', 'not_provided');
    }

    public function setConsentProfile($val)
    {
        if (!in_array($val, ['accept', 'refuse'], true)) return;
        $this->persist('consent_profile', $val);
    }

    public function setConsentMarketing($val)
    {
        if (!in_array($val, ['accept', 'refuse'], true)) return;
        $this->persist('consent_marketing', $val);
    }

    public function setMarketingEmail($val)
    {
        if (!in_array($val, ['yes', 'no'], true)) return;
        $this->persist('marketing_email', $val);
    }

    public function setConsentCredit($val)
    {
        if (!in_array($val, ['yes', 'no'], true)) return;
        $this->persist('consent_credit', $val);
    }

    public function goToStep(string $name): void
    {
        if (!in_array($name, $this->validSteps(), true)) return;

        $this->step = $name;

        if ($name === 'model' && !empty($this->data['brand_id'])) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->data['brand_id'])
                ->orderBy('name')->get();
        }

        $this->dispatch('scroll-down');
    }

    public function render()
    {
        return view('livewire.quote-auto-chat');
    }
}
