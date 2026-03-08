@php
use Illuminate\Support\Carbon;

/** Helpers */
$payload = (array) ($payload ?? []);
$client = (array) data_get($payload, 'client', []);
$spouse = (array) data_get($payload, 'spouse', []);
$hasSpouse = (bool) data_get($payload, 'has_spouse', false);

$totals = (array) data_get($results, 'totals', []);
$progress = (array) data_get($results, 'progress', []);

$fmtMoney = fn ($v) => '$' . number_format((float) $v, 0, '.', ' ');
$fmtMoney2 = fn ($v) => '$' . number_format((float) $v, 2, '.', ' ');
$age = function ($date) {
    if (blank($date)) return null;
    try { return Carbon::parse($date)->age; } catch (\Throwable) { return null; }
};

$maritalLabel = function (?string $key) {
    return [
        'single' => 'Célibataire',
        'common_law' => 'Conjoint de fait',
        'married' => 'Marié(e)',
        'divorced' => 'Divorcé(e)',
        'separated' => 'Séparé(e)',
        'widowed' => 'Veuf(ve)',
    ][$key ?? ''] ?? '—';
};

$citLabel = function (?string $key) {
    return [
        'canadian_citizen' => 'Citoyen(ne) canadien(ne)',
        'permanent_resident' => 'Résident(e) permanent(e)',
        'temporary_resident' => 'Résident(e) temporaire / permis',
        'other' => 'Autre',
    ][$key ?? ''] ?? '—';
};

$deps = (array) data_get($payload, 'dependents', []);
@endphp

