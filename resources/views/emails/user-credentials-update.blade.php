<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #4B2E1E;
            background-color: #FDF6EC;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            color: #47A547;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            margin-bottom: 30px;
        }

        .credentials {
            background-color: #FDF6EC;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .alert {
            color: #E6782C;
            font-weight: bold;
            margin: 20px 0;
        }

        .button {
            background-color: #E6782C;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            color: #4B2E1E;
            margin-top: 30px;
            font-size: 14px;
        }

        .highlight {
            color: #F8C346;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Lelagali</div>
        </div>
        <div class="content">
            <h2>Mise à jour de vos identifiants</h2>
            <p>Bonjour {{ $user->name }},</p>
            <p>Votre mot de passe a été mis à jour. Voici vos nouveaux identifiants de connexion :</p>

            <div class="credentials">
                <p><strong>Email :</strong> {{ $user->email }}</p>
                @if($user->phone)
                <p><strong>Téléphone :</strong> {{ $user->phone }}</p>
                @endif
                <p><strong>Nouveau mot de passe :</strong> {{ $password }}</p>
            </div>

            <p class="alert">Pour votre sécurité, nous vous recommandons de changer ce mot de passe lors de votre
                prochaine connexion.</p>
        </div>

        <div class="footer">
            <p>Si vous n'avez pas demandé ce changement, veuillez nous contacter immédiatement.</p>
        </div>
    </div>
</body>

</html>
