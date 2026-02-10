# Documentation - Système de Notifications Email Lelagali

## 📧 Vue d'ensemble

Ce document décrit l'implémentation complète du système de notifications par email pour la plateforme Lelagali. Tous les emails utilisent le design cohérent de la marque avec le logo `public/pic.jpg` et les couleurs officielles.

## 🎨 Couleurs utilisées

- **Vert** : `#47A547` - Succès, approbation
- **Orange** : `#E6782C` - Avertissement, action requise
- **Jaune** : `#F8C346` - Information
- **Marron** : `#4B2E1E` - Texte principal
- **Beige** : `#FDF6EC` - Fond secondaire

---

## 📋 Liste des notifications implémentées

### 1. Création d'utilisateur ✅ (Déjà existant)
**Trigger** : Admin crée un utilisateur
**Destinataire** : Utilisateur créé
**Classe** : `App\Mail\UserRegistrationMail`
**Template** : `resources/views/emails/user-registration.blade.php`
**Controller** : `UserController::store()` ligne 84

### 2. Changement de mot de passe ✅ (Déjà existant)
**Trigger** : Admin change le mot de passe
**Destinataire** : Utilisateur concerné
**Classe** : `App\Mail\UserPasswordResetMail`
**Template** : `resources/views/emails/password-reset.blade.php`
**Controller** : `UserController::resetPassword()` ligne 254

### 3. Approbation de plat ✅ (Nouveau)
**Trigger** : Admin approuve un plat
**Destinataire** : Restaurateur
**Classe** : `App\Mail\PlatApprovedMail`
**Template** : `resources/views/emails/plat-approved.blade.php`
**Controller** : `PlatController::approve()`

**Contenu** :
- Badge de succès vert
- Détails du plat approuvé
- Message de félicitation
- Instructions pour maintenir la qualité

### 4. Rejet de plat ✅ (Nouveau)
**Trigger** : Admin rejette un plat
**Destinataire** : Restaurateur
**Classe** : `App\Mail\PlatRejectedMail`
**Template** : `resources/views/emails/plat-rejected.blade.php`
**Controller** : `PlatController::reject()`

**Paramètres optionnels** :
- `raison` : Raison du rejet

**Contenu** :
- Badge d'avertissement orange
- Détails du plat rejeté
- Raison du rejet (si fournie)
- Conseils pour réussir la prochaine soumission

### 5. Suspension d'utilisateur ✅ (Nouveau)
**Trigger** : Admin suspend un compte (active: true → false)
**Destinataire** : Utilisateur suspendu
**Classe** : `App\Mail\UserSuspendedMail`
**Template** : `resources/views/emails/user-suspended.blade.php`
**Controller** : `UserController::update()`

**Paramètres optionnels** :
- `raison_suspension` : Raison de la suspension

**Contenu** :
- Badge d'avertissement orange
- Informations du compte
- Raison de suspension (si fournie)
- Coordonnées du support

### 6. Nouvelle commande (Restaurant) ✅ (Nouveau)
**Trigger** : Client passe une commande
**Destinataire** : Restaurateur
**Classe** : `App\Mail\NewOrderRestaurantMail`
**Template** : `resources/views/emails/new-order-restaurant.blade.php`
**Controller** : `CommandeController::store()`

**Contenu** :
- Badge de notification vert
- Numéro de commande
- Informations client (nom, téléphone, email)
- Type de service (livraison/retrait)
- Adresse de livraison (si applicable)
- Détails des plats commandés
- Total à payer
- Moyen de paiement
- Appel à l'action pour confirmer

### 7. Confirmation de commande (Client) ✅ (Nouveau)
**Trigger** : Client passe une commande
**Destinataire** : Client
**Classe** : `App\Mail\OrderConfirmationMail`
**Template** : `resources/views/emails/order-confirmation.blade.php`
**Controller** : `CommandeController::store()`

**Contenu** :
- Badge de confirmation vert
- Numéro de commande
- Nom du restaurant
- Temps de préparation estimé
- Type de service
- Détails des plats
- Total à payer
- Statut du paiement
- Information sur le suivi

### 8. Changement de statut (Client) ✅ (Nouveau)
**Trigger** : Restaurant change le statut de la commande
**Destinataire** : Client
**Classe** : `App\Mail\OrderStatusChangedMail`
**Template** : `resources/views/emails/order-status-changed.blade.php`
**Routes** :
- `PATCH /app/commandes/{id}/accept` → confirmee
- `PATCH /app/commandes/{id}/ready` → prete
- `PATCH /app/commandes/{id}/deliver` → en_livraison
- `PATCH /app/commandes/{id}/complete` → recuperee

**Statuts gérés** :
- **confirmee** : Commande confirmée par le restaurant
- **prete** : Commande prête pour retrait/livraison
- **en_livraison** : Commande en cours de livraison
- **recuperee** : Commande livrée/récupérée

**Contenu** :
- Badge de statut (couleur selon statut)
- Message personnalisé selon le statut
- Barre de progression visuelle
- Récapitulatif de la commande
- Prochaines étapes

### 9. Refus de paiement (Client) ✅ (Nouveau)
**Trigger** : Restaurant rejette le paiement
**Destinataire** : Client
**Classe** : `App\Mail\PaymentRejectedMail`
**Template** : `resources/views/emails/payment-rejected.blade.php`
**Route** : `PATCH /app/commandes/{id}/reject-payment`

**Paramètres optionnels** :
- `raison` : Raison du refus

