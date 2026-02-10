# Lelagali Back - Contexte du Projet

## Description

**Lelagali** est une plateforme de commande de repas en ligne (type food delivery) basee en Afrique de l'Ouest (monnaie FCFA). Le backend est une API REST construite avec Laravel 12, deployee sur un environnement Laragon (Windows).

L'application connecte trois types d'acteurs : **clients**, **restaurateurs** et **admins**.

---

## Stack Technique

| Composant            | Technologie                        |
|----------------------|------------------------------------|
| Framework            | Laravel 12 (PHP 8.2+)             |
| Base de donnees      | PostgreSQL (`lelagali_back`)       |
| Authentification     | JWT via `tymon/jwt-auth` v2.2      |
| Permissions          | `spatie/laravel-permission` v6     |
| IDs                  | UUID sur toutes les tables         |
| Soft Delete          | Actif sur tous les modeles         |
| Queue                | Database                           |
| Cache / Session      | Database                           |
| Mail                 | Log (dev), SMTP prevu en prod      |
| Tests                | Pest v3                            |
| Linter               | Laravel Pint                       |

---

## Architecture du Projet

```
app/
  Http/
    Controllers/
      Auth/
        UserController.php        # Auth (login/logout/refresh) + CRUD users
      CommandeController.php      # CRUD commandes avec filtres/pagination
      CommandeItemController.php  # Items de commande
      PlatController.php          # Gestion des plats/menus + moderation + stats
      QuartierController.php      # CRUD quartiers
      MoyenPaiementController.php # CRUD moyens de paiement
      RestaurateurMoyenPaiementController.php  # Pivot restaurateur<->paiement
      TarifLivraisonController.php # Tarifs livraison par quartier/restaurateur
      NotificationCommandeController.php # Notifications in-app
    Requests/                     # Form Requests pour validation
  Mail/
    UserRegistrationMail.php      # Email d'inscription
    UserPasswordResetMail.php     # Email reset mot de passe
    UserCredentialsUpdateMail.php # Email mise a jour credentials
  Models/                         # 9 modeles Eloquent (voir schema)
  Providers/
    AppServiceProvider.php        # defaultStringLength(191)
routes/
  api.php                         # Point d'entree, delegue vers routers/
  routers/
    auth.php                      # Routes auth + users (prefix: /api/auth)
    app.php                       # Routes metier (prefix: /api/app)
    settings.php                  # Routes config (prefix: /api/settings)
database/
  migrations/                     # 14 migrations
  seeders/                        # Seeders complets pour toutes les tables
```

---

## Schema de la Base de Donnees

### Tables principales

#### `users` (UUID)
- `name`, `email` (nullable, unique), `phone` (unique), `password`
- `role` : enum `client` | `restaurateur` | `admin`
- `active` : boolean
- `profile_image`, `address` (nullable)
- `quartier_id` -> `quartiers.id`
- SoftDeletes, timestamps

#### `quartiers` (UUID)
- `nom`
- `created_by` -> `users.id` (nullable)
- SoftDeletes, timestamps

#### `plats` (UUID)
- `nom`, `description`, `prix` (int, FCFA)
- `quantite_disponible`, `quantite_vendue`
- `image` (nullable, stockee dans `storage/app/public/menus`)
- `restaurateur_id` -> `users.id`
- `date_disponibilite` (date) - concept de "menu du jour"
- `is_approved` (boolean), `approved_by` -> `users.id`, `approved_at`
- `temps_preparation` (minutes, nullable)
- Index composite : `[date_disponibilite, is_approved]`
- SoftDeletes, timestamps

#### `commandes` (UUID)
- `numero_commande` (auto-genere : `CMD-YmdHis-XXXX`)
- `client_id` -> `users.id`, `restaurateur_id` -> `users.id`
- `total_plats`, `frais_livraison`, `total_general` (int, FCFA)
- `type_service` : enum `livraison` | `retrait`
- `adresse_livraison`, `quartier_livraison_id` -> `quartiers.id`
- `moyen_paiement_id` -> `moyen_paiements.id`
- `status` : enum `en_attente` | `confirmee` | `prete` | `en_livraison` | `recuperee` | `annulee`
- `status_paiement` (boolean), `reference_paiement`, `numero_paiement`
- `notes_client`, `notes_restaurateur`, `raison_annulation`
- `temps_preparation_estime`
- SoftDeletes, timestamps

#### `commande_items` (UUID)
- `commande_id` -> `commandes.id`, `plat_id` -> `plats.id`
- `quantite`, `prix_unitaire`, `prix_total` (calcul auto)
- SoftDeletes, timestamps

#### `moyen_paiements` (UUID)
- `nom`, `icon` (nullable)
- `created_by` -> `users.id`
- SoftDeletes, timestamps

