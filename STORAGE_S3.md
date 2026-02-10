# Configuration du Stockage S3 - LeLagaLi Backend

## Vue d'Ensemble

Le backend LeLagaLi utilise **Amazon S3** pour stocker tous les fichiers uploadés (principalement les images des plats). Cette configuration permet:

- ✅ Stockage illimité et scalable
- ✅ URLs publiques permanentes
- ✅ CDN intégré pour performance mondiale
- ✅ Haute disponibilité (99.99%)
- ✅ Pas de gestion de serveur de fichiers

---

## Configuration

### 1. Fichier `.env`

Les credentials AWS S3 sont configurés dans le fichier `.env` :

```env
# Filesystem par défaut (utiliser S3)
FILESYSTEM_DISK=s3

# AWS S3 Configuration
AWS_ACCESS_KEY_ID=AKIAYXFUBYSIHOV5ZL6C
AWS_SECRET_ACCESS_KEY=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
AWS_DEFAULT_REGION=eu-north-1
AWS_BUCKET=lelagali
AWS_URL=https://lelagali.s3.eu-north-1.amazonaws.com
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### 2. Fichier `config/filesystems.php`

Le disque S3 est configuré avec visibilité publique par défaut :

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'visibility' => 'public', // Fichiers publics par défaut
    'throw' => false,
    'report' => false,
],
```

---

## Helper StorageHelper

Un helper personnalisé `App\Helpers\StorageHelper` a été créé pour abstraire les opérations de stockage. Il fonctionne automatiquement avec S3 ou local storage selon la configuration.

### Méthodes Disponibles

#### `getUrl(?string $path): ?string`
Obtient l'URL publique complète d'un fichier.

```php
use App\Helpers\StorageHelper;

$imageUrl = StorageHelper::getUrl('menus/1234567890_abc123.jpg');
// Retourne: https://lelagali.s3.eu-north-1.amazonaws.com/menus/1234567890_abc123.jpg
```

#### `store($file, string $directory, ?string $filename): string`
Stocke un fichier et retourne son chemin.

```php
$path = StorageHelper::store($request->file('document'), 'documents', 'mon-fichier.pdf');
// Retourne: documents/mon-fichier.pdf
```

#### `storeImage($image, string $directory): string`
Stocke une image avec un nom unique généré automatiquement.

```php
$path = StorageHelper::storeImage($request->file('image'), 'menus');
// Retourne: menus/1709635200_abc123def456.jpg
```

#### `delete(?string $path): bool`
Supprime un fichier de S3.

```php
StorageHelper::delete('menus/old-image.jpg');
```

#### `exists(?string $path): bool`
Vérifie si un fichier existe.

```php
if (StorageHelper::exists('menus/image.jpg')) {
    // Le fichier existe
}
```

#### `size(?string $path): int|false`
Obtient la taille d'un fichier en octets.

```php
$size = StorageHelper::size('menus/image.jpg');
// Retourne: 245678 (octets)
```

#### `mimeType(?string $path): string|false`
Obtient le type MIME d'un fichier.

```php
$mimeType = StorageHelper::mimeType('menus/image.jpg');
// Retourne: "image/jpeg"
```

---

## Modèle Plat - Attribut `image_url`

Le modèle `Plat` a été enrichi avec un attribut virtuel `image_url` qui retourne automatiquement l'URL complète S3.

### Code

```php
use App\Helpers\StorageHelper;

class Plat extends Model
{
    // ...

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return StorageHelper::getUrl($this->image);
    }
}
```

### Utilisation

```php
$plat = Plat::find($id);

// Chemin relatif dans la base de données
echo $plat->image;
// menus/1709635200_abc123.jpg

// URL complète S3 (attribut virtuel)
echo $plat->image_url;
// https://lelagali.s3.eu-north-1.amazonaws.com/menus/1709635200_abc123.jpg
```

### Réponse API JSON

Lors de la sérialisation JSON, l'attribut `image_url` est automatiquement inclus :

```json
{
    "id": "uuid-123",
    "nom": "Attiéké Poisson",
    "description": "...",
    "prix": 2500,
    "image": "menus/1709635200_abc123.jpg",
    "image_url": "https://lelagali.s3.eu-north-1.amazonaws.com/menus/1709635200_abc123.jpg",
    ...
}
```

**Le frontend peut directement utiliser `plat.image_url` pour afficher les images !**

---

## PlatController - Upload vers S3

Le `PlatController` a été mis à jour pour utiliser `StorageHelper` au lieu de `Storage::disk()`.

### Création d'un Plat

```php
public function store(Request $request)
{
    // ...

    if ($request->hasFile('image')) {
        // Upload vers S3 avec nom unique
        $imagePath = StorageHelper::storeImage($request->file('image'), 'menus');
        $data['image'] = $imagePath;
    }

    $plat = Plat::create($data);

    return response()->json([
        'success' => true,
        'data' => $plat // Contient image_url automatiquement
    ]);
}
```

### Mise à Jour d'un Plat

```php
public function update(Request $request, Plat $plat)
{
    // ...

    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image de S3
        if ($plat->image) {
            StorageHelper::delete($plat->image);
        }

        // Upload la nouvelle image vers S3
        $imagePath = StorageHelper::storeImage($request->file('image'), 'menus');
        $data['image'] = $imagePath;
    }

    $plat->update($data);

    return response()->json([
        'success' => true,
        'data' => $plat // Contient la nouvelle image_url
    ]);
}
```

