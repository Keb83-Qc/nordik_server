<!doctype html>
<html lang="fr">

<body style="font-family: Arial, sans-serif;">
    <h2>Soumission auto reçue</h2>

    <p><strong>Client :</strong> {{ $clientName }}</p>
    <p><strong>Véhicule :</strong> {{ $vehicle }}</p>
    <p><strong>Date :</strong> {{ $submission->created_at->format('d/m/Y H:i') }}</p>

    <p>
        <a href="{{ $filamentUrl }}" style="display:inline-block;padding:10px 14px;background:#0E1030;color:#fff;text-decoration:none;border-radius:8px;">
            Ouvrir dans le portail
        </a>
    </p>
</body>

</html>