#### `restaurateur_moyens_paiement` (UUID, table pivot)
- `restaurateur_id` -> `users.id`, `moyen_paiement_id` -> `moyen_paiements.id`
- `numero_compte`, `nom_titulaire`
- `status` : enum `active` | `inactive`
- Contrainte unique : `[restaurateur_id, moyen_paiement_id]`
- SoftDeletes, timestamps

#### `tarif_livraisons` (UUID)
- `restaurateur_id` -> `users.id`, `quartier_id` -> `quartiers.id`
- `prix` (int, FCFA)
- Contrainte unique : `[restaurateur_id, quartier_id]`
- SoftDeletes, timestamps

#### `notification_commandes` (UUID)
- `title`, `message`
- `type` : enum `order` | `user` | `payment` | `system` | `info` | `warning` | `success` | `error`
- `user_id` -> `users.id` (nullable, null = broadcast)
- `is_read`, `read_at`, `action_required`, `data` (JSON)
- Index : `[user_id, is_read]`, `[type, created_at]`
- SoftDeletes, timestamps

---

## Relations Eloquent

```
User
  -> belongsTo Quartier
  -> hasMany Commande (client_id)
  -> hasMany Plat (restaurateur_id)
  -> belongsToMany MoyenPaiement (via restaurateur_moyens_paiement)
  -> hasMany TarifLivraison (restaurateur_id)

Commande
  -> belongsTo User (client_id)
  -> belongsTo User (restaurateur_id)
  -> belongsTo Quartier (quartier_livraison_id)
  -> belongsTo MoyenPaiement
  -> hasMany CommandeItem
  -> hasMany NotificationCommande

Plat
  -> belongsTo User (restaurateur_id)
  -> belongsTo User (approved_by)
  -> hasMany CommandeItem

CommandeItem
  -> belongsTo Commande
  -> belongsTo Plat

Quartier
  -> hasMany User
  -> hasMany TarifLivraison
  -> hasMany Commande (quartier_livraison_id)
  -> belongsTo User (created_by)

MoyenPaiement
  -> belongsToMany User/Restaurateur (via restaurateur_moyens_paiement)
  -> hasMany Commande
  -> belongsTo User (created_by)

TarifLivraison
  -> belongsTo User (restaurateur_id)
  -> belongsTo Quartier

NotificationCommande
  -> belongsTo Commande
  -> belongsTo User
```

---

## Authentification & Securite

- **Guard par defaut** : `api` avec driver `jwt` (pas de guard `web`)
- **Login** : via email + password, retourne un JWT bearer token
- **Endpoints proteges** : middleware `api` sur tous les groupes de routes
- **Roles** : geres via la colonne `role` dans `users` ET via `spatie/laravel-permission` (syncRoles)
- **Autorisations** : verifications manuelles dans les controleurs (`$user->role !== 'admin'`)
- **Mot de passe** : genere automatiquement (12 chars) si non fourni lors de la creation
- **Emails** : envoyes a la creation, modification de credentials, reset de mot de passe

---

## Logique Metier Cle

### Cycle de vie d'une commande
```
en_attente -> confirmee -> prete -> en_livraison -> recuperee
                                 -> recuperee (retrait direct)
       (tout sauf recuperee) -> annulee
```

- Le modele `Commande` contient les methodes de transition d'etat : `accepter()`, `marquerPrete()`, `mettreEnLivraison()`, `marquerRecuperee()`, `annuler()`
- Le numero de commande est auto-genere au format `CMD-YmdHis-XXXX`
- Recalcul automatique des totaux via `recalculerTotaux()`
- Les prix sont en **entiers** (FCFA, pas de centimes)

### Plats / Menus du jour
- Les plats sont lies a une `date_disponibilite` (concept de menu quotidien)
- Workflow de moderation : un admin doit approuver les plats (`is_approved`)
- Stats par restaurateur : menus actifs, commandes du jour, revenu hebdomadaire

### Tarifs de livraison
- Chaque restaurateur definit ses tarifs par quartier
- Contrainte unique `[restaurateur_id, quartier_id]`
- Logique upsert dans le `store()`

### Moyens de paiement
- Types supportes : Mobile Money (Orange, MTN, Moov, Wave, Telecel/Sank), Especes, Carte bancaire
- Chaque restaurateur configure ses moyens de paiement acceptes avec `numero_compte` et `nom_titulaire`
- Detection automatique du type (electronic vs cash) via le nom

---

## Format de Reponse API

Deux formats coexistent dans le projet :

### Format 1 (controleurs Auth, Settings, anciens)
```json
{
  "status": "success|error",
  "code": 200,
  "message": "...",
  "data": {...}
}
```

### Format 2 (controleurs App, Commandes)
```json
{
  "success": true|false,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 50,
    "last_page": 5
  }
}
```

---

## Routes API

