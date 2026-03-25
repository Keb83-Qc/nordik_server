{{-- resources/views/livewire/quote-bundle-steps.blade.php --}}

@php
$common = $data['common'] ?? [];
$auto = $data['auto'] ?? [];
$hab = $data['habitation'] ?? [];

$t = function (string $key, string $fallback = '') {
$v = __($key);
return ($v === $key) ? $fallback : $v;
};

$norm = function ($v) {
if ($v === null) return null;
$v = trim((string)$v);
if ($v === 'Oui') return 'yes';
if ($v === 'Non') return 'no';
return $v;
};

$labelYesNo = function ($v) use ($t, $norm) {
$v = $norm($v);
return match ($v ?? '') {
'yes' => $t('bundlechat.btn_yes', 'Oui'),
'no' => $t('bundlechat.btn_no', 'Non'),
default => ($v === null || $v === '') ? '-' : $v,
};
};

$labelConsent = function ($v) use ($t, $labelYesNo, $norm) {
$v = $norm($v);
return match ($v ?? '') {
'accept' => $t('bundlechat.btn_accept', "J'accepte"),
'refuse' => $t('bundlechat.btn_refuse', 'Je refuse'),
'yes', 'no' => $labelYesNo($v),
default => ($v === null || $v === '') ? '-' : $v,
};
};

$labelGender = function ($v) use ($t) {
$map = [
'homme' => 'bundlechat.gender_homme',
'femme' => 'bundlechat.gender_femme',
'autre' => 'bundlechat.gender_autre',
'prefer_not' => 'bundlechat.gender_prefer_not',
];
if (!$v) return '-';
if (!isset($map[$v])) return $v;
return $t($map[$v], $v);
};

$labelUsage = function ($v) use ($t) {
$v = strtolower((string)$v);
return match ($v) {
'personnel' => $t('bundlechat.btn_personal', 'Personnel'),
'commercial' => $t('bundlechat.btn_commercial', 'Commercial'),
default => ($v === '') ? '-' : $v,
};
};

$labelOccupancy = function ($v) use ($t) {
return match ($v ?? '') {
'locataire' => $t('bundlechat.btn_tenant', 'Locataire'),
'proprietaire' => $t('bundlechat.btn_owner', 'Propriétaire'),
default => ($v === null || $v === '') ? '-' : $v,
};
};

$labelPropertyType = function ($v) use ($t) {
return match ($v ?? '') {
'maison' => $t('bundlechat.btn_house', 'Maison'),
'condo' => $t('bundlechat.btn_condo', 'Condo'),
'appartement' => $t('bundlechat.btn_apartment', 'Appartement'),
default => ($v === null || $v === '') ? '-' : $v,
};
};

$labelExistingProducts = function ($v) {
$key = 'bundlechat.products_' . ($v ?? '');
$translated = __($key);
if ($translated !== $key) return $translated;
return ($v === null || $v === '') ? '-' : $v;
};

$labelYearsInsured = function ($v) use ($t) {
// safe: 0|1_2|3_5|6_10|11_plus
$key = 'bundlechat.years_insured_' . ($v ?? '');
$translated = __($key);
if ($translated !== $key) return $translated;

// legacy fallback
return match($v ?? '') {
'0' => $t('bundlechat.years_insured_0', '0 an'),
'1-2' => $t('bundlechat.years_insured_1_2', '1 à 2 ans'),
'3-5' => $t('bundlechat.years_insured_3_5', '3 à 5 ans'),
'6-10' => $t('bundlechat.years_insured_6_10', '6 à 10 ans'),
'11+' => $t('bundlechat.years_insured_11_plus', '11 ans et plus'),
default => ($v === null || $v === '') ? '-' : $v,
};
};

$fmtMoney = function ($v) {
if ($v === null || $v === '') return '-';
return number_format((int)$v, 0, ',', ' ') . ' $';
};

// petits helpers de rendu
$show = function(string $stepName, $valueExists) use ($step) {
return $step === $stepName || $valueExists;
};

