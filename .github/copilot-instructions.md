# Directives pour les agents IA - Lelagali Backend

Ce guide contient les informations essentielles pour comprendre et travailler efficacement avec le backend de Lelagali.

## Architecture générale

Lelagali est une application de livraison de repas construite avec Laravel. Points clés :

-   Authentication via JWT (voir `app/Models/User.php` qui implémente `JWTSubject`)
-   Gestion des utilisateurs avec rôles (`role` dans la table users)
-   Gestion de la localisation par quartiers (`quartier_id` dans users)
-   SoftDeletes activé sur les modèles principaux

## Modèles principaux

-   `User` : Utilisateurs du système (clients, restaurateurs, livreurs)
-   `Plat` : Les plats proposés par les restaurants
-   `Commande` et `CommandeItem` : Gestion des commandes et leurs détails
-   `MoyenPaiement` et `RestaurateurMoyenPaiement` : Moyens de paiement acceptés par restaurant
-   `TarifLivraison` : Tarification de la livraison
-   `Quartier` : Zones de livraison

## Conventions de développement

1. **Modèles** :

    - Utilisation systématique de `SoftDeletes` pour la suppression douce
    - Définition explicite des `$fillable` pour la sécurité
    - Type-hints PHPDoc pour les propriétés et relations

2. **API** :

    - Routes API sous `/api`
    - Authentification via JWT
    - Réponses standardisées (format à documenter)

3. **Base de données** :
    - Migrations datées avec préfixe YYMMDD
    - Relations géographiques basées sur les quartiers
    - Timestamps automatiques sur les tables principales

## Workflow de développement

1. Configuration initiale :

    ```bash
    composer install
    php artisan key:generate
    php artisan jwt:secret
    php artisan migrate
    ```

2. Tests :

    - Framework de test : Pest PHP
    - Fichiers de test dans `tests/Feature` et `tests/Unit`

3. Commandes utiles :
    ```bash
    php artisan test  # Exécuter les tests
    php artisan route:list  # Lister toutes les routes
    php artisan make:model NomModel -mf  # Créer modèle+migration+factory
    ```

## Points d'attention

-   Toujours vérifier les permissions utilisateur via les rôles
-   Utiliser les Request classes pour la validation des entrées
-   Respecter la structure de réponse API établie
-   Gérer la localisation (quartiers) pour les restrictions géographiques

## Ressources clés

-   `config/jwt.php` : Configuration JWT
-   `routes/api.php` : Points d'entrée API
-   `app/Http/Controllers` : Logique métier
-   `database/migrations` : Structure de la base de données
