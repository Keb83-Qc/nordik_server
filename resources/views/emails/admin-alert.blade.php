@php
    $emailSettings = app(\App\Settings\EmailSettings::class);

    $data     = $message->data ?? [];
    $type     = $data['type'] ?? 'system';
    $prio     = $data['priority'] ?? null;
    $category = $data['category'] ?? null;
    $url      = $data['url'] ?? null;
    $sender   = $message->sender
        ? trim($message->sender->first_name . ' ' . $message->sender->last_name)
        : 'Système';
    $senderEmail = $message->sender->email ?? null;

    $typeLabel = match($type) {
        'bug_report' => match($category) {
            'bug'         => '🐛 Bug / Erreur',
            'suggestion'  => '💡 Suggestion',
            'improvement' => '✨ Amélioration',
            default       => 'Rapport',
        },
        default => '⚙️ Demande Système',
    };

    $prioLabel = match($prio) {
        'high'   => '▲ Élevée',
        'medium' => '● Moyenne',
        'low'    => '▼ Basse',
        default  => null,
    };

    $badgeClass = match(true) {
        $type === 'bug_report' && $prio === 'high' => 'badge-bug-high',
        $type === 'bug_report'                     => 'badge-bug',
        default                                    => 'badge-system',
    };

    $adminUrl = config('app.url') . '/admin/bug-reports';
@endphp

<x-email.layout
    :headerColor="$emailSettings->alert_header_color"
    headerTitle="{{ $typeLabel }}"
    :accentColor="$emailSettings->global_accent_color"
    :logoUrl="$emailSettings->global_logo_url"
    :footerText="$emailSettings->global_footer_text"
    title="Alerte Admin"
>
    <x-slot name="styles">
    <style>
        .badge { display: inline-block; margin-bottom: 8px; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .badge-bug { background: rgba(239,68,68,.15); color: #dc2626; border: 1px solid rgba(239,68,68,.3); }
        .badge-bug-high { background: rgba(239,68,68,.25); color: #b91c1c; border: 1px solid rgba(239,68,68,.5); }
        .badge-system { background: rgba(201,160,80,.15); color: #92400e; border: 1px solid rgba(201,160,80,.3); }
        .row { margin: 10px 0; font-size: 14px; line-height: 1.5; }
        .label { font-weight: 700; color: #0E1030; display: inline-block; min-width: 110px; }
        .value { color: #333; }
        .desc { margin-top: 16px; background: #f8f8f8; border: 1px solid #eee; border-radius: 8px; padding: 14px 16px; font-size: 13px; line-height: 1.6; color: #444; }
        .cta { margin-top: 20px; text-align: center; }
        .cta a { display: inline-block; padding: 10px 22px; background: #C9A050; color: #fff !important; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; }
        .note { font-size: 11px; color: #999; text-align: center; margin-top: 12px; }
    </style>
    </x-slot>

    <span class="badge {{ $badgeClass }}">
        {{ $type === 'bug_report' ? 'Bug Report' : 'Demande Système' }}
        @if($prioLabel) — {{ $prioLabel }} @endif
    </span>

    <div class="row"><span class="label">Sujet :</span> <span class="value">{{ $message->subject }}</span></div>
    <div class="row">
        <span class="label">Conseiller :</span>
        <span class="value">{{ $sender }}@if($senderEmail) &lt;<a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>&gt;@endif</span>
    </div>
    <div class="row"><span class="label">Date :</span> <span class="value">{{ $message->created_at?->format('d M Y à H:i') }}</span></div>
    @if($url)
    <div class="row"><span class="label">Page :</span> <span class="value"><a href="{{ $url }}">{{ $url }}</a></span></div>
    @endif

    @if($message->body)
    <div class="desc">{!! $message->body !!}</div>
    @endif

    <div class="cta">
        <a href="{{ $adminUrl }}">Voir dans le portail admin</a>
    </div>

    <p class="note">Notification automatique — ne pas répondre directement.</p>
</x-email.layout>
