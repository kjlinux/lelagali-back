<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Obtenir l'URL publique d'un fichier
     * Fonctionne automatiquement avec S3 ou local storage
     *
     * @param string|null $path
     * @return string|null
     */
    public static function getUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = config('filesystems.default');

        if ($disk === 's3') {
            // Pour S3, retourner l'URL complète
            return Storage::disk('s3')->url($path);
        }

        // Pour local, utiliser l'URL publique
        return Storage::disk('public')->url($path);
    }

    /**
     * Stocker un fichier et retourner le chemin
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return string Le chemin du fichier stocké
     */
    public static function store($file, string $directory = 'uploads', ?string $filename = null): string
    {
        $disk = config('filesystems.default');

        if ($filename) {
            $path = $file->storeAs($directory, $filename, $disk);
        } else {
            $path = $file->store($directory, $disk);
        }

        return $path;
    }

    /**
     * Stocker un fichier d'image et retourner le chemin
     * Génère un nom de fichier unique basé sur le timestamp
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $directory
     * @return string Le chemin de l'image stockée
     */
    public static function storeImage($image, string $directory = 'images'): string
    {
        $extension = $image->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        return self::store($image, $directory, $filename);
    }

    /**
     * Supprimer un fichier
     *
     * @param string|null $path
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $disk = config('filesystems.default');

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Vérifier si un fichier existe
     *
     * @param string|null $path
     * @return bool
     */
    public static function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $disk = config('filesystems.default');

        return Storage::disk($disk)->exists($path);
    }

    /**
     * Obtenir la taille d'un fichier en octets
     *
     * @param string|null $path
     * @return int|false
     */
    public static function size(?string $path)
    {
        if (!$path) {
            return false;
        }

        $disk = config('filesystems.default');

        return Storage::disk($disk)->size($path);
    }

    /**
     * Obtenir le type MIME d'un fichier
     *
     * @param string|null $path
     * @return string|false
     */
    public static function mimeType(?string $path)
    {
        if (!$path) {
            return false;
        }

        $disk = config('filesystems.default');

        return Storage::disk($disk)->mimeType($path);
    }
}
