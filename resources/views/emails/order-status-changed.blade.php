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

        .status-badge {
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .status-confirmee {
            background-color: #47A547;
        }

        .status-prete {
            background-color: #2196F3;
        }

        .status-en_livraison {
            background-color: #E6782C;
        }

        .status-recuperee {
            background-color: #4CAF50;
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

        .status-info {
            background-color: #FDF6EC;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #47A547;
        }

        .status-info h3 {
            color: #47A547;
            margin-top: 0;
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .progress-step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .progress-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .progress-circle.active {
            background-color: #47A547;
        }

        .progress-step-label {
            font-size: 12px;
            color: #666;
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
            @php
                $statusLabels = [
                    'confirmee' => 'Commande confirmée',
                    'prete' => 'Commande prête',
                    'en_livraison' => 'En cours de livraison',
                    'recuperee' => 'Commande livrée'
                ];
                $statusLabel = $statusLabels[$newStatus] ?? 'Mise à jour';
            @endphp
            <div class="status-badge status-{{ $newStatus }}">
                @if($newStatus === 'confirmee')
                    ✓
                @elseif($newStatus === 'prete')
                    🍽️
                @elseif($newStatus === 'en_livraison')
                    🚗
                @elseif($newStatus === 'recuperee')
                    ✓
                @endif
                {{ strtoupper($statusLabel) }}
            </div>
        </div>

        <div class="order-number">
            #{{ $commande->numero_commande }}
        </div>

        <div class="content">
            <h2>{{ $statusLabel }}</h2>

            @if($newStatus === 'confirmee')
                <p>Bonjour {{ $commande->client->name }},</p>
                <p>Bonne nouvelle ! Le restaurant <strong>{{ $commande->restaurateur->name }}</strong> a confirmé votre commande et commence la préparation.</p>
                @if($commande->temps_preparation_estime)
                    <p>Temps de préparation estimé : <strong>{{ $commande->temps_preparation_estime }} minutes</strong></p>
                @endif

            @elseif($newStatus === 'prete')
                <p>Bonjour {{ $commande->client->name }},</p>
                <p>Votre commande est prête ! 🎉</p>
                @if($commande->type_service === 'retrait')
                    <p>Vous pouvez venir récupérer votre commande chez <strong>{{ $commande->restaurateur->name }}</strong>.</p>
                @else
                    <p>Votre commande va bientôt être livrée à votre adresse.</p>
                @endif

            @elseif($newStatus === 'en_livraison')
                <p>Bonjour {{ $commande->client->name }},</p>
                <p>Votre commande est en route ! 🚗</p>
                <p>Le livreur est en chemin vers votre adresse : <strong>{{ $commande->adresse_livraison }}</strong></p>

            @elseif($newStatus === 'recuperee')
                <p>Bonjour {{ $commande->client->name }},</p>
                <p>Votre commande a été {{ $commande->type_service === 'livraison' ? 'livrée' : 'récupérée' }} avec succès ! ✓</p>
                <p>Nous espérons que vous apprécierez votre repas. Merci d'avoir choisi Lelagali !</p>
            @endif

            <div class="progress-bar">
                <div class="progress-step">
                    <div class="progress-circle active">1</div>
                    <div class="progress-step-label">En attente</div>
                </div>
                <div class="progress-step">
                    <div class="progress-circle {{ in_array($newStatus, ['confirmee', 'prete', 'en_livraison', 'recuperee']) ? 'active' : '' }}">2</div>
                    <div class="progress-step-label">Confirmée</div>
                </div>
                <div class="progress-step">
                    <div class="progress-circle {{ in_array($newStatus, ['prete', 'en_livraison', 'recuperee']) ? 'active' : '' }}">3</div>
                    <div class="progress-step-label">Prête</div>
                </div>
                @if($commande->type_service === 'livraison')
                <div class="progress-step">
                    <div class="progress-circle {{ in_array($newStatus, ['en_livraison', 'recuperee']) ? 'active' : '' }}">4</div>
                    <div class="progress-step-label">En livraison</div>
                </div>
                @endif
                <div class="progress-step">
                    <div class="progress-circle {{ $newStatus === 'recuperee' ? 'active' : '' }}">{{ $commande->type_service === 'livraison' ? '5' : '4' }}</div>
                    <div class="progress-step-label">{{ $commande->type_service === 'livraison' ? 'Livrée' : 'Récupérée' }}</div>
                </div>
            </div>

            <div class="order-details">
                <h3>Récapitulatif de la commande</h3>
                <div class="detail-row">
                    <span class="detail-label">Restaurant :</span>
                    <span>{{ $commande->restaurateur->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Type de service :</span>
                    <span>{{ $commande->type_service === 'livraison' ? 'Livraison' : 'Retrait' }}</span>
                </div>
                @if($commande->type_service === 'livraison')
                <div class="detail-row">
                    <span class="detail-label">Adresse :</span>
                    <span>{{ $commande->adresse_livraison }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Montant total :</span>
                    <span><strong>{{ number_format($commande->total_general, 0, ',', ' ') }} FCFA</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Statut du paiement :</span>
                    <span>{{ $commande->status_paiement ? 'Payé' : 'Non payé' }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Bon appétit !</strong></p>
            <p>L'équipe Lelagali</p>
        </div>
    </div>
</body>

</html>
