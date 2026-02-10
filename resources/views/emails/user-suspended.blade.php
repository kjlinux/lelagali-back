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
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        .warning-badge {
            background-color: #E6782C;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            margin-bottom: 30px;
        }

        .alert-box {
            background-color: #fff3e0;
            border: 2px solid #E6782C;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .alert-box h3 {
            color: #E6782C;
            margin-top: 0;
        }

        .raison-box {
            background-color: #FDF6EC;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #E6782C;
        }

        .raison-box h4 {
            color: #4B2E1E;
            margin-top: 0;
        }

        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #2196F3;
        }

        .footer {
            text-align: center;
            color: #4B2E1E;
            margin-top: 30px;
            font-size: 14px;
        }

        .contact-info {
            background-color: #47A547;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }

        .contact-info a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="warning-badge">⚠ COMPTE SUSPENDU</div>
        </div>

        <div class="content">
            <h2>Bonjour {{ $user->name }},</h2>

            <div class="alert-box">
                <h3>Votre compte Lelagali a été suspendu</h3>
                <p>Nous vous informons que votre compte a été temporairement suspendu. Vous ne pouvez plus accéder aux services de la plateforme pour le moment.</p>
            </div>

            @if($raison)
            <div class="raison-box">
                <h4>Raison de la suspension :</h4>
                <p>{{ $raison }}</p>
            </div>
            @endif

            <div class="info-box">
                <p><strong>Informations sur votre compte :</strong></p>
                <ul>
                    <li><strong>Nom :</strong> {{ $user->name }}</li>
                    <li><strong>Email :</strong> {{ $user->email ?? 'Non renseigné' }}</li>
                    <li><strong>Téléphone :</strong> {{ $user->phone }}</li>
                    <li><strong>Rôle :</strong> {{ ucfirst($user->role) }}</li>
                </ul>
            </div>

            <p><strong>Que faire maintenant ?</strong></p>
            <p>Si vous pensez qu'il s'agit d'une erreur ou si vous souhaitez plus d'informations concernant cette suspension, nous vous invitons à contacter notre service support.</p>

            <div class="contact-info">
                <p><strong>Contactez-nous</strong></p>
                <p>Pour toute réclamation ou information, veuillez nous écrire à notre équipe support.</p>
            </div>
        </div>

        <div class="footer">
            <p>Nous nous excusons pour le désagrément occasionné.</p>
            <p><strong>L'équipe Lelagali</strong></p>
        </div>
    </div>
</body>

</html>
