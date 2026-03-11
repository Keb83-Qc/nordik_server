<?php

namespace App\Livewire;

use App\Domain\QuoteBundle\QuoteBundleData;
use App\Domain\QuoteBundle\StepEngine;
use App\Domain\QuoteBundle\StepRegistry;
use App\Domain\QuoteBundle\StepValidation;
use App\Livewire\Concerns\HasChatSteps;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Services\LeadDispatcher;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class QuoteBundleChat extends Component
{
    use HasChatSteps {
        // Bundle override persist/calculateStep car il utilise le DTO bucketed
        persist as traitPersist;
        calculateStep as traitCalculateStep;
    }

    public ?string $editStep = null;

    public $brands;
    public $models = [];

    // COMMON
    public $first_name;
    public $last_name;
    public $gender;
    public $age;
    public $email;
    public $phone;
    public $best_contact_time;

    // AUTO
    public $vehicle_year;
    public $vehicle_brand;
    public $vehicle_model;
    public $renewal_date;
    public $usage;
    public $km_annuel;
    public $license_number;
    public $existing_products;

    // HABITATION
    public $occupancy;
    public $property_type;
    public $hab_renewal_date;
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

    // Profile
    public $marital_status;
    public $employment_status;
    public $education_level;
    public $industry;
    public $has_ia_products;

    // Consents
    public $consent_profile;
    public $consent_marketing;
    public $marketing_email;
    public $consent_credit;

    // ── Trait contract ──────────────────────────

    protected function chatType(): string { return 'bundle'; }
    protected function sessionKey(): string { return 'current_submission_id_bundle'; }
    protected function defaultAgentImage(): string { return asset('assets/img/agent-default.jpg'); }

    protected function validSteps(): array
    {
        return array_merge(
            array_keys(StepRegistry::common()),
            array_keys(StepRegistry::profile()),
            array_keys(StepRegistry::auto()),
            array_keys(StepRegistry::habitation()),
            ['final']
        );
    }

    protected function stepOrder(): array
    {
        // Bundle utilise StepEngine, pas stepOrder
        return [];
    }

    protected function afterPersist(): void
    {
        // handled by persistDto
    }

    protected function afterHydrate(): void
    {
        $this->data = $this->dto()->toArray();
        $this->fillFromData();
        $this->calculateStep();
        $this->hydrateForStep($this->step);
    }

    // ── Mount ───────────────────────────────────

    public function mount(LeadDispatcher $dispatcher)
    {
        $this->data = [
            'common' => [],
            'auto' => [],
            'habitation' => [],
            'meta' => [],
        ];

        $this->brands = Cache::remember('vehicle_brands', 3600, function () {
            return VehicleBrand::orderBy('name')->get();
        });

        $this->mountChat($dispatcher);

        // Si aucune soumission existante, en créer une
        if (!isset($this->submission)) {
            $this->submission = \App\Models\Submission::create([
                'type' => 'bundle',
                'advisor_code' => $this->advisorCode,
                'data' => $this->data,
            ]);
            session([$this->sessionKey() => $this->submission->id]);
            $this->calculateStep();
            $this->hydrateForStep($this->step);
        }
    }

    // ── Bundle-specific: DTO & Engine ───────────

    private function dto(): QuoteBundleData
    {
        return QuoteBundleData::fromArray($this->data);
    }

    private function engine(): StepEngine
    {
        return StepEngine::makeDefault();
    }

    public function calculateStep(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $prev = $this->step;
            $this->step = $this->engine()->nextStep($this->dto());
            $this->guardStep();
            if ($this->step === $prev) break;
        }
    }

    private function guardStep(): void
    {
        $hab = $this->data['habitation'] ?? [];
        $living = $hab['living_there'] ?? null;
        $ptype  = $hab['property_type'] ?? null;

        if ($this->step === 'hab_move_in_date' && $living === 'yes') {
            $this->step = ($ptype && $ptype !== 'maison')
                ? 'hab_units_in_building'
                : 'hab_contents_amount';
            return;
        }
        if ($this->step === 'hab_units_in_building' && $ptype === 'maison') {
            $this->step = 'hab_contents_amount';
            return;
        }
        if ($this->step === 'hab_marketing_email' && (($hab['consent_marketing'] ?? null) !== 'accept')) {
            $this->step = 'hab_consent_credit';
            return;
        }
    }

    private function hydrateForStep(string $step): void
    {
        foreach ($this->engine()->needsHydration($step) as $need) {
            if ($need === 'auto_models_for_brand') {
                $brandId = $this->data['auto']['brand_id'] ?? $this->vehicle_brand ?? null;
                $this->models = $brandId
                    ? VehicleModel::where('vehicle_brand_id', $brandId)->orderBy('name')->get()
                    : [];
            }
        }
    }

    // ── Bundle persist (uses DTO, not flat) ─────

    private function persistDto(QuoteBundleData $dto): void
    {
        $this->data = $dto->toArray();
        $this->submission->update(['data' => $this->data]);

        $this->calculateStep();
        $this->hydrateForStep($this->step);
        $this->editStep = null;
        $this->dispatch('scroll-down');
    }

    private function persistField(string $bucket, string $key, mixed $value): void
    {
        $dto = $this->dto();
        $dto->set($bucket, $key, $value);
        $this->persistDto($dto);
    }

    private function persistMany(string $bucket, array $pairs): void
    {
        $dto = $this->dto();
        $dto->setMany($bucket, $pairs);
        $this->persistDto($dto);
    }

    private function fillFromData(): void
    {
        foreach (($this->data['common'] ?? []) as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
        foreach (($this->data['auto'] ?? []) as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
        foreach (($this->data['habitation'] ?? []) as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }

        if (!empty($this->data['auto']['year']))     $this->vehicle_year  = $this->data['auto']['year'];
        if (!empty($this->data['auto']['brand_id'])) $this->vehicle_brand = $this->data['auto']['brand_id'];
        if (!empty($this->data['auto']['model_id'])) $this->vehicle_model = $this->data['auto']['model_id'];
    }

    // ── GoToStep (override for editStep + hydration) ─

    public function goToStep(string $name): void
    {
        if (!in_array($name, $this->validSteps(), true)) return;

        $this->editStep = $name;
        $this->step = $name;
        $this->hydrateForStep($this->step);
        $this->dispatch('scroll-down');
    }

    // ── COMMON handlers ─────────────────────────

    public function submitCommonIdentity(): void
    {
        $this->validate(StepValidation::rules('common_identity'));
        $this->persistMany('common', [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'gender'     => $this->gender,
        ]);
    }

    public function submitCommonAge(): void
    {
        $this->validate(StepValidation::rules('common_age'));
        $this->persistField('common', 'age', $this->age);
    }

    public function submitCommonEmail(): void
    {
        $this->validate(StepValidation::rules('common_email'));
        $this->persistField('common', 'email', $this->email);
    }

    public function submitCommonPhone(): void
    {
        $this->validate(StepValidation::rules('common_phone'));
        $this->persistField('common', 'phone', $this->phone);
    }

    public function setCommonBestContactTime(string $val): void
    {
        if (!in_array($val, ['matin', 'apres_midi', 'soir', 'nimporte_quand'], true)) return;
        $this->persistField('common', 'best_contact_time', $val);
    }

    // ── AUTO handlers ───────────────────────────


    public function updatedVehicleYear($val): void
    {
        if (!empty($val)) $this->persistField('auto', 'year', $val);
    }

    public function updatedVehicleBrand($val): void
    {
        $brand = VehicleBrand::find($val);
        if (!$brand) return;

        $dto = $this->dto();
        unset($dto->auto['model'], $dto->auto['model_id']);
        $dto->auto['brand_id'] = $brand->id;
        $dto->auto['brand']    = $brand->name;

        $this->vehicle_model = null;
        $this->models = VehicleModel::where('vehicle_brand_id', $brand->id)
            ->orderBy('name')->get();

        $this->persistDto($dto);
    }

    public function updatedVehicleModel($val): void
    {
        $model = VehicleModel::find($val);
        if (!$model) return;

        $dto = $this->dto();
        $dto->auto['model_id'] = $model->id;
        $dto->auto['model']    = $model->name;
        $this->persistDto($dto);
    }

    public function submitAutoRenewalDate(): void
    {
        $this->validate(StepValidation::rules('auto_renewal_date'));
        $this->persistField('auto', 'renewal_date', $this->renewal_date);
    }

    public function saveAuto(string $field, string $value): void
    {
        if ($field === 'usage') {
            $value = strtolower($value);
            $value = ($value === 'commercial') ? 'commercial' : 'personnel';
        }
        $this->persistField('auto', $field, $value);
    }

    public function setAutoKm(string $val): void
    {
        $this->persistField('auto', 'km_annuel', $val);
    }

    public function setAutoExistingProducts(string $val): void
    {
        if (!in_array($val, ['assurance', 'placement', 'both', 'none'], true)) return;
        $this->persistField('auto', 'existing_products', $val);
    }

    public function submitAutoLicense(): void
    {
        $this->validate(StepValidation::rules('auto_license_number'));
        $val = !empty($this->license_number) ? $this->license_number : 'not_provided';
        $this->persistField('auto', 'license_number', $val);
    }

    public function skipAutoLicense(): void
    {
        $this->persistField('auto', 'license_number', 'not_provided');
    }

    // ── HABITATION handlers ─────────────────────

    public function saveHab(string $field, string $value): void
    {
        $this->persistField('habitation', $field, $value);
    }

    public function submitHabRenewalDate(): void
    {
        $this->validate(StepValidation::rules('hab_renewal_date'));
        $this->persistField('habitation', 'renewal_date', $this->hab_renewal_date);
    }

    public function submitHabAddress(): void
    {
        $this->validate(StepValidation::rules('hab_address'));
        $this->persistField('habitation', 'address', $this->address);
    }

    public function submitHabMoveInDate(): void
    {
        $this->validate(StepValidation::rules('hab_move_in_date'));
        $this->persistField('habitation', 'move_in_date', $this->move_in_date);
    }

    public function submitHabUnits(): void
    {
        $this->validate(StepValidation::rules('hab_units_in_building'));
        $this->persistField('habitation', 'units_in_building', $this->units_in_building);
    }

    public function submitHabContentsAmount(): void
    {
        $this->validate(StepValidation::rules('hab_contents_amount'));
        $this->persistField('habitation', 'contents_amount', $this->contents_amount);
    }

    public function submitHabYearsWithInsurer(): void
    {
        $this->validate(StepValidation::rules('hab_years_with_insurer'));
        $this->persistField('habitation', 'years_with_insurer', $this->years_with_insurer);
    }

    public function submitHabCurrentInsurer(): void
    {
        $this->validate(StepValidation::rules('hab_current_insurer'));
        $this->persistField('habitation', 'current_insurer', $this->current_insurer);
    }

    public function submitHabIndustry(): void
    {
        $this->validate(StepValidation::rules('hab_industry'));
        $this->persistField('habitation', 'industry', $this->industry);
    }

    public function render()
    {
        return view('livewire.quote-bundle-chat');
    }
}
