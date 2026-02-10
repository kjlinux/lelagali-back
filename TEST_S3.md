# Guide de Test S3 - LeLagaLi

## Test Rapide de la Configuration S3

Ce guide vous permet de tester rapidement que S3 fonctionne correctement.

---

## Étape 1 : Vérifier la Configuration

### 1.1 Vérifier le fichier `.env`

```bash
# Afficher les variables S3
grep AWS_ c:/laragon/www/lelagali-back/.env
grep FILESYSTEM_DISK c:/laragon/www/lelagali-back/.env
```

**Attendu** :
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=AKIAYXFUBYSIHOV5ZL6C
AWS_SECRET_ACCESS_KEY=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
AWS_DEFAULT_REGION=eu-north-1
AWS_BUCKET=lelagali
AWS_URL=https://lelagali.s3.eu-north-1.amazonaws.com
```

### 1.2 Vider le cache

```bash
cd c:/laragon/www/lelagali-back
php artisan config:clear
php artisan cache:clear
```

---

## Étape 2 : Test via Tinker

### 2.1 Lancer Tinker

```bash
php artisan tinker
```

### 2.2 Test d'Upload

```php
// Créer un fichier de test
Storage::disk('s3')->put('test-upload.txt', 'Hello from S3!');

// Vérifier que le fichier existe
Storage::disk('s3')->exists('test-upload.txt');
// Devrait retourner: true

// Obtenir l'URL
Storage::disk('s3')->url('test-upload.txt');
// Devrait retourner: https://lelagali.s3.eu-north-1.amazonaws.com/test-upload.txt

// Lire le contenu
Storage::disk('s3')->get('test-upload.txt');
// Devrait retourner: "Hello from S3!"

// Supprimer
Storage::disk('s3')->delete('test-upload.txt');
```

### 2.3 Test du Helper StorageHelper

```php
use App\Helpers\StorageHelper;

// Simuler un fichier
$fakePath = 'menus/test-image.jpg';

// Obtenir l'URL
$url = StorageHelper::getUrl($fakePath);
echo $url;
// Devrait afficher: https://lelagali.s3.eu-north-1.amazonaws.com/menus/test-image.jpg
```

### 2.4 Quitter Tinker

```php
exit
```

---

## Étape 3 : Test via API (Postman/Insomnia)

### 3.1 Se Connecter

**Endpoint** : `POST http://localhost:8000/api/auth/login`

**Body (JSON)** :
```json
{
    "email": "admin@lelagali.ci",
    "password": "password"
}
```

**Copier le token** de la réponse.

---

### 3.2 Créer un Plat avec Image

**Endpoint** : `POST http://localhost:8000/api/app/plats`

**Headers** :
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: multipart/form-data
```

**Body (form-data)** :
| Key | Value | Type |
|-----|-------|------|
| nom | Test Plat S3 | Text |
| description | Test upload vers S3 | Text |
| prix | 2500 | Text |
| quantite_disponible | 10 | Text |
| image | [Sélectionner une image JPG/PNG] | File |

**Cliquer sur Send**

---

### 3.3 Vérifier la Réponse

**Réponse attendue** :
```json
{
    "success": true,
    "message": "Menu créé avec succès",
    "data": {
        "id": "uuid-xxx-xxx",
        "nom": "Test Plat S3",
        "description": "Test upload vers S3",
        "prix": 2500,
        "quantite_disponible": 10,
        "image": "menus/1709635200_abc123def456.jpg",
        "image_url": "https://lelagali.s3.eu-north-1.amazonaws.com/menus/1709635200_abc123def456.jpg",
        "restaurateur_id": "uuid-restaurateur",
        "created_at": "2026-02-10T...",
        ...
    }
}
```

**Points clés à vérifier** :
- ✅ `image` contient le chemin relatif (menus/...)
- ✅ `image_url` contient l'URL complète S3
- ✅ Le statut HTTP est 201 Created

---

### 3.4 Vérifier l'Image dans le Navigateur

**Copier** l'URL de `image_url` et **ouvrir** dans un navigateur :

```
https://lelagali.s3.eu-north-1.amazonaws.com/menus/1709635200_abc123def456.jpg
```

**L'image devrait s'afficher directement** sans erreur 403 ou 404.

---

## Étape 4 : Vérifier dans la Console AWS

### 4.1 Se Connecter à AWS

1. Aller sur https://console.aws.amazon.com/
2. Se connecter avec vos identifiants AWS
3. Région : **EU (Stockholm) eu-north-1**

### 4.2 Ouvrir le Bucket S3

1. Services > S3
2. Cliquer sur le bucket **lelagali**
3. Naviguer dans le dossier **menus/**

### 4.3 Vérifier le Fichier

Vous devriez voir :
- ✅ Le fichier uploadé (ex: `1709635200_abc123def456.jpg`)
- ✅ Taille du fichier (ex: 245 KB)
- ✅ Date d'upload (juste maintenant)

---

## Étape 5 : Test de Suppression

### 5.1 Supprimer le Plat de Test

**Endpoint** : `DELETE http://localhost:8000/api/app/plats/{PLAT_ID}`

