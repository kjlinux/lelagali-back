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

        .success-badge {
            background-color: #47A547;
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
            border-left: 4px solid #47A547;
        }

        .plat-details h3 {
            color: #47A547;
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

        .footer {
            text-align: center;
            color: #4B2E1E;
            margin-top: 30px;
            font-size: 14px;
        }

        .highlight {
            color: #E6782C;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="success-badge">✓ PLAT APPROUVÉ</div>
        </div>

        <div class="content">
            <h2>Félicitations {{ $plat->restaurateur->name }} !</h2>
            <p>Nous sommes heureux de vous informer que votre plat a été approuvé par notre équipe et est maintenant visible par tous les clients sur Lelagali.</p>

            <div class="plat-details">
                <h3>Détails du plat approuvé</h3>

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
                    <span class="detail-value highlight">{{ number_format($plat->prix, 0, ',', ' ') }} FCFA</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Quantité disponible :</span>
                    <span class="detail-value">{{ $plat->quantite_disponible }} portions</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date de disponibilité :</span>
                    <span class="detail-value">{{ $plat->date_disponibilite->format('d/m/Y') }}</span>
                </div>
            </div>

            <p>Votre plat est maintenant en ligne et les clients peuvent commencer à passer des commandes. Assurez-vous de maintenir la qualité et la disponibilité annoncées.</p>
        </div>

        <div class="footer">
            <p><strong>Merci de faire partie de la famille Lelagali !</strong></p>
            <p>Pour toute question, n'hésitez pas à nous contacter.</p>
        </div>
    </div>
</body>

</html>