// Conditions hab
$living = $norm($hab['living_there'] ?? null); // yes/no
$ptype = strtolower(trim((string)($hab['property_type'] ?? ''))); // maison|condo|appartement
@endphp

{{-- =========================
   COMMON
========================= --}}

@php
$hasIdentity = !empty($common['first_name']) && !empty($common['last_name']) && !empty($common['gender']);
$identityAnswer = $hasIdentity
? trim($common['first_name'].' '.$common['last_name']).' — '.$labelGender($common['gender'])
: null;
@endphp

@if($show('common_identity', !empty($common)))
@include('livewire.partials.qa', [
'baseKey' => 'common_identity',
'questionText' => $this->getQuestion('common_identity'),
'answerText' => $identityAnswer,
'goTo' => 'common_identity',
'agentImage' => $agentImage,
])
@endif

@if($show('common_age', isset($common['gender'])))
@php $answer = isset($common['age']) ? ($common['age'].' '.$t('bundlechat.years_old','ans')) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => 'common_age',
'questionText' => $this->getQuestion('common_age'),
'answerText' => $answer,
'goTo' => 'common_age',
'agentImage' => $agentImage,
])
@endif

@if($show('common_email', isset($common['age'])))
@php $answer = isset($common['email']) ? (string)$common['email'] : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => 'common_email',
'questionText' => $this->getQuestion('common_email'),
'answerText' => $answer,
'goTo' => 'common_email',
'agentImage' => $agentImage,
])
@endif

@if($show('common_phone', isset($common['email'])))
@php $answer = isset($common['phone']) ? (string)$common['phone'] : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => 'common_phone',
'questionText' => $this->getQuestion('common_phone'),
'answerText' => $answer,
'goTo' => 'common_phone',
'agentImage' => $agentImage,
])
@endif

@if($show('common_best_contact_time', isset($common['phone'])))
@php
$answer = null;
if (isset($common['best_contact_time'])) {
    $ctKey = 'bundlechat.contact_time_' . $common['best_contact_time'];
    $ctLabel = __($ctKey);
    $answer = ($ctLabel === $ctKey) ? $common['best_contact_time'] : $ctLabel;
}
@endphp
@include('livewire.partials.qa', [
'baseKey' => 'common_best_contact_time',
'questionText' => $this->getQuestion('common_best_contact_time'),
'answerText' => $answer,
'goTo' => 'common_best_contact_time',
'agentImage' => $agentImage,
])
@endif

{{-- =========================
   PROFILE (dans habitation, avant auto)
========================= --}}

@php
$profileBeforeAuto = [
['marital_status', 'hab_marital_status', $this->getQuestion('hab_marital_status'), fn($v) => __($k="bundlechat.marital_{$v}") === $k ? (string)$v : __($k)],
['employment_status', 'hab_employment_status', $this->getQuestion('hab_employment_status'), fn($v) => __($k="bundlechat.employment_{$v}") === $k ? (string)$v : __($k)],
['education_level', 'hab_education_level', $this->getQuestion('hab_education_level'), fn($v) => __($k="bundlechat.education_{$v}") === $k ? (string)$v : __($k)],
['industry', 'hab_industry', $this->getQuestion('hab_industry'), fn($v) => (string)$v],
['has_ia_products', 'hab_has_ia_products', $this->getQuestion('hab_ia_products'), fn($v) => $labelYesNo($v)],
];
@endphp

@foreach($profileBeforeAuto as [$field, $stepName, $questionText, $fmt])
@if($show($stepName, isset($hab[$field])))
@php $answer = isset($hab[$field]) ? (string)$fmt($hab[$field]) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => $stepName,
'questionText' => $questionText,
'answerText' => $answer,
'goTo' => $stepName,
'agentImage' => $agentImage,
])
@endif
@endforeach

{{-- =========================
   AUTO - Section Title
========================= --}}

