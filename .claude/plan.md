# Plan d'implémentation des notifications email pour Lelagali

## 📋 Vue d'ensemble

Implémentation d'un système complet de notifications par email pour tous les acteurs de la plateforme Lelagali (Admin, Restaurateurs, Clients).

## 🎨 Design Pattern Existant

Le projet utilise déjà :
- **Mailable classes** dans `app/Mail/`
- **Templates Blade** dans `resources/views/emails/`
- **Couleurs Lelagali** :
  - Vert : `#47A547`
  - Orange : `#E6782C`
  - Jaune : `#F8C346`
  - Marron : `#4B2E1E`
  - Beige : `#FDF6EC`
- **Logo** : `public/pic.jpg`

## 📧 Notifications à implémenter

### 1. **ADMIN** - Création d'utilisateur
**Trigger** : Quand un admin crée un utilisateur (client, restaurateur ou admin)
**Destinataire** : L'utilisateur créé
**Contenu** : Identifiants de connexion (téléphone + mot de passe)
**Fichiers** :
- ✅ Déjà existant : `app/Mail/UserRegistrationMail.php`
- ✅ Déjà existant : `resources/views/emails/user-registration.blade.php`
- ✅ Déjà implémenté dans : `UserController::store()` ligne 84

**Action** : Aucune modification nécessaire

---

### 2. **ADMIN** - Approbation/Rejet de plat
**Trigger** : Quand un admin approuve ou rejette un plat
**Destinataire** : Le restaurateur qui a créé le plat
**Contenu** :
- Notification d'approbation avec détails du plat
- Notification de rejet avec raison (optionnelle)

**Fichiers à créer** :
- `app/Mail/PlatApprovedMail.php`
- `app/Mail/PlatRejectedMail.php`
- `resources/views/emails/plat-approved.blade.php`
- `resources/views/emails/plat-rejected.blade.php`

**Fichiers à modifier** :
- `app/Http/Controllers/PlatController.php` (méthodes `approve()` et `reject()`)

---

### 3. **ADMIN** - Changement de mot de passe
**Trigger** : Quand un admin change le mot de passe d'un utilisateur
**Destinataire** : L'utilisateur dont le mot de passe a été changé
**Contenu** : Nouveau mot de passe

**Fichiers** :
- ✅ Déjà existant : `app/Mail/UserPasswordResetMail.php`
- ✅ Déjà existant : `resources/views/emails/password-reset.blade.php`
- ✅ Déjà implémenté dans : `UserController::resetPassword()` ligne 254

**Action** : Aucune modification nécessaire

---

### 4. **ADMIN** - Suspension d'utilisateur
**Trigger** : Quand un admin suspend un utilisateur (restaurateur, client ou admin)
**Destinataire** : L'utilisateur suspendu
**Contenu** : Notification de suspension avec raison (optionnelle)

**Fichiers à créer** :
- `app/Mail/UserSuspendedMail.php`
- `resources/views/emails/user-suspended.blade.php`

**Fichiers à modifier** :
- `app/Http/Controllers/Auth/UserController.php` (méthode `update()` - détecter quand `active` passe à `false`)

---

### 5. **RESTAURANT** - Réception d'une nouvelle commande
**Trigger** : Quand un client passe une commande
**Destinataire** : Le restaurateur
**Contenu** :
- Numéro de commande
- Détails des plats
- Informations client
- Adresse de livraison (si applicable)
- Total
- Moyen de paiement

**Fichiers à créer** :
- `app/Mail/NewOrderRestaurantMail.php`
- `resources/views/emails/new-order-restaurant.blade.php`

**Fichiers à modifier** :
- Trouver où les commandes sont créées (probablement dans un `CommandeController` ou endpoint API)

---

### 6. **CLIENT** - Confirmation de commande
**Trigger** : Quand un client passe une commande
**Destinataire** : Le client
**Contenu** :
- Numéro de commande
- Détails des plats commandés
- Total
- Temps de préparation estimé
- Type de service (livraison/retrait)

**Fichiers à créer** :
- `app/Mail/OrderConfirmationMail.php`
- `resources/views/emails/order-confirmation.blade.php`

**Fichiers à modifier** :
- Même endpoint que #5

---

### 7. **CLIENT** - Changement de statut de commande
**Trigger** : Quand un restaurateur change le statut d'une commande
**Destinataire** : Le client
**Contenu** :
- Nouveau statut (confirmée, prête, en livraison, récupérée)
- Numéro de commande
- Message personnalisé selon le statut

**Statuts possibles** :
- `en_attente` → `confirmee` : "Votre commande a été confirmée"
- `confirmee` → `prete` : "Votre commande est prête"
- `prete` → `en_livraison` : "Votre commande est en cours de livraison"
- `en_livraison` → `recuperee` : "Votre commande a été livrée"

**Fichiers à créer** :
- `app/Mail/OrderStatusChangedMail.php`
- `resources/views/emails/order-status-changed.blade.php`

**Fichiers à modifier** :
- `app/Http/Controllers/CommandeController.php` (méthode `update()`)
- Possiblement les méthodes du modèle `Commande` : `accepter()`, `marquerPrete()`, `mettreEnLivraison()`, `marquerRecuperee()`

---

### 8. **CLIENT** - Refus de confirmation de paiement
**Trigger** : Quand un restaurateur ne confirme pas le paiement
**Destinataire** : Le client
**Contenu** :
- Notification de refus de paiement
- Raison (optionnelle)
- Numéro de commande

**Fichiers à créer** :
- `app/Mail/PaymentRejectedMail.php`
- `resources/views/emails/payment-rejected.blade.php`