**Headers** :
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Remplacer** `{PLAT_ID}` par l'ID du plat créé à l'étape 3.2

---

### 5.2 Vérifier la Suppression

**Dans la console AWS S3** :
- Le fichier devrait **encore être présent** (soft delete du plat uniquement)

**Note** : La suppression physique de l'image S3 n'est pas automatique lors du soft delete. C'est normal et évite la perte de données.

---

## Résultats Attendus

### ✅ Tests Réussis

| Test | Résultat |
|------|----------|
| Configuration .env | ✅ FILESYSTEM_DISK=s3 |
| Upload via Tinker | ✅ Fichier créé sur S3 |
| URL via Helper | ✅ URL complète générée |
| Upload via API | ✅ Image uploadée |
| Attribut image_url | ✅ Présent dans JSON |
| Image accessible | ✅ Visible dans navigateur |
| Fichier dans S3 | ✅ Visible dans console AWS |

---

## ❌ Résolution des Problèmes

### Problème 1 : Erreur "Class AwsS3Adapter not found"

**Solution** :
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

---

### Problème 2 : Erreur "Access Denied"

**Causes possibles** :
1. Credentials incorrectes dans `.env`
2. Permissions IAM insuffisantes
3. Bucket name incorrect

**Solution** :
```bash
# Vérifier les credentials
php artisan tinker
config('filesystems.disks.s3.key');
config('filesystems.disks.s3.secret');
config('filesystems.disks.s3.bucket');
```

---

### Problème 3 : Image non accessible (403 Forbidden)

**Cause** : Le bucket ou l'objet n'est pas public

**Solution dans Console AWS** :
1. S3 > Bucket lelagali > Permissions
2. Désactiver "Block all public access"
3. Bucket Policy : Ajouter
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicRead",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::lelagali/*"
        }
    ]
}
```

---

### Problème 4 : URL retournée est `null`

**Cause** : Le chemin de l'image est vide ou null

**Solution** :
```php
// Vérifier dans Tinker
$plat = App\Models\Plat::latest()->first();
$plat->image; // Doit contenir "menus/..."
$plat->image_url; // Doit contenir l'URL complète
```

---

## Commandes Utiles

### Lister les fichiers S3

```bash
php artisan tinker
Storage::disk('s3')->files('menus');
```

### Obtenir la taille d'un fichier

```bash
php artisan tinker
Storage::disk('s3')->size('menus/fichier.jpg');
```

### Vérifier si un fichier existe

```bash
php artisan tinker
Storage::disk('s3')->exists('menus/fichier.jpg');
```

---

## Prochaines Étapes

Une fois les tests réussis :

1. ✅ S3 est configuré et fonctionnel
2. ⏳ Intégrer App.vue pour charger les menus depuis l'API
3. ⏳ Vérifier que les images S3 s'affichent dans le frontend
4. ⏳ Tester l'upload d'images depuis le frontend (restaurateurs)

---

**Bon test ! 🧪**

Date : 2026-02-10
Auteur : Claude Sonnet 4.5