@php
$isAutoSection = str_starts_with($step, 'auto_') || !empty($auto);
@endphp

@if($isAutoSection)
<div class="messages__item" wire:key="sec-auto">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon">
            <img src="{{ $agentImage }}" onerror="this.src='{{ asset('assets/img/agent-default.jpg') }}'">
        </div>
        <div class="agent-msg"><strong>🚗 {{ __('bundlechat.section_auto') }}</strong></div>
    </div>
</div>
@endif

@php
$autoFields = [
['year', 'auto_year', $this->getQuestion('auto_year'), fn($v) => (string)$v],
['brand', 'auto_brand', $this->getQuestion('auto_brand'), fn($v) => (string)$v],
['model', 'auto_model', $this->getQuestion('auto_model'), fn($v) => (string)$v],
['renewal_date', 'auto_renewal_date', $this->getQuestion('auto_renewal'), fn($v) => (string)$v],
['usage', 'auto_usage', $this->getQuestion('auto_usage'), fn($v) => $labelUsage($v)],
['km_annuel', 'auto_km_annuel', $this->getQuestion('auto_km'), fn($v) => (string)$v],
['existing_products', 'auto_existing_products', $this->getQuestion('auto_existing_products'), fn($v) => $labelExistingProducts($v)],
['license_number', 'auto_license_number', $this->getQuestion('auto_license'), fn($v) => ($v === 'not_provided' ? $t('bundlechat.not_provided','Non fourni') : (string)$v)],
];
@endphp

@foreach($autoFields as [$field, $stepName, $questionText, $fmt])
@if($show($stepName, isset($auto[$field])))
@php $answer = isset($auto[$field]) ? (string)$fmt($auto[$field]) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => $stepName,
'questionText' => $questionText,
'answerText' => $answer,
'goTo' => $stepName,
'agentImage' => $agentImage,
])
@endif
@endforeach

{{-- =========================
   HABITATION - Section Title
========================= --}}

@php
// Steps "profil" (posés avant auto) -> ne doivent PAS déclencher la section habitation
$profileSteps = [
'hab_marital_status',
'hab_employment_status',
'hab_education_level',
'hab_industry',
'hab_has_ia_products',
];

// Clés "habitation réelle" (ce qui correspond à la section 🏠)
$habCoreKeys = [
'occupancy',
'property_type',
'renewal_date',
'address',
'living_there',
'move_in_date',
'units_in_building',
'contents_amount',
'electric_baseboard',
'supp_heating',
'years_insured',
'years_with_insurer',
'current_insurer',
'consent_profile',
'consent_marketing',
'marketing_email',
'consent_credit',
];

// On affiche la section habitation si :
// - on est sur un step hab_ qui n'est pas un step profil
// OU
// - on a déjà au moins une réponse d'habitation "réelle"
$isHabSection = (
str_starts_with($step, 'hab_')
&& !in_array($step, $profileSteps, true)
) || (
!empty(array_intersect(array_keys($hab ?? []), $habCoreKeys))
);
@endphp

@if($isHabSection)
<div class="messages__item" wire:key="sec-home">
    <div class="messages__wrapper">
        <div class="agent-avatar__icon">
            <img src="{{ $agentImage }}" onerror="this.src='{{ asset('assets/img/agent-default.jpg') }}'">
        </div>
        <div class="agent-msg"><strong>🏠 {{ __('bundlechat.section_home') }}</strong></div>
    </div>
</div>
@endif

{{-- Hab fields (hors conditionnels) --}}
@php
$habFieldsTop = [
['occupancy', 'hab_occupancy', $this->getQuestion('hab_occupancy'), fn($v) => $labelOccupancy($v)],
['property_type', 'hab_property_type', $this->getQuestion('hab_property_type'), fn($v) => $labelPropertyType($v)],
['renewal_date', 'hab_renewal_date', $this->getQuestion('hab_renewal_date'), fn($v) => (string)$v],
['address', 'hab_address', $this->getQuestion('hab_address'), fn($v) => (string)$v],
['living_there', 'hab_living_there', $this->getQuestion('hab_living_there'), fn($v) => $labelYesNo($v)],
];
@endphp

