<x-filament-panels::page>
    <div style="font-family: monospace;">

        @if(empty($entries))
            <div style="padding:20px; color:#6b7280; text-align:center;">
                Aucune entrée dans le journal. Le fichier <code>storage/logs/laravel.log</code> est vide ou introuvable.
            </div>
        @else
        <div style="margin-bottom:12px; font-size:13px; color:#6b7280;">
            {{ count($entries) }} entrée(s) — plus récent en premier
        </div>

        @foreach($entries as $entry)
        @php
            $color = match($entry['level']) {
                'ERROR', 'CRITICAL', 'EMERGENCY' => '#fee2e2',
                'WARNING' => '#fef3c7',
                default => '#f3f4f6',
            };
            $border = match($entry['level']) {
                'ERROR', 'CRITICAL', 'EMERGENCY' => '#ef4444',
                'WARNING' => '#f59e0b',
                default => '#d1d5db',
            };
            $textColor = match($entry['level']) {
                'ERROR', 'CRITICAL', 'EMERGENCY' => '#991b1b',
                'WARNING' => '#92400e',
                default => '#374151',
            };
            $contextShort = mb_substr(trim($entry['context']), 0, 600);
        @endphp
        <details style="
            margin-bottom: 8px;
            border: 1px solid {{ $border }};
            border-radius: 8px;
            overflow: hidden;
            background: {{ $color }};
        ">
            <summary style="
                padding: 10px 14px;
                cursor: pointer;
                display: flex;
                gap: 12px;
                align-items: flex-start;
                list-style: none;
            ">
                <span style="
                    font-size: 10px;
                    font-weight: 700;
                    text-transform: uppercase;
                    background: {{ $border }};
                    color: #fff;
                    padding: 2px 7px;
                    border-radius: 4px;
                    white-space: nowrap;
                    margin-top: 1px;
                ">{{ $entry['level'] }}</span>

                <span style="font-size:11px; color:#6b7280; white-space:nowrap; margin-top:1px;">
                    {{ $entry['date'] }}
                </span>

                <span style="color: {{ $textColor }}; font-size: 13px; word-break: break-word;">
                    {{ $entry['message'] }}
                </span>
            </summary>

            @if(!empty(trim($entry['context'])))
            <div style="
                padding: 10px 14px;
                border-top: 1px solid {{ $border }};
                background: rgba(0,0,0,.03);
                font-size: 11px;
                white-space: pre-wrap;
                word-break: break-word;
                color: #374151;
                max-height: 300px;
                overflow-y: auto;
            ">{{ $contextShort }}@if(mb_strlen(trim($entry['context'])) > 600)
<span style="color:#9ca3af;">... (tronqué)</span>@endif</div>
            @endif
        </details>
        @endforeach
        @endif

    </div>

    {{-- Bouton rafraîchir --}}
    <div style="margin-top:16px;">
        <button
            wire:click="$refresh"
            style="
                background: #0E1030; color: #C9A050;
                border: none; padding: 8px 18px;
                border-radius: 8px; cursor: pointer;
                font-size: 13px; font-weight: 600;
            "
        >↻ Rafraîchir</button>
    </div>
</x-filament-panels::page>