**Fichiers à modifier** :
- Trouver l'endpoint de confirmation de paiement (probablement dans `CommandeController`)

---

### 9. **RESTAURANT** - Annulation de commande par le client
**Trigger** : Quand un client annule sa commande
**Destinataire** : Le restaurateur
**Contenu** :
- Notification d'annulation
- Numéro de commande
- Nom du client
- Raison d'annulation (optionnelle)

**Fichiers à créer** :
- `app/Mail/OrderCancelledByClientMail.php`
- `resources/views/emails/order-cancelled-by-client.blade.php`

**Fichiers à modifier** :
- Trouver l'endpoint d'annulation de commande (probablement dans `CommandeController`)
- Possiblement la méthode `annuler()` du modèle `Commande`

---

## 🏗️ Structure de fichiers

```
app/Mail/
├── UserRegistrationMail.php (✅ existe)
├── UserPasswordResetMail.php (✅ existe)
├── UserCredentialsUpdateMail.php (✅ existe)
├── PlatApprovedMail.php (➕ nouveau)
├── PlatRejectedMail.php (➕ nouveau)
├── UserSuspendedMail.php (➕ nouveau)
├── NewOrderRestaurantMail.php (➕ nouveau)
├── OrderConfirmationMail.php (➕ nouveau)
├── OrderStatusChangedMail.php (➕ nouveau)
├── PaymentRejectedMail.php (➕ nouveau)
└── OrderCancelledByClientMail.php (➕ nouveau)

resources/views/emails/
├── user-registration.blade.php (✅ existe)
├── password-reset.blade.php (✅ existe)
├── user-credentials-update.blade.php (✅ existe)
├── plat-approved.blade.php (➕ nouveau)
├── plat-rejected.blade.php (➕ nouveau)
├── user-suspended.blade.php (➕ nouveau)
├── new-order-restaurant.blade.php (➕ nouveau)
├── order-confirmation.blade.php (➕ nouveau)
├── order-status-changed.blade.php (➕ nouveau)
├── payment-rejected.blade.php (➕ nouveau)
└── order-cancelled-by-client.blade.php (➕ nouveau)
```

## 🎯 Ordre d'implémentation

1. **Créer un template email de base** avec le logo pic.jpg
2. **Plats (Admin → Restaurateur)**
   - PlatApprovedMail
   - PlatRejectedMail
3. **Suspension (Admin → Utilisateur)**
   - UserSuspendedMail
4. **Commandes (Client → Restaurant / Restaurant → Client)**
   - NewOrderRestaurantMail
   - OrderConfirmationMail
   - OrderStatusChangedMail
   - PaymentRejectedMail
   - OrderCancelledByClientMail

## ⚠️ Points d'attention

1. **Logo** : Utiliser `public/pic.jpg` dans les templates (accessible via asset('pic.jpg'))
2. **Email nullable** : Certains utilisateurs n'ont pas d'email (uniquement téléphone)
   - Toujours vérifier `if ($user->email)` avant d'envoyer
3. **Transactions** : Les envois d'emails doivent être dans les transactions DB existantes
4. **Queue** : Considérer l'utilisation de queues pour les emails (déjà configuré : `QUEUE_CONNECTION=database`)
5. **Testing** : MAIL_MAILER=log par défaut, vérifier les emails dans `storage/logs/laravel.log`

## 🔍 Résultats des recherches

✅ **Commandes** :
- Création : `Route::post('commandes', [CommandeController::class, 'store'])` (ligne 14 & 31 de app.php) - **MÉTHODE NON IMPLÉMENTÉE**
- Mise à jour : `CommandeController::update()` (ligne 124)
- Changement de statut : Routes dans `app.php` lignes 49-110 (closures directes)
  - `/accept` → `$commande->accepter()`
  - `/ready` → `$commande->marquerPrete()`
  - `/deliver` → `$commande->mettreEnLivraison()`
  - `/complete` → `$commande->marquerRecuperee()`
  - `/cancel` → `$commande->annuler($raison)`
  - `/mark-paid` → `$commande->marquerPayee($reference, $numero)`

✅ **Suspension** :
- Le modèle User a un champ `active` (boolean)
- La suspension se fait via `UserController::update()` en changeant le champ `active`
- Pas de méthode dédiée de suspension

✅ **Refus de paiement** :
- Aucun endpoint trouvé pour refuser un paiement
- **À CRÉER** : Un endpoint pour rejeter le paiement

## 📝 Notes d'implémentation

### Points critiques identifiés

1. **CommandeController::store() n'existe pas**
   - Il faut l'implémenter avec envoi d'emails au restaurant et au client

2. **Changements de statut sont dans des closures** (routes/routers/app.php)
   - Il faut ajouter les envois d'emails directement dans ces closures
   - OU déplacer la logique dans des méthodes du controller

3. **Aucun endpoint de rejet de paiement**
   - À créer dans CommandeController ou dans une route closure

4. **Suspension d'utilisateur**
   - Détecter le changement `active: true → false` dans UserController::update()
   - Envoyer l'email dans cette condition

## ✅ Validation

Après implémentation, tester :
1. Création utilisateur → Email reçu ✅ (déjà fait)
2. Reset password → Email reçu ✅ (déjà fait)
3. Approbation plat → Email au restaurateur
4. Rejet plat → Email au restaurateur
5. Suspension → Email à l'utilisateur
6. Nouvelle commande → Email au restaurant et au client
7. Changement statut → Email au client
8. Refus paiement → Email au client
9. Annulation → Email au restaurant