<div class="space-y-10">

    {{-- KPI --}}
    <div>
        <h2 class="text-xl font-bold mb-4">Bilan (actifs / passifs)</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 rounded bg-gray-50 dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-300">Total actifs</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $fmtMoney(data_get($totals, 'assets_total', 0)) }}
                </div>
            </div>

            <div class="p-4 rounded bg-gray-50 dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-300">Total passifs</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $fmtMoney(data_get($totals, 'liabilities_total', 0)) }}
                </div>
            </div>

            <div class="p-4 rounded {{ (data_get($totals, 'net_worth', 0) >= 0) ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30' }}">
                <div class="text-sm text-gray-600 dark:text-gray-300">Valeur nette</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $fmtMoney(data_get($totals, 'net_worth', 0)) }}
                </div>
            </div>

            <div class="p-4 rounded bg-gray-50 dark:bg-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-300">Progression</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ (int) data_get($progress, 'percent', 0) }} %
                </div>
            </div>
        </div>
    </div>

    {{-- Profil client / conjoint --}}
    <div>
        <h2 class="text-xl font-bold mb-4">Profil</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="p-4 rounded bg-gray-50 dark:bg-gray-800">
                <div class="font-semibold mb-3">Client</div>
                <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                    <dt class="text-gray-600 dark:text-gray-300">Nom</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">
                        {{ trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')) ?: '—' }}
                    </dd>

                    <dt class="text-gray-600 dark:text-gray-300">Naissance</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">
                        {{ $client['birth_date'] ?? '—' }}
                        @if($age($client['birth_date'] ?? null) !== null)
                            <span class="text-gray-500">({{ $age($client['birth_date'] ?? null) }} ans)</span>
                        @endif
                    </dd>

                    <dt class="text-gray-600 dark:text-gray-300">État civil</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">{{ $maritalLabel($client['marital_status'] ?? null) }}</dd>

                    <dt class="text-gray-600 dark:text-gray-300">Adresse</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">
                        {{ $client['address'] ?? '—' }}
                        @if(!blank($client['postal_code'] ?? null))
                            <span class="text-gray-500">({{ $client['postal_code'] }})</span>
                        @endif
                    </dd>

                    <dt class="text-gray-600 dark:text-gray-300">Tél. dom.</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">{{ $client['home_phone'] ?? '—' }}</dd>

                    <dt class="text-gray-600 dark:text-gray-300">Courriel</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">{{ $client['email'] ?? '—' }}</dd>

                    <dt class="text-gray-600 dark:text-gray-300">Statut</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">{{ $citLabel($client['citizenship_status'] ?? null) }}</dd>

                    <dt class="text-gray-600 dark:text-gray-300">NAS</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">
                        @php $hs = data_get($client, 'has_sin'); @endphp
                        {{ $hs === null ? '—' : ((bool) $hs ? 'Oui' : 'Non') }}
                    </dd>

                    <dt class="text-gray-600 dark:text-gray-300">Travail CA</dt>
                    <dd class="col-span-2 text-gray-900 dark:text-white">{{ $client['work_in_canada_since'] ?? '—' }}</dd>
                </dl>
            </div>

            <div class="p-4 rounded bg-gray-50 dark:bg-gray-800">
                <div class="font-semibold mb-3">Conjoint</div>

                @if(! $hasSpouse)
                    <div class="text-sm text-gray-600 dark:text-gray-300">Aucun conjoint déclaré.</div>
                @else
                    <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                        <dt class="text-gray-600 dark:text-gray-300">Nom</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">
                            {{ trim(($spouse['first_name'] ?? '') . ' ' . ($spouse['last_name'] ?? '')) ?: '—' }}
                        </dd>

                        <dt class="text-gray-600 dark:text-gray-300">Naissance</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">
                            {{ $spouse['birth_date'] ?? '—' }}
                            @if($age($spouse['birth_date'] ?? null) !== null)
                                <span class="text-gray-500">({{ $age($spouse['birth_date'] ?? null) }} ans)</span>
                            @endif
                        </dd>

                        <dt class="text-gray-600 dark:text-gray-300">État civil</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">{{ $maritalLabel($spouse['marital_status'] ?? null) }}</dd>

                        <dt class="text-gray-600 dark:text-gray-300">Même adresse/tél.</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">
                            {{ (bool) data_get($payload, 'spouse.same_contact_as_client', false) ? 'Oui' : 'Non' }}
                        </dd>

                        <dt class="text-gray-600 dark:text-gray-300">Adresse</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">
                            {{ $spouse['address'] ?? '—' }}
                            @if(!blank($spouse['postal_code'] ?? null))
                                <span class="text-gray-500">({{ $spouse['postal_code'] }})</span>
                            @endif
                        </dd>

                        <dt class="text-gray-600 dark:text-gray-300">Tél. dom.</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">{{ $spouse['home_phone'] ?? '—' }}</dd>

                        <dt class="text-gray-600 dark:text-gray-300">Courriel</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">{{ $spouse['email'] ?? '—' }}</dd>

                        <dt class="text-gray-600 dark:text-gray-300">Statut</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">{{ $citLabel($spouse['citizenship_status'] ?? null) }}</dd>

                        <dt class="text-gray-600 dark:text-gray-300">Travail CA</dt>
                        <dd class="col-span-2 text-gray-900 dark:text-white">{{ $spouse['work_in_canada_since'] ?? '—' }}</dd>
                    </dl>
                @endif
            </div>

        </div>
    </div>

    {{-- Personnes à charge --}}
    <div>
        <h2 class="text-xl font-bold mb-4">Personnes à charge</h2>

        @if(count($deps) === 0)
            <div class="text-sm text-gray-600 dark:text-gray-300">Aucune personne à charge.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-3">Nom</th>
                            <th class="py-2 pr-3">Naissance</th>
                            <th class="py-2 pr-3">Âge</th>
                            <th class="py-2 pr-3">Lien</th>
                            <th class="py-2 pr-3">Dépendance</th>
                            <th class="py-2">Même adresse</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($deps as $d)
                            @php
                                $a = $age($d['birth_date'] ?? null);
                                $rel = [
                                    'child' => 'Enfant',
                                    'dependent' => 'Personne à charge',
                                    'other' => 'Autre',
                                ][$d['relationship'] ?? ''] ?? '—';
                                $dep = [
                                    'full' => 'Totale',
                                    'partial' => 'Partielle',
                                    'none' => 'Aucune',
                                ][$d['financial_dependency'] ?? ''] ?? '—';
                            @endphp
                            <tr>
                                <td class="py-2 pr-3 text-gray-900 dark:text-white">{{ $d['name'] ?? '—' }}</td>
                                <td class="py-2 pr-3 text-gray-900 dark:text-white">{{ $d['birth_date'] ?? '—' }}</td>
                                <td class="py-2 pr-3 text-gray-900 dark:text-white">{{ $a === null ? '—' : ($a . ' ans') }}</td>
                                <td class="py-2 pr-3 text-gray-900 dark:text-white">{{ $rel }}</td>
                                <td class="py-2 pr-3 text-gray-900 dark:text-white">{{ $dep }}</td>
                                <td class="py-2 text-gray-900 dark:text-white">
                                    {{ (bool) ($d['same_contact_as_client'] ?? false) ? 'Oui' : 'Non' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-xs text-gray-500 mt-2">
                * Pour les enfants mineurs, l'adresse est automatiquement considérée identique à celle du client si « Même adresse » est cochée.
            </div>
        @endif
    </div>

</div>
