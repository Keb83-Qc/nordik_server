{{-- ABF Custom Top Bar: date · utilisateur · toggle dark mode --}}
<div
    class="abf-topbar"
    x-data="{
        dark: localStorage.getItem('abf-theme') === 'dark'
            || (localStorage.getItem('abf-theme') === null && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('abf-theme', this.dark ? 'dark' : 'light');
        }
    }"
    x-init="document.documentElement.classList.toggle('dark', dark)"
>
    {{-- Gauche : date --}}
    <div class="abf-topbar-left">
        <span class="abf-topbar-date">
            {{ \Carbon\Carbon::now()->locale('fr_CA')->isoFormat('dddd D MMMM YYYY') }}
        </span>
    </div>

    {{-- Droite : utilisateur + toggle --}}
    <div class="abf-topbar-right">
        <span class="abf-topbar-user">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 style="width:15px;height:15px;flex-shrink:0;">
                <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
            </svg>
            <span>{{ auth()->user()?->full_name ?? auth()->user()?->email ?? 'Conseiller' }}</span>
        </span>

        <button class="abf-topbar-theme-btn" @click="toggle()" :title="dark ? 'Passer en mode clair' : 'Passer en mode sombre'">
            <span x-show="!dark" style="display:flex;align-items:center;gap:5px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;">
                    <path d="M17.293 13.293A8 8 0 0 1 6.707 2.707a8.001 8.001 0 1 0 10.586 10.586Z" />
                </svg>
                Mode sombre
            </span>
            <span x-show="dark" style="display:flex;align-items:center;gap:5px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;">
                    <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.061ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z" />
                </svg>
                Mode clair
            </span>
        </button>
    </div>
</div>
