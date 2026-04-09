<x-email.layout>
    <h2 style="color:#0e1030;margin:0 0 20px">Nouveau message — Formulaire de contact</h2>

    <table style="width:100%;border-collapse:collapse;font-size:14px;color:#374151">
        <tr>
            <td style="padding:10px 14px;background:#f8f9fd;font-weight:600;border-bottom:1px solid #e5e7eb;width:140px">Nom</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb">{{ $senderName }}</td>
        </tr>
        <tr>
            <td style="padding:10px 14px;background:#f8f9fd;font-weight:600;border-bottom:1px solid #e5e7eb">Courriel</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb">
                <a href="mailto:{{ $senderEmail }}" style="color:#c9a227">{{ $senderEmail }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 14px;background:#f8f9fd;font-weight:600;border-bottom:1px solid #e5e7eb">Sujet</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb">{{ $subject }}</td>
        </tr>
    </table>

    <div style="margin-top:24px;padding:16px 20px;background:#f8f9fd;border-left:4px solid #c9a227;border-radius:4px;font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap">{{ $messageBody }}</div>

    <p style="margin-top:24px;font-size:12px;color:#9ca3af">
        Ce message a été envoyé depuis le formulaire de contact du site vipgpi.net.
    </p>
</x-email.layout>
