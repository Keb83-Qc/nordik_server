{{--
    Barre d'auto-sauvegarde ABF.
    $mode = 'edit'   → sauvegarde silencieuse en DB toutes les 30 s
    $mode = 'create' → brouillon localStorage toutes les 30 s
    $recordId = identifiant du dossier (edit seulement, pour la clé localStorage)
--}}
@php $draftKey = 'abf_draft_' . ($mode === 'edit' ? ($recordId ?? 'edit') : 'new'); @endphp

<div
    x-data="abfAutoSave('{{ $mode }}', '{{ $draftKey }}')"
    x-init="init()"
    class="fixed bottom-4 right-4 z-50"
    style="pointer-events:none;"
>
    {{-- Pill de statut --}}
    <div
        x-show="status !== ''"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="pointer-events:auto; background:rgba(14,16,48,.92); color:#e8c97a;
               padding:6px 14px; border-radius:999px; font-size:13px;
               box-shadow:0 2px 10px rgba(0,0,0,.35); display:flex; align-items:center; gap:8px;"
    >
        <span x-html="icon" style="font-size:15px;"></span>
        <span x-text="status"></span>
    </div>

    {{-- Bandeau restauration brouillon (create uniquement) --}}
    @if($mode === 'create')
    <div
        x-show="hasDraft"
        style="pointer-events:auto; margin-top:8px; background:#fff; border:1px solid #C9A050;
               border-radius:10px; padding:12px 16px; font-size:13px; color:#0E1030;
               box-shadow:0 3px 12px rgba(0,0,0,.15); min-width:280px;"
    >
        <p style="font-weight:700; margin-bottom:6px;">📋 Brouillon récupéré</p>
        <p style="color:#555; margin-bottom:10px;">Un brouillon local a été trouvé. Voulez-vous le restaurer?</p>
        <div style="display:flex; gap:8px;">
            <button
                @click="restoreDraft()"
                style="background:#0E1030; color:#e8c97a; border:none; padding:6px 14px;
                       border-radius:6px; cursor:pointer; font-weight:600; font-size:12px;"
            >Restaurer</button>
            <button
                @click="discardDraft()"
                style="background:#f3f4f6; color:#666; border:none; padding:6px 14px;
                       border-radius:6px; cursor:pointer; font-size:12px;"
            >Ignorer</button>
        </div>
    </div>
    @endif
</div>

<script>
function abfAutoSave(mode, draftKey) {
    return {
        status: '',
        icon: '💾',
        hasDraft: false,
        statusTimer: null,

        init() {
            if (mode === 'edit') {
                this.startEditAutoSave();
            } else {
                this.checkDraft();
                this.startCreateBackup();
            }
        },

        // ── MODE EDIT : sauvegarde DB ─────────────────────────────────────
        startEditAutoSave() {
            setInterval(() => this.doEditSave(), 30000);

            // Écoute la réponse du composant Livewire
            window.addEventListener('abf-auto-saved', () => {
                this.showStatus('💾', 'Auto-sauvegardé à ' + this.time());
            });
        },

        async doEditSave() {
            try {
                this.showStatus('⏳', 'Sauvegarde...');
                await $wire.call('autoSave');
            } catch (e) {
                this.showStatus('⚠️', 'Échec de sauvegarde');
            }
        },

        // ── MODE CREATE : brouillon localStorage ──────────────────────────
        checkDraft() {
            const raw = localStorage.getItem(draftKey);
            if (!raw) return;
            try {
                const draft = JSON.parse(raw);
                if (draft && draft.data) this.hasDraft = true;
            } catch {}
        },

        startCreateBackup() {
            setInterval(() => this.saveDraft(), 30000);

            // Aussi sauvegarder avant que l'onglet se ferme
            window.addEventListener('beforeunload', () => this.saveDraft());

            // Nettoyer après création réussie
            window.addEventListener('abf-draft-clear', () => {
                localStorage.removeItem(draftKey);
            });
        },

        async saveDraft() {
            try {
                const data = await $wire.get('data');
                if (!data) return;
                localStorage.setItem(draftKey, JSON.stringify({ data, savedAt: this.time() }));
                this.showStatus('📋', 'Brouillon local sauvegardé à ' + this.time());
            } catch {}
        },

        async restoreDraft() {
            try {
                const raw = localStorage.getItem(draftKey);
                if (!raw) return;
                const draft = JSON.parse(raw);
                await $wire.set('data', draft.data);
                this.hasDraft = false;
                this.showStatus('✅', 'Brouillon restauré');
                localStorage.removeItem(draftKey);
            } catch {
                this.showStatus('⚠️', 'Impossible de restaurer');
            }
        },

        discardDraft() {
            localStorage.removeItem(draftKey);
            this.hasDraft = false;
        },

        // ── Utils ─────────────────────────────────────────────────────────
        showStatus(icon, msg) {
            this.icon = icon;
            this.status = msg;
            clearTimeout(this.statusTimer);
            this.statusTimer = setTimeout(() => { this.status = ''; }, 4000);
        },

        time() {
            return new Date().toLocaleTimeString('fr-CA', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
    };
}
</script>
