@php
$user = \Filament\Facades\Filament::auth()->user();

// Sécurité: si pas connecté, on n'affiche rien
if (! $user) {
return;
}

// Compteur des messages internes non lus
$internalUnreadCount = \App\Models\Message::query()
->where('receiver_id', $user->id)
->internal()
->where('is_read', false)
->count();

$inboxUrl = \App\Filament\Resources\MessageResource::getUrl('index');

// Super admin: compteur des demandes système en attente
$isSuperAdmin = method_exists($user, 'isSuperAdmin')
? $user->isSuperAdmin()
: (method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : false);

$systemPendingCount = 0;
$systemUrl = null;
if ($isSuperAdmin) {
$systemPendingCount = \App\Models\Message::query()
->system()
->where('status', 'pending')
->count();

$systemUrl = \App\Filament\Resources\SystemRequestResource::getUrl('index');
}

$displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
@endphp

<div class="fi-topbar-welcome hidden md:flex items-center gap-3">
    <div class="flex items-baseline gap-2">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-100">
            Bonjour, {{ $displayName ?: 'Utilisateur' }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-300">
            {{ now()->isoFormat('dddd D MMMM') }}
        </span>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ $inboxUrl }}"
            class="inline-flex items-center gap-2 rounded-lg px-2.5 py-1 text-xs font-semibold
                  bg-white/70 dark:bg-white/10 border border-gray-200/70 dark:border-white/10
                  text-gray-700 dark:text-gray-100 hover:bg-white dark:hover:bg-white/15 transition">
            <x-heroicon-m-envelope class="w-4 h-4" />
            <span>Messages</span>

            @if ($internalUnreadCount > 0)
            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-[11px]
                             bg-red-600 text-white">
                {{ $internalUnreadCount }}
            </span>
            @endif
        </a>

        @if ($isSuperAdmin)
        <a href="{{ $systemUrl }}"
            class="inline-flex items-center gap-2 rounded-lg px-2.5 py-1 text-xs font-semibold
                      bg-white/70 dark:bg-white/10 border border-gray-200/70 dark:border-white/10
                      text-gray-700 dark:text-gray-100 hover:bg-white dark:hover:bg-white/15 transition">
            <x-heroicon-m-inbox-stack class="w-4 h-4" />
            <span>Demandes système</span>

            @if ($systemPendingCount > 0)
            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-[11px]
                                 bg-amber-500 text-black">
                {{ $systemPendingCount }}
            </span>
            @endif
        </a>
        @endif
    </div>
</div>

{{-- Petit ajustement de layout topbar pour espacer proprement --}}
<style>
    /* La topbar de Filament est un flex; on s'assure que notre bloc reste compact */
    .fi-topbar-welcome {
        white-space: nowrap;
    }
</style>