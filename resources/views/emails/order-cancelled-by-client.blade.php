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

        .client-info {
            background-color: #FDF6EC;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #E6782C;
        }

        .client-info h3 {
            color: #4B2E1E;
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
            background-color: #ffebee;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #f44336;
        }

        .raison-box h4 {
            color: #c62828;
            margin-top: 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background-color: #E6782C;
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
            <img src="{{ asset('pic.jpg') }}" alt="Lelagali" class="logo">
            <div class="warning-badge">✖ COMMANDE ANNULÉE</div>
        </div>

        <div class="order-number">
            #{{ $commande->numero_commande }}
        </div>

        <div class="content">
            <h2>Annulation de commande</h2>

            <div class="alert-box">
                <h3>Un client a annulé sa commande</h3>
                <p>Nous vous informons qu'une commande a été annulée par le client.</p>
            </div>

            <div class="client-info">
                <h3>Informations du client</h3>

                <div class="detail-row">
                    <span class="detail-label">Nom :</span>
                    <span class="detail-value">{{ $commande->client->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Téléphone :</span>
                    <span class="detail-value">{{ $commande->client->phone }}</span>
                </div>

                @if($commande->client->email)
                <div class="detail-row">
                    <span class="detail-label">Email :</span>
                    <span class="detail-value">{{ $commande->client->email }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Date de commande :</span>
                    <span class="detail-value">{{ $commande->created_at->format('d/m/Y à H:i') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date d'annulation :</span>
                    <span class="detail-value">{{ now()->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            @if($commande->raison_annulation)
            <div class="raison-box">
                <h4>Raison de l'annulation :</h4>
                <p>{{ $commande->raison_annulation }}</p>
            </div>
            @endif

            <h3>Détails de la commande annulée</h3>
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
                <div style="margin: 10px 0; font-size: 18px;">
                    <strong>Total :</strong> <strong style="color: #E6782C;">{{ number_format($commande->total_general, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>

            <p style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
                <strong>Action requise :</strong> Si la préparation avait déjà commencé, veuillez mettre à jour votre stock en conséquence.
                Si un paiement avait été reçu, merci de gérer le remboursement selon vos conditions.
            </p>
        </div>

        <div class="footer">
            <p>Cette annulation a été enregistrée dans le système.</p>
            <p><strong>L'équipe Lelagali</strong></p>
        </div>
    </div>
</body>

</html>