### Prefix `/api/auth`
| Methode | URI                           | Action                    |
|---------|-------------------------------|---------------------------|
| POST    | login                         | Connexion JWT             |
| POST    | logout                        | Deconnexion               |
| POST    | refresh                       | Rafraichir token          |
| GET     | users                         | Liste utilisateurs        |
| POST    | users                         | Creer utilisateur         |
| GET     | users/{user}                  | Detail utilisateur        |
| PUT     | users/{user}                  | Modifier utilisateur      |
| PUT     | users/{user}/profile          | Modifier profil           |
| PUT     | users/{user}/reset-password   | Reset mot de passe        |
| PUT     | users/{user}/role             | Modifier role             |
| DELETE  | users/{user}                  | Supprimer (soft)          |
| GET     | users-trashed                 | Liste supprimes           |
| POST    | users/{id}/restore            | Restaurer                 |
| GET     | profile                       | Profil connecte           |

### Prefix `/api/app`
- CRUD Commandes + actions (accept, ready, deliver, complete, cancel, mark-paid)
- CRUD CommandeItems
- CRUD Plats + moderation (approve, reject) + stats + menus du jour
- CRUD RestaurateurMoyenPaiements
- CRUD TarifLivraisons
- Notifications (CRUD + mark-read + unread-count)
- Recherche avancee commandes
- Rapports (ventes, bestsellers)
- Liste quartiers et moyens de paiement

### Prefix `/api/settings`
- CRUD Quartiers
- CRUD MoyenPaiements

---

## Seeders Disponibles

Execution : `php artisan db:seed`

Ordre : UserSeeder -> QuartierSeeder -> MoyenPaiementSeeder -> RestaurateurMoyenPaiementSeeder -> TarifLivraisonSeeder -> PlatSeeder -> CommandeSeeder -> NotificationCommandeSeeder

---

## Conventions du Projet

- **Langue** : Code en anglais (Laravel), noms metier en francais (tables, colonnes, messages)
- **IDs** : UUID partout (trait `HasUuids`)
- **Suppression** : SoftDeletes sur tous les modeles
- **Protection** : `$guarded = ['id']` sur la plupart des modeles (sauf User qui utilise `$fillable`)
- **Validation** : Mix de Form Requests (QuartierRequest, PlatRequest...) et validation inline dans les controleurs
- **Transactions** : `DB::beginTransaction()` / `commit()` / `rollBack()` dans les operations d'ecriture
- **Scopes** : Utilisation intensive de scopes Eloquent pour les filtres recurrents
- **Images** : Stockees sur **Amazon S3** (bucket `lelagali`, region `eu-north-1`)
  - Helper `App\Helpers\StorageHelper` pour abstraire les operations
  - Attribut virtuel `image_url` sur le modele `Plat` retourne l'URL complete S3
  - Upload via `StorageHelper::storeImage()`, suppression via `StorageHelper::delete()`
  - Documentation complete dans `STORAGE_S3.md`
- **Pagination** : Via `paginate()` avec meta (current_page, per_page, total, last_page)

---

## Integration Frontend-Backend

### Configuration CORS

Le backend est configure pour accepter les requetes cross-origin depuis le frontend Vue.js :

- **Fichier** : `config/cors.php`
- **Origins autorisees** : Defini dans `.env` via `CORS_ALLOWED_ORIGINS` (defaut: `http://localhost:5173,http://localhost:3000`)
- **Headers autorises** : Tous (`*`)
- **Methodes autorisees** : Toutes (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`)
- **Middleware** : `HandleCors` active dans `bootstrap/app.php`

### Authentification JWT

- **Login** : `POST /api/auth/login` avec `email` OU `phone` + `password`
- **Register** : `POST /api/auth/users` avec `name`, (`email` OU `phone`), `password`, `role` (defaut: `client`)
- **Logout** : `POST /api/auth/logout` (authentifie)
- **Refresh** : `POST /api/auth/refresh` (authentifie)
- **Profile** : `GET /api/auth/profile` (authentifie)

Reponse login/register :
```json
{
  "status": "success",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": { "id": "uuid", "name": "...", "email": "...", "role": "client" }
  }
}
```

### Frontend (Vue 3 + PrimeVue)

**Emplacement** : `c:\laragon\www\lelagali-client`

**Services crees** :
- `src/service/api.js` : Service HTTP de base (fetch, gestion token JWT, headers, erreurs)
- `src/service/AuthService.js` : Authentification (login, register, logout, profile)
- `src/service/PlatService.js` : Gestion des plats/menus (CRUD, filtres, menus du jour)
- `src/service/CommandeService.js` : Gestion des commandes (CRUD, cycle de vie, rapports)
- `src/service/ReferenceService.js` : Donnees de reference (quartiers, moyens paiement, tarifs)

**Composants integres** :
- `AuthModal.vue` : Connexion et inscription (✅ connecte au backend)
- `PaymentModal.vue` : Finalisation commande (⏳ a integrer)
- `App.vue` : Application principale (⏳ a integrer pour charger menus depuis API)

**Configuration** :
- `.env` : `VITE_API_BASE_URL=http://localhost:8000/api`

**Documentation complete** : Voir `INTEGRATION.md` dans le frontend
