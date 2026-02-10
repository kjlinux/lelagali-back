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

        .order-details {
            background-color: #FDF6EC;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #47A547;
        }

        .order-details h3 {
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
            width: 180px;
        }

        .detail-value {
            color: #666;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background-color: #47A547;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .total-box {
            background-color: #E6782C;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .total-box .amount {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
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

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-livraison {
            background-color: #2196F3;
            color: white;
        }

        .badge-retrait {
            background-color: #F8C346;
            color: #4B2E1E;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="success-badge">✓ COMMANDE CONFIRMÉE</div>
        </div>

        <div class="order-number">
            #{{ $commande->numero_commande }}
        </div>

        <div class="content">
            <h2>Merci pour votre commande {{ $commande->client->name }} !</h2>
            <p>Votre commande a bien été enregistrée et transmise au restaurant. Vous recevrez une notification dès que le restaurant aura confirmé votre commande.</p>

            <div class="order-details">
                <h3>Résumé de votre commande</h3>

                <div class="detail-row">
                    <span class="detail-label">Restaurant :</span>
                    <span class="detail-value">{{ $commande->restaurateur->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date de commande :</span>
                    <span class="detail-value">{{ $commande->created_at->format('d/m/Y à H:i') }}</span>
                </div>

                @if($commande->temps_preparation_estime)
                <div class="detail-row">
                    <span class="detail-label">Temps de préparation estimé :</span>
                    <span class="detail-value">{{ $commande->temps_preparation_estime }} minutes</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Type de service :</span>
                    <span class="detail-value">
                        <span class="badge {{ $commande->type_service === 'livraison' ? 'badge-livraison' : 'badge-retrait' }}">
                            {{ $commande->type_service === 'livraison' ? 'Livraison' : 'Retrait' }}
                        </span>
                    </span>
                </div>

                @if($commande->type_service === 'livraison')
                <div class="detail-row">
                    <span class="detail-label">Adresse de livraison :</span>
                    <span class="detail-value">{{ $commande->adresse_livraison }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Moyen de paiement :</span>
                    <span class="detail-value">{{ $commande->moyenPaiement->nom }}</span>
                </div>
            </div>

            <h3>Détails de votre commande</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Qté</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commande->items as $item)
                    <tr>
                        <td>{{ $item->plat->nom }}</td>
                        <td>{{ $item->quantite }}</td>
                        <td>{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                        <td><strong>{{ number_format($item->prix_total, 0, ',', ' ') }} FCFA</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align: right; padding: 10px 0;">
                <div style="margin: 5px 0;">
                    <strong>Total plats :</strong> {{ number_format($commande->total_plats, 0, ',', ' ') }} FCFA
                </div>
                @if($commande->frais_livraison > 0)
                <div style="margin: 5px 0;">
                    <strong>Frais de livraison :</strong> {{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA
                </div>
                @endif
            </div>

            <div class="total-box">
                <div>MONTANT TOTAL À PAYER</div>
                <div class="amount">{{ number_format($commande->total_general, 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="info-box">
                <p><strong>Suivi de votre commande</strong></p>
                <p>Vous recevrez des notifications par email à chaque changement de statut de votre commande :</p>
                <ul>
                    <li>Commande confirmée par le restaurant</li>
                    <li>Commande prête</li>
                    @if($commande->type_service === 'livraison')
                    <li>Commande en cours de livraison</li>
                    @endif
                    <li>Commande {{ $commande->type_service === 'livraison' ? 'livrée' : 'récupérée' }}</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong>Bon appétit !</strong></p>
            <p>L'équipe Lelagali</p>
        </div>
    </div>
</body>

</html>
