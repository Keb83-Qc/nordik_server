// public/js/filament-admin.js

(function () {
    function unlockWizardSteps() {
        const wizard = document.querySelector('.abf-wizard');
        if (!wizard) return;

        const header = wizard.querySelector('.fi-fo-wizard-header');
        if (!header) return;

        const buttons = Array.from(header.querySelectorAll('button'));
        if (!buttons.length) return;

        const key = 'abf_wizard_max_' + window.location.pathname;

        const activeIndex = buttons.findIndex(
            (b) => b.getAttribute('aria-current') === 'step'
        );

        if (activeIndex >= 0) {
            const currentMax = parseInt(localStorage.getItem(key) || '0', 10);
            localStorage.setItem(key, String(Math.max(currentMax, activeIndex)));
        }

        const maxVisited = parseInt(localStorage.getItem(key) || '0', 10);

        buttons.forEach((btn, idx) => {
            if (idx <= maxVisited) {
                btn.removeAttribute('disabled');
                btn.style.pointerEvents = 'auto';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        });
    }

    function boot() {
        unlockWizardSteps();
    }

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('livewire:initialized', boot);
    document.addEventListener('livewire:navigated', boot);

    if (window.Livewire && typeof window.Livewire.hook === 'function') {
        window.Livewire.hook('morph.updated', () => boot());
        window.Livewire.hook('commit', () => setTimeout(boot, 0));
    }
})();
