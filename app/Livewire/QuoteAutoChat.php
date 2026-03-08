<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Submission;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewSubmissionAdmin;
use App\Services\LeadDispatcher;

class QuoteAutoChat extends Component
{
    public $step = 'year';
    public $data = [];
    public Submission $submission;

    // Agent
    public $advisorCode;
    public $agentName = 'Julie';
    public $agentImage;

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

    protected $stepOrder = [
        'year'             => 'year',
        'brand'            => 'brand',
        'model'            => 'model',
        'renewal_date'     => 'renewal_date',
        'usage'            => 'usage',
        'km_annuel'        => 'km_annuel',
        'address'          => 'address',
        'identity'         => ['first_name', 'last_name'],
        'age'              => 'age',
        'profession'       => 'profession',
        'existing_products' => 'existing_products',
        'email'            => 'email',
        'phone'            => 'phone',
        'license_number'   => 'license_number',
    ];

    public function mount(LeadDispatcher $dispatcher)
    {
        if (!session('has_consented')) {
            return redirect()->route('consent.show', [
                'locale' => app()->getLocale(),
                'code'   => session('current_advisor_code'),
            ]);
        }

        // conseiller (organique)
        if (!session()->has('current_advisor_code')) {
            $assignedAdvisor = $dispatcher->assignAdvisor();
            if ($assignedAdvisor) {
                session(['current_advisor_code' => $assignedAdvisor->advisor_code]);
            }
        }

        $this->advisorCode = session('current_advisor_code');
        $advisor = User::where('advisor_code', $this->advisorCode)->first();

        if ($advisor) {
            $this->agentName  = $advisor->first_name;
            $this->agentImage = $advisor->image_url;
        } else {
            $this->agentImage = asset('assets/img/VIP_Logo_Gold_Gradient10.png');
        }

        $this->brands = VehicleBrand::orderBy('name')->get();

        // ✅ session unifiée
        if (session()->has('current_submission_id')) {
            $submission = Submission::find(session('current_submission_id'));
            if ($submission) {
                $this->submission = $submission;
                $this->data = $submission->data ?? [];
                $this->fillPropertiesFromData();
                $this->calculateStep();
                return;
            }
        }
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

            if ($missing) {
                $this->step = $stepName;
                break;
            }
        }

        // reload models if needed
        if ($this->step === 'model' && !empty($this->data['brand_id'])) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->data['brand_id'])
                ->orderBy('name')
                ->get();
        }
    }

    public function persist($key, $value)
    {
        $this->data[$key] = $value;

        if (!isset($this->submission)) {
            $this->submission = Submission::create([
                'type' => 'auto',
                'advisor_code' => $this->advisorCode,
                'data' => $this->data
            ]);

            // ✅ session unifiée
            session(['current_submission_id' => $this->submission->id]);
        } else {
            $this->submission->update(['data' => $this->data]);
        }

        $this->calculateStep();
        $this->dispatch('scroll-down');
    }

    // Année
    public function updatedVehicleYear($val)
    {
        if (!empty($val)) $this->persist('year', $val);
    }

    // Marque
    public function updatedVehicleBrand($val)
    {
        $brand = VehicleBrand::find($val);
        if (!$brand) return;

        // ✅ persister les IDs + noms
        $this->data['brand_id'] = $brand->id;
        $this->vehicle_brand = $brand->id;

        // reset modèle
        unset($this->data['model'], $this->data['model_id']);
        $this->vehicle_model = null;

        $this->models = VehicleModel::where('vehicle_brand_id', $brand->id)
            ->orderBy('name')
            ->get();

        $this->persist('brand', $brand->name);
    }

    // Modèle
    public function updatedVehicleModel($val)
    {
        $model = VehicleModel::find($val);
        if (!$model) return;

        // ✅ persister model_id + nom
        $this->data['model_id'] = $model->id;
        $this->vehicle_model = $model->id;

        $this->persist('model', $model->name);
    }

    public function submitRenewalDate()
    {
        $this->validate(['renewal_date' => 'required|date']);
        $this->persist('renewal_date', $this->renewal_date);
    }

    public function save($field, $value)
    {
        // normalisation usage
        if ($field === 'usage') {
            $value = strtolower((string)$value);
            $value = ($value === 'commercial') ? 'commercial' : 'personnel';
        }

        $this->persist($field, $value);
    }

    public function setKm($val)
    {
        // valeurs stables (comme tu as déjà)
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
            'last_name' => 'required|string|min:2|max:60',
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

    public function submitLicense()
    {
        $val = !empty($this->license_number) ? $this->license_number : 'not_provided';
        $this->persist('license_number', $val);
    }

    public function skipLicense()
    {
        $this->persist('license_number', 'not_provided');
    }

    public function goToStep($name)
    {
        $this->step = $name;

        // hydratation modèles si retour modèle
        if ($name === 'model' && !empty($this->data['brand_id'])) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->data['brand_id'])
                ->orderBy('name')
                ->get();
        }

        $this->dispatch('scroll-down');
    }

    private function fillPropertiesFromData()
    {
        foreach ($this->data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        // mapping champs UI
        if (isset($this->data['year'])) $this->vehicle_year = $this->data['year'];
        if (isset($this->data['brand_id'])) $this->vehicle_brand = $this->data['brand_id'];
        if (isset($this->data['model_id'])) $this->vehicle_model = $this->data['model_id'];

        // précharger modèles si brand_id existe
        if (!empty($this->vehicle_brand)) {
            $this->models = VehicleModel::where('vehicle_brand_id', $this->vehicle_brand)
                ->orderBy('name')
                ->get();
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
                Log::info("Soumission Auto {$this->submission->id} envoyée à : " . implode(', ', $recipients));
            } catch (\Exception $e) {
                Log::error("Erreur Mail Soumission Auto {$this->submission->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("Aucun destinataire pour Soumission Auto {$this->submission->id}");
        }

        session(['last_advisor_code' => $this->advisorCode]);

        // ✅ nettoyage complet (inclut legacy)
        session()->forget([
            'current_submission_id',
            'current_submission_id_auto',
            'current_advisor_code',
        ]);

        return redirect()->route('quote.success', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        return view('livewire.quote-auto-chat');
    }
}