---

## Frontend - Utilisation des Images S3

Le frontend n'a **aucune modification à faire** ! Il reçoit déjà l'attribut `image_url` avec l'URL complète S3.

### Exemple dans App.vue (données mockées actuelles)

```javascript
// AVANT (données mockées)
{
    id: 1,
    title: 'Attiéké Poisson',
    image: 'https://images.unsplash.com/photo-...',
    // ...
}

// APRÈS (données réelles de l'API)
{
    id: "uuid-123",
    nom: 'Attiéké Poisson',
    image: "menus/1709635200_abc123.jpg",
    image_url: "https://lelagali.s3.eu-north-1.amazonaws.com/menus/1709635200_abc123.jpg",
    // ...
}
```

### Affichage dans le template

```vue
<template>
    <!-- Utiliser image_url au lieu de image -->
    <img :src="plat.image_url" :alt="plat.nom" />
</template>
```

---

## Bucket S3 - Informations

| Paramètre | Valeur |
|-----------|--------|
| Nom du bucket | `lelagali` |
| Région | `eu-north-1` (Stockholm, Suède) |
| URL de base | `https://lelagali.s3.eu-north-1.amazonaws.com` |
| Visibilité | Publique (lecture seule) |
| Dossiers | `menus/` (images des plats) |

---

## Permissions IAM Requises

L'utilisateur AWS (credentials dans `.env`) doit avoir les permissions suivantes sur le bucket `lelagali` :

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::lelagali",
                "arn:aws:s3:::lelagali/*"
            ]
        }
    ]
}
```

---

## Sécurité

### ⚠️ Credentials AWS dans .env

**IMPORTANT** : Les credentials AWS dans le fichier `.env` sont sensibles et **NE DOIVENT JAMAIS** être committés dans Git.

- ✅ Le fichier `.env` est dans `.gitignore`
- ✅ Le fichier `.env.example` ne contient PAS les vraies credentials
- ⚠️ Ne partagez jamais vos Access Key ID et Secret Access Key

### Rotation des Credentials

Il est recommandé de créer un nouvel utilisateur IAM et de nouvelles credentials régulièrement (tous les 90 jours).

---

## Commandes Utiles

### Tester la connexion S3

```bash
php artisan tinker

# Tester l'upload
Storage::disk('s3')->put('test.txt', 'Hello S3');

# Tester la lecture
Storage::disk('s3')->get('test.txt');

# Tester l'URL
Storage::disk('s3')->url('test.txt');

# Supprimer le fichier de test
Storage::disk('s3')->delete('test.txt');
```

### Lister les fichiers du bucket

```bash
php artisan tinker

Storage::disk('s3')->files('menus');
```

### Vider le cache de configuration

Après modification du `.env`, vider le cache :

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Migration Local → S3

Si vous aviez déjà des images en local storage, voici comment les migrer vers S3 :

```php
// Dans tinker ou un script de migration
$plats = App\Models\Plat::whereNotNull('image')->get();

foreach ($plats as $plat) {
    $localPath = storage_path('app/public/' . $plat->image);

    if (file_exists($localPath)) {
        $fileContent = file_get_contents($localPath);
        Storage::disk('s3')->put($plat->image, $fileContent, 'public');
        echo "✅ Migré: {$plat->image}\n";
    }
}
```

---

## Troubleshooting

### Erreur: "Class 'League\Flysystem\AwsS3v3\AwsS3Adapter' not found"

Installer le package AWS SDK :

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Erreur: "Error executing PutObject on..."

Vérifier les credentials AWS dans `.env` et les permissions IAM.

### Images non affichées

1. Vérifier que le bucket S3 autorise la lecture publique
2. Vérifier que `image_url` est bien retourné dans la réponse API
3. Vérifier les CORS du bucket S3 (autoriser GET depuis votre domaine frontend)

### Configuration CORS du Bucket S3

Dans la console AWS S3, ajouter cette configuration CORS au bucket `lelagali` :

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "HEAD"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": []
    }
]
```

---

## Coûts AWS S3

### Tarification (région eu-north-1)

- **Stockage** : ~$0.023 par GB/mois
- **Requêtes GET** : $0.0004 par 1000 requêtes
- **Requêtes PUT** : $0.005 par 1000 requêtes
- **Transfert sortant** : $0.09 par GB (premiers 10 TB/mois)

### Estimation pour LeLagaLi

- **Images** : 1000 plats × 200 KB/image = 200 MB
- **Coût stockage** : ~$0.005/mois
- **Coût requêtes** : ~$0.01/mois (10 000 vues/mois)

**Total estimé : < $0.10/mois**

---

## Checklist de Configuration

- [x] Credentials AWS configurées dans `.env`
- [x] `FILESYSTEM_DISK=s3` dans `.env`
- [x] Bucket S3 créé et configuré en public
- [x] Helper `StorageHelper` créé
- [x] Modèle `Plat` mis à jour avec `image_url`
- [x] `PlatController` mis à jour pour utiliser `StorageHelper`
- [ ] Tester l'upload d'une image de plat
- [ ] Vérifier que `image_url` est retourné dans l'API
- [ ] Vérifier que les images s'affichent dans le frontend

---

**Date de création** : 2026-02-10
**Version** : 1.0
**Auteur** : Claude Sonnet 4.5
