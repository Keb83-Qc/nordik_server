@php
$advisorCode = session('current_advisor_code');
$advisor = \App\Models\User::where('advisor_code', $advisorCode)->first();

$advisorName = $advisor ? $advisor->first_name . ' ' . $advisor->last_name : 'Conseiller';
$advisorPhone = $advisor && $advisor->phone ? $advisor->phone : null;
@endphp

<!DOCTYPE html>
<html lang="{{ session('locale', 'fr') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('bundlechat.page_title') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @livewireStyles

    @include('quote.partials.chat-styles')
</head>

<body>
    <nav class="navbar fixed-top vip-navbar">
        <div class="container">
            <a class="navbar-brand shadow-sm" href="{{ url('/' . app()->getLocale()) }}">
                <img src="{{ asset('assets/img/VIP_Logo_Gold_Gradient10.png') }}" alt="VIP GPI Logo">
            </a>

            <div class="d-flex align-items-center">
                <div class="advisor-box text-end">
                    <span class="advisor-label">{{ __('bundlechat.advisor_label') }}</span>

                    <div class="advisor-name">
                        <i class="fas fa-user-tie me-1 text-muted small"></i>
                        {{ $advisorName }}
                    </div>

                    @if($advisorPhone)
                    <a href="tel:{{ $advisorPhone }}" class="advisor-phone">
                        <i class="fas fa-phone-alt me-1 small"></i>
                        {{ $advisorPhone }}
                    </a>
                    @else
                    <span class="text-muted small">{{ __('bundlechat.contact_us') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    @livewire('quote-bundle-chat')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>