<!DOCTYPE html>
<html>

<head>
    <title>Demande d'accès</title>
</head>

<body style="font-family: Arial; max-width: 500px; margin: 50px auto;">

    <h2>Demande d’accès</h2>

    @if(session('success'))
    <div style="color: green; margin-bottom:15px;">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="">
        @csrf

        <label>Nom</label><br>
        <input type="text" name="name" required style="width:100%; margin-bottom:10px;"><br>

        <label>Email</label><br>
        <input type="email" name="email" required style="width:100%; margin-bottom:10px;"><br>

        <label>Téléphone</label><br>
        <input type="text" name="phone" style="width:100%; margin-bottom:15px;"><br>

        <button type="submit" style="padding:8px 15px;">
            Envoyer la demande
        </button>
    </form>

</body>

</html>