@foreach($habFieldsTop as [$field, $stepName, $questionText, $fmt])
@if($show($stepName, isset($hab[$field])))
@php $answer = isset($hab[$field]) ? (string)$fmt($hab[$field]) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => $stepName,
'questionText' => $questionText,
'answerText' => $answer,
'goTo' => $stepName,
'agentImage' => $agentImage,
])
@endif
@endforeach

{{-- ✅ move_in_date : seulement si living_there = yes --}}
@if($living === 'yes' && $show('hab_move_in_date', isset($hab['move_in_date'])))
@php $answer = isset($hab['move_in_date']) ? (string)$hab['move_in_date'] : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => 'hab_move_in_date',
'questionText' => $this->getQuestion('hab_years_at_address'),
'answerText' => $answer,
'goTo' => 'hab_move_in_date',
'agentImage' => $agentImage,
])
@endif

{{-- ✅ units_in_building : seulement si property_type != maison --}}
@if($ptype !== 'maison' && $show('hab_units_in_building', isset($hab['units_in_building'])))
@php $answer = isset($hab['units_in_building']) ? (string)$hab['units_in_building'] : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => 'hab_units_in_building',
'questionText' => $this->getQuestion('hab_units_in_building'),
'answerText' => $answer,
'goTo' => 'hab_units_in_building',
'agentImage' => $agentImage,
])
@endif

@php
$habFieldsBottom = [
['contents_amount', 'hab_contents_amount', $this->getQuestion('hab_contents_amount'), fn($v) => $fmtMoney($v)],
['electric_baseboard', 'hab_electric_baseboard', $this->getQuestion('hab_electric_baseboard'), fn($v) => $labelYesNo($v)],
['supp_heating', 'hab_supp_heating', $this->getQuestion('hab_supp_heating'), fn($v) => $labelYesNo($v)],
['years_insured', 'hab_years_insured', $this->getQuestion('hab_years_insured'), fn($v) => $labelYearsInsured($v)],
['years_with_insurer', 'hab_years_with_insurer', $this->getQuestion('hab_years_with_insurer'), fn($v) => (string)$v],
['current_insurer', 'hab_current_insurer', $this->getQuestion('hab_current_insurer'), fn($v) => (string)$v],
];
@endphp

@foreach($habFieldsBottom as [$field, $stepName, $questionText, $fmt])
@if($show($stepName, isset($hab[$field])))
@php $answer = isset($hab[$field]) ? (string)$fmt($hab[$field]) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => $stepName,
'questionText' => $questionText,
'answerText' => $answer,
'goTo' => $stepName,
'agentImage' => $agentImage,
])
@endif
@endforeach

{{-- CONSENTEMENTS --}}
@php
$habConsents = [
['consent_profile', 'hab_consent_profile', $this->getQuestion('common_consent_profile')],
['consent_marketing', 'hab_consent_marketing', $this->getQuestion('common_consent_marketing')],
];

// marketing_email seulement si consent_marketing = accept
if (($hab['consent_marketing'] ?? null) === 'accept') {
$habConsents[] = ['marketing_email', 'hab_marketing_email', $this->getQuestion('common_marketing_email')];
}

$habConsents[] = ['consent_credit', 'hab_consent_credit', $this->getQuestion('hab_consent_credit')];
@endphp

@foreach($habConsents as [$field, $stepName, $questionText])
@if($show($stepName, isset($hab[$field])))
@php $answer = isset($hab[$field]) ? (string)$labelConsent($hab[$field]) : null; @endphp
@include('livewire.partials.qa', [
'baseKey' => $stepName,
'questionText' => $questionText,
'answerText' => $answer,
'goTo' => $stepName,
'agentImage' => $agentImage,
])
@endif
@endforeach