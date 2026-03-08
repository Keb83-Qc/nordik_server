@php
/** @var \App\Models\User|null $record */
$user = $record;

if (! $user) {
echo "<div class='text-sm text-slate-400'>Aucun utilisateur chargé.</div>";
return;
}

$url = $user->image_url ?? asset('assets/img/VIP_Logo_Gold_Gradient10.png');

$title = $user->title?->name ?? null;
if (is_array($title)) {
$locale = app()->getLocale();
$title = $title[$locale] ?? $title['fr'] ?? $title['en'] ?? '';
}

$role = $user->role?->name ?? '';
@endphp

<div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-white/0 p-5 shadow-sm">
    <div class="flex items-center gap-4">
        <div class="relative">
            <div class="h-20 w-20 rounded-full ring-2 ring-white/10 bg-white/5 overflow-hidden">
                <img
                    src="{{ $url }}"
                    alt="Avatar"
                    class="h-full w-full object-cover"
                    onerror="this.src='{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}'" />
            </div>
            <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-emerald-500 ring-4 ring-slate-950/60"></div>
        </div>

        <div class="min-w-0">
            <div class="text-base font-semibold text-white truncate">
                {{ $user?->full_name ?? '—' }}
            </div>

            @if(!empty($title))
            <div class="text-sm text-slate-300 truncate">
                {{ $title }}
            </div>
            @endif

            <div class="mt-2 flex flex-wrap gap-2">
                @if(!empty($role))
                <span class="inline-flex items-center rounded-full bg-white/5 px-2.5 py-1 text-xs text-slate-200 ring-1 ring-white/10">
                    {{ $role }}
                </span>
                @endif

                @if(!empty($user?->city))
                <span class="inline-flex items-center rounded-full bg-white/5 px-2.5 py-1 text-xs text-slate-200 ring-1 ring-white/10">
                    📍 {{ $user->city }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4 text-xs text-slate-400">
        Conseil: utilise une photo carrée (min 600×600). On recadre automatiquement en 1:1.
    </div>
</div>