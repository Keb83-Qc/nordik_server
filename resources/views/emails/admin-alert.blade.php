<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alerte Admin – VIP GPI</title>
<style>
body{font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;margin:0;}
.wrap{max-width:680px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);}
.hdr{background:#0E1030;color:#fff;padding:18px 24px;}
.hdr h2{margin:0;font-size:18px;letter-spacing:.3px;}
.badge{display:inline-block;margin-top:8px;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;text-transform:uppercase;}
.badge-bug{background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.4);}
.badge-bug-high{background:rgba(239,68,68,.35);color:#fecaca;border:1px solid rgba(239,68,68,.6);}
.badge-system{background:rgba(201,160,80,.18);color:#fde68a;border:1px solid rgba(201,160,80,.4);}
.body{padding:22px 24px;}
.row{margin:10px 0;font-size:14px;line-height:1.5;}
.label{font-weight:700;color:#0E1030;display:inline-block;min-width:110px;}
.value{color:#333;}
.desc{margin-top:16px;background:#f8f8f8;border:1px solid #eee;border-radius:8px;padding:14px 16px;font-size:13px;line-height:1.6;color:#444;}
.cta{margin-top:20px;text-align:center;}
.cta a{display:inline-block;padding:10px 22px;background:#c9a050;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;}
.footer{text-align:center;font-size:11px;color:#888;padding:14px;background:#fafafa;border-top:1px solid #eee;}
</style>
</head>
<body>
@php
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

<div class="wrap">
    <div class="hdr">
        <h2>{{ $typeLabel }}</h2>
        <span class="badge {{ $badgeClass }}">
            {{ $type === 'bug_report' ? 'Bug Report' : 'Demande Système' }}
            @if($prioLabel) — {{ $prioLabel }} @endif
        </span>
    </div>

    <div class="body">
        <div class="row"><span class="label">Sujet :</span> <span class="value">{{ $message->subject }}</span></div>
        <div class="row"><span class="label">Conseiller :</span> <span class="value">{{ $sender }}@if($senderEmail) &lt;<a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>&gt;@endif</span></div>
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
    </div>

    <div class="footer">
        VIP Gestion de Patrimoine &amp; Investissement Inc. &copy; {{ date('Y') }}<br>
        Notification automatique — ne pas répondre directement.
    </div>
</div>
</body>
</html>
