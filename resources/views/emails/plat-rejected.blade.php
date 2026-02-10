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

        .plat-details {
            background-color: #FDF6EC;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #E6782C;
        }

        .plat-details h3 {
            color: #E6782C;
            margin-top: 0;
        }

        .detail-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #4B2E1E;
            display: inline-block;
            width: 150px;
        }

        .detail-value {
            color: #666;
        }

        .raison-box {
            background-color: #fff3e0;
            border: 2px solid #E6782C;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .raison-box h4 {
            color: #E6782C;
            margin-top: 0;
        }

        .footer {
            text-align: center;
            color: #4B2E1E;
            margin-top: 30px;
            font-size: 14px;
        }

        .info-box {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #47A547;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="warning-badge">⚠ PLAT REJETÉ</div>
        </div>

        <div class="content">
            <h2>Bonjour {{ $plat->restaurateur->name }},</h2>
            <p>Nous sommes au regret de vous informer que votre plat n'a pas été approuvé par notre équipe de modération.</p>

            <div class="plat-details">
                <h3>Détails du plat concerné</h3>

                <div class="detail-row">
                    <span class="detail-label">Nom du plat :</span>
                    <span class="detail-value">{{ $plat->nom }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Description :</span>
                    <span class="detail-value">{{ $plat->description }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Prix :</span>
                    <span class="detail-value">{{ number_format($plat->prix, 0, ',', ' ') }} FCFA</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date de soumission :</span>
                    <span class="detail-value">{{ $plat->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            @if($raison)
            <div class="raison-box">
                <h4>Raison du rejet :</h4>
                <p>{{ $raison }}</p>
            </div>
            @endif

            <div class="info-box">
                <p><strong>Que faire maintenant ?</strong></p>
                <p>Vous pouvez modifier votre plat et le soumettre à nouveau pour approbation. Assurez-vous que :</p>
                <ul>
                    <li>La description est claire et précise</li>
                    <li>Le prix est correct et compétitif</li>
                    <li>Les informations sont exactes</li>
                    <li>L'image (si fournie) est de bonne qualité</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Notre équipe est là pour vous accompagner. N'hésitez pas à nous contacter pour plus d'informations.</p>
            <p><strong>L'équipe Lelagali</strong></p>
        </div>
    </div>
</body>

</html>