**Contenu** :
- Badge d'avertissement orange
- Numéro de commande
- Raison du refus (si fournie)
- Détails de la transaction
- Actions à entreprendre
- Coordonnées du support

### 10. Annulation par client (Restaurant) ✅ (Nouveau)
**Trigger** : Client annule sa commande
**Destinataire** : Restaurateur
**Classe** : `App\Mail\OrderCancelledByClientMail`
**Template** : `resources/views/emails/order-cancelled-by-client.blade.php`
**Route** : `PATCH /app/commandes/{id}/cancel` avec `cancelled_by: 'client'`

**Contenu** :
- Badge d'annulation orange
- Informations du client
- Raison d'annulation (si fournie)
- Détails de la commande annulée
- Instructions pour la gestion du stock

---

## 🔧 Utilisation

### Envoyer un email manuellement

```php
use App\Mail\PlatApprovedMail;
use Illuminate\Support\Facades\Mail;

$plat = Plat::find($platId);

Mail::to($plat->restaurateur->email)->send(new PlatApprovedMail($plat));
```

### Vérifier qu'un email existe avant envoi

Tous les emails vérifient automatiquement si l'utilisateur a un email :

```php
if ($user->email) {
    Mail::to($user->email)->send(new SomeEmail($user));
}
```

### Configuration mail (fichier .env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@lelagali.com"
MAIL_FROM_NAME="Lelagali"
```

Pour le développement local :
```env
MAIL_MAILER=log
```
Les emails seront enregistrés dans `storage/logs/laravel.log`

---

## 📁 Structure des fichiers

```
app/Mail/
├── UserRegistrationMail.php (existant)
├── UserPasswordResetMail.php (existant)
├── UserCredentialsUpdateMail.php (existant)
├── PlatApprovedMail.php (nouveau)
├── PlatRejectedMail.php (nouveau)
├── UserSuspendedMail.php (nouveau)
├── NewOrderRestaurantMail.php (nouveau)
├── OrderConfirmationMail.php (nouveau)
├── OrderStatusChangedMail.php (nouveau)
├── PaymentRejectedMail.php (nouveau)
└── OrderCancelledByClientMail.php (nouveau)

resources/views/emails/
├── user-registration.blade.php (existant)
├── password-reset.blade.php (existant)
├── user-credentials-update.blade.php (existant)
├── plat-approved.blade.php (nouveau)
├── plat-rejected.blade.php (nouveau)
├── user-suspended.blade.php (nouveau)
├── new-order-restaurant.blade.php (nouveau)
├── order-confirmation.blade.php (nouveau)
├── order-status-changed.blade.php (nouveau)
├── payment-rejected.blade.php (nouveau)
└── order-cancelled-by-client.blade.php (nouveau)
```

---

## 🧪 Tests

### Tester l'envoi d'emails en local

1. Configurer `.env` avec `MAIL_MAILER=log`
2. Effectuer une action qui déclenche un email
3. Vérifier dans `storage/logs/laravel.log`

### Tester avec Mailtrap (recommandé)

1. Créer un compte sur [Mailtrap.io](https://mailtrap.io)
2. Configurer les credentials dans `.env`
3. Tous les emails seront interceptés dans Mailtrap

### Tester tous les emails

```bash
# Approbation de plat
PUT /api/plats/{plat}/approve

# Rejet de plat
PUT /api/plats/{plat}/reject
Body: {"raison": "Image de mauvaise qualité"}

# Suspension d'utilisateur
PUT /api/users/{user}
Body: {"active": false, "raison_suspension": "Violation des conditions"}

# Création de commande
POST /api/commandes
Body: {voir structure dans CommandeController}

# Changement de statut
PATCH /api/app/commandes/{id}/accept
PATCH /api/app/commandes/{id}/ready
PATCH /api/app/commandes/{id}/deliver
PATCH /api/app/commandes/{id}/complete

# Refus de paiement
PATCH /api/app/commandes/{id}/reject-payment
Body: {"raison": "Transaction non reçue"}

# Annulation par client
PATCH /api/app/commandes/{id}/cancel
Body: {"raison": "Changement d'avis", "cancelled_by": "client"}
```

---

## ⚠️ Points importants

1. **Email nullable** : Certains utilisateurs n'ont que le téléphone, vérifier toujours `if ($user->email)`
2. **Transactions** : Les emails sont envoyés APRÈS le commit de la transaction
3. **Queue** : Pour la production, configurer `QUEUE_CONNECTION=database` et lancer `php artisan queue:work`
4. **Logo** : Le logo est dans `public/pic.jpg`, accessible via `asset('pic.jpg')`
5. **Couleurs** : Respecter la charte graphique Lelagali dans tous les templates

---

## 🚀 Améliorations futures possibles

- [ ] Ajouter une file d'attente (queue) pour les emails en production
- [ ] Créer des notifications push en complément des emails
- [ ] Ajouter des statistiques d'envoi d'emails
- [ ] Permettre aux utilisateurs de gérer leurs préférences de notification
- [ ] Ajouter des templates pour SMS (pour utilisateurs sans email)
- [ ] Internationalisation (i18n) pour supporter plusieurs langues
- [ ] Version HTML + texte brut pour chaque email

---

## 📞 Support

Pour toute question ou problème :
- Vérifier les logs : `storage/logs/laravel.log`
- Vérifier la configuration mail dans `.env`
- Tester avec Mailtrap avant la production
- S'assurer que le serveur SMTP est accessible

---

**Date d'implémentation** : 10 février 2026
**Version** : 1.0.0
**Statut** : ✅ Complet et opérationnel
