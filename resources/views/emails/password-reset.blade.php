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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Lelagali</div>
        </div>
        <div class="content">
            <h2>Réinitialisation de votre mot de passe</h2>
            <p>Bonjour {{ $user->name }},</p>
            <p>Votre mot de passe a été réinitialisé avec succès. Voici votre nouveau mot de passe :</p>

            <div class="credentials">
                <p><strong>Nouveau mot de passe :</strong> {{ $password }}</p>
            </div>

            <p class="alert">Pour des raisons de sécurité, nous vous recommandons de changer ce mot de passe dès votre
                prochaine connexion.</p>
        </div>

        <div class="footer">
            <p>Si vous n'avez pas demandé cette réinitialisation, veuillez nous contacter immédiatement.</p>
        </div>
    </div>
</body>

</html>
