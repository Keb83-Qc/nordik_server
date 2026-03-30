@php
$advisorCode = session('current_advisor_code');
$advisor     = \App\Models\User::where('advisor_code', $advisorCode)->first();

$advisorName  = $advisor ? $advisor->first_name . ' ' . $advisor->last_name : 'Conseiller';
$advisorPhone = $advisor && $advisor->phone ? $advisor->phone : null;
@endphp

<!DOCTYPE html>
<html lang="{{ session('locale', 'fr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portal->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @livewireStyles

    @include('quote.partials.chat-styles')

    {{-- Surcharge des couleurs avec celles du portail --}}
    <style>
        :root {
            --portal-primary:   {{ $portal->primary_color   ?? '#1a2e4a' }};
            --portal-secondary: {{ $portal->secondary_color ?? '#e8b84b' }};
        }

        .vip-navbar       { background-color: var(--portal-primary) !important; }
        .navbar-brand img { max-height: 48px; object-fit: contain; }
    </style>
</head>

<body>

    <nav class="navbar fixed-top vip-navbar">
        <div class="container">

            {{-- Logo du partenaire OU nom du portail --}}
            <a class="navbar-brand shadow-sm" href="{{ url('/' . app()->getLocale()) }}">
                @if($portal->logo_path)
                    <img src="{{ asset('storage/' . $portal->logo_path) }}"
                         alt="{{ $portal->name }}">
                @else
                    <span style="color: {{ $portal->secondary_color ?? '#e8b84b' }}; font-weight: 800; font-size: 1.1rem;">
                        {{ $portal->name }}
                    </span>
                @endif
            </a>

            {{-- Infos conseiller --}}
            <div class="d-flex align-items-center">
                <div class="advisor-box text-end">
                    <span class="advisor-label">{{ __('chat.advisor_label') }}</span>
                    <div class="advisor-name">
                        <i class="fas fa-user-tie me-1 text-muted small"></i>
                        {{ $advisorName }}
                    </div>
                    @if($advisorPhone)
                        <a href="tel:{{ $advisorPhone }}" class="advisor-phone">
                            <i class="fas fa-phone-alt me-1 small"></i>
                            {{ $advisorPhone }}
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </nav>

    {{-- Composant Livewire déterminé par le type (quote-auto-chat, quote-home-chat, quote-bundle-chat) --}}
    @livewire($component)

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        function sendJsError(data) {
            try {
                fetch('/log-js-error', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data),
                    keepalive: true,
                });
            } catch (_) {}
        }
        window.onerror = function (message, source, line, column, error) {
            sendJsError({ type: 'js_error', message: String(message).slice(0, 300), source: String(source || '').slice(0, 300), line: line, column: column, stack: error?.stack ? String(error.stack).slice(0, 600) : '', url: window.location.href.slice(0, 300) });
            return false;
        };
        window.addEventListener('unhandledrejection', function (event) {
            var reason = event.reason;
            var message = reason instanceof Error ? reason.message : String(reason);
            sendJsError({ type: 'unhandled_promise', message: String(message).slice(0, 300), source: '', line: '', column: '', stack: reason instanceof Error && reason.stack ? String(reason.stack).slice(0, 600) : '', url: window.location.href.slice(0, 300) });
        });
    })();
    </script>
</body>
</html>
