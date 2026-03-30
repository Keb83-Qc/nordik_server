<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Bilan Financier — VIP GPI</title>

    {{-- ── Variables Laravel injectées pour le JS ─────────── --}}
    <script>
        @if ($record)
            @php $recordIdentifier = $record->slug ?: 'nouveau-' . $record->id; @endphp
            window.ABF_RECORD_ID = {{ $record->id }};
            window.ABF_SAVE_URL = '{{ route('abf.editor.save', ['record' => $recordIdentifier]) }}';
            window.ABF_INITIAL_PAYLOAD = {!! json_encode($record->payload ?? []) !!};
        @else
            window.ABF_RECORD_ID = null;
            window.ABF_SAVE_URL = null;
            window.ABF_CREATE_URL = '{{ route('abf.create.json') }}';
            window.ABF_INITIAL_PAYLOAD = null;
        @endif
        window.ABF_CSRF_TOKEN = '{{ csrf_token() }}';
        window.ABF_ADVISOR_NAME = '{{ auth()->user()->full_name ?? (auth()->user()->name ?? '') }}';
        window.ABF_PARAMS_SAVE_URL = '{{ route('abf.params.save') }}';

        {{-- Paramètres système chargés depuis la base de données --}}
        window.ABF_PARAMS = {!! json_encode($abfParams ?? []) !!};
    </script>

    {{-- ── Feuille de style ────────────────────────────────── --}}
    <link rel="stylesheet" href="{{ asset('css/abf-editor.css') }}" />
</head>

<body>

    {{-- ── Page d'accueil (landing) ───────────────────────── --}}
    @include('abf.partials._page-accueil')

    {{-- ── Modal Configuration unifié (Profil / Valeurs / Impôt / Rente) ── --}}
    @include('abf.partials._modal-config')

    {{-- ── Modal hypothèses du parcours ───────────────────── --}}
    @include('abf.partials._modal-hypotheses')

    {{-- ── Topbar + Sidebar ────────────────────────────────── --}}
    @include('abf.partials._topbar-sidebar')

    {{-- ── Pages principales ──────────────────────────────── --}}
    @include('abf.partials._page-infos-perso')
    @include('abf.partials._page-objectifs')
    @include('abf.partials._page-actifs-passifs')
    @include('abf.partials._page-revenu-epargne')
    @include('abf.partials._page-fonds-urgence')
    @include('abf.partials._page-deces')
    @include('abf.partials._page-invalidite')
    @include('abf.partials._page-maladie-grave')
    @include('abf.partials._page-projets')
    @include('abf.partials._page-retraite')
    @include('abf.partials._page-recommandations')
    @include('abf.partials._page-rapport')

    </main>{{-- fermé dans _topbar-sidebar --}}
    </div>{{-- .layout --}}

    {{-- ── JavaScript ──────────────────────────────────────── --}}
    <script src="{{ asset('js/abf-editor.js') }}?v={{ filemtime(public_path('js/abf-editor.js')) }}"></script>
</body>

</html>
