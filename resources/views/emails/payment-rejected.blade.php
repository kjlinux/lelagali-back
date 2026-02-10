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

        .order-number {
            font-size: 24px;
            color: #E6782C;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
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

        .order-details {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .detail-row {
            margin: 10px 0;
        }

        .detail-label {
            font-weight: bold;
            color: #4B2E1E;
            display: inline-block;
            width: 180px;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="warning-badge">⚠ PAIEMENT NON CONFIRMÉ</div>
        </div>

        <div class="order-number">
            #{{ $commande->numero_commande }}
        </div>

        <div class="content">
            <h2>Bonjour {{ $commande->client->name }},</h2>

            <div class="alert-box">
                <h3>Le restaurant n'a pas confirmé votre paiement</h3>
                <p>Nous vous informons que le restaurant <strong>{{ $commande->restaurateur->name }}</strong> n'a pas confirmé la réception de votre paiement pour cette commande.</p>
            </div>

            @if($raison)
            <div class="raison-box">
                <h4>Raison :</h4>
                <p>{{ $raison }}</p>
            </div>
            @endif

            <div class="order-details">
                <h3>Détails de la commande</h3>
                <div class="detail-row">
                    <span class="detail-label">Numéro de commande :</span>
                    <span>{{ $commande->numero_commande }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Restaurant :</span>
                    <span>{{ $commande->restaurateur->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Montant total :</span>
                    <span><strong>{{ number_format($commande->total_general, 0, ',', ' ') }} FCFA</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Moyen de paiement :</span>
                    <span>{{ $commande->moyenPaiement->nom }}</span>
                </div>
                @if($commande->numero_paiement)
                <div class="detail-row">
                    <span class="detail-label">Numéro de transaction :</span>
                    <span>{{ $commande->numero_paiement }}</span>
                </div>
                @endif
            </div>

            <div class="info-box">
                <p><strong>Que faire maintenant ?</strong></p>
                <ul>
                    <li>Vérifiez que le paiement a bien été effectué de votre côté</li>
                    <li>Si vous avez effectué le paiement, conservez votre reçu/preuve de paiement</li>
                    <li>Contactez le restaurant pour clarifier la situation</li>
                    <li>Si nécessaire, contactez notre service client avec les détails de votre transaction</li>
                </ul>
            </div>

            <div class="contact-info">
                <p><strong>Besoin d'aide ?</strong></p>
                <p>Contactez notre service client pour résoudre ce problème rapidement.</p>
                @if($commande->restaurateur->phone)
                <p><strong>Restaurant :</strong> {{ $commande->restaurateur->phone }}</p>
                @endif
            </div>

            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                <strong>Important :</strong> Si vous n'avez pas encore effectué le paiement, cette commande ne sera pas traitée.
                Veuillez effectuer le paiement et contacter le restaurant pour confirmation.
            </p>
        </div>

        <div class="footer">
            <p>Nous sommes désolés pour ce désagrément.</p>
            <p><strong>L'équipe Lelagali</strong></p>
        </div>
    </div>
</body>

</html>
