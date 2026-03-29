<div class="space-y-4 p-1">

    @php
        $props = $activity->properties->toArray();
        $attrs    = $props['attributes'] ?? [];
        $old      = $props['old'] ?? [];
        $hasChanges = ! empty($attrs) && ! empty($old);
    @endphp

    {{-- Résumé --}}
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
            <span class="font-semibold text-gray-500">Date :</span>
            <span>{{ $activity->created_at->locale('fr')->isoFormat('D MMMM YYYY [à] H[h]mm:ss') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-500">Action :</span>
            <span>{{ match($activity->event) { 'created' => 'Créé', 'updated' => 'Modifié', 'deleted' => 'Supprimé', default => $activity->event ?? '—' } }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-500">Par :</span>
            <span>
                @if($activity->causer)
                    {{ trim(($activity->causer->first_name ?? '') . ' ' . ($activity->causer->last_name ?? '')) ?: $activity->causer->email }}
                @else
                    Système
                @endif
            </span>
        </div>
        <div>
            <span class="font-semibold text-gray-500">Objet :</span>
            <span>{{ class_basename($activity->subject_type ?? '—') }} #{{ $activity->subject_id ?? '?' }}</span>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- Tableau avant / après --}}
    @if($hasChanges)
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                    <th class="py-2 pr-4 font-semibold w-1/3">Champ</th>
                    <th class="py-2 pr-4 font-semibold w-1/3">Avant</th>
                    <th class="py-2 font-semibold w-1/3">Après</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($attrs as $field => $newVal)
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-700 dark:text-gray-300">{{ $field }}</td>
                        <td class="py-2 pr-4 text-red-600 dark:text-red-400 line-through opacity-75">
                            {{ is_array($old[$field] ?? null) ? json_encode($old[$field]) : ($old[$field] ?? '—') }}
                        </td>
                        <td class="py-2 text-green-700 dark:text-green-400 font-medium">
                            {{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? '—') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(! empty($attrs))
        {{-- Création : juste les nouvelles valeurs --}}
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                    <th class="py-2 pr-4 font-semibold">Champ</th>
                    <th class="py-2 font-semibold">Valeur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($attrs as $field => $val)
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-700 dark:text-gray-300">{{ $field }}</td>
                        <td class="py-2 text-gray-900 dark:text-gray-100">
                            {{ is_array($val) ? json_encode($val) : ($val ?? '—') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-400 text-sm text-center py-4">Aucun détail disponible.</p>
    @endif
</div>
