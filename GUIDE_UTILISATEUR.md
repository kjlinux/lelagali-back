# Guide Utilisateur - LeLagaLi

Plateforme de commande de repas en ligne. Ce guide couvre les trois interfaces : Client, Restaurateur et Administrateur.

---

## Table des matieres

1. [Application Client](#1-application-client)
2. [Espace Restaurateur](#2-espace-restaurateur)
3. [Panneau Administrateur](#3-panneau-administrateur)

---

## 1. Application Client

### 1.1 Creer un compte

- Depuis la page d'accueil, cliquer sur "Connexion" dans l'en-tete.
- Basculer vers le mode "Inscription".
- Renseigner : nom complet, numero de telephone (format burkinabe), adresse email, mot de passe (6 caracteres minimum).
- Valider. Le compte est cree et la connexion est automatique.

### 1.2 Se connecter

- Cliquer sur "Connexion".
- Saisir l'email ou le numero de telephone et le mot de passe.
- Valider.

### 1.3 Parcourir les menus

La page d'accueil affiche les plats disponibles du jour.

**Recherche** : utiliser la barre de recherche pour filtrer par nom de plat, restaurant ou quartier.

**Filtres disponibles** :
- Par quartier (zone de livraison)
- Par fourchette de prix (curseur min/max)

Les resultats s'affichent par pages de 9 plats.

### 1.4 Consulter un plat

Cliquer sur un plat pour ouvrir sa fiche detaillee :
- Description complete
- Prix et quantite disponible
- Temps de preparation estime
- Liste des ingredients
- Notes et avis
- Modes de service (livraison et/ou retrait)
- Nom et localisation du restaurant

### 1.5 Gerer le panier

**Ajouter un plat** : depuis la fiche ou la carte du plat, choisir la quantite et ajouter au panier.

Un resume flottant apparait en bas a droite avec le nombre d'articles et le total.

**Modifier le panier** : ouvrir le detail du panier pour :
- Augmenter ou diminuer la quantite d'un article
- Supprimer un article
- Vider le panier

Le panier est sauvegarde localement. Il persiste entre les sessions et se vide apres une commande validee ou une deconnexion.

La quantite ne peut pas depasser le stock disponible du plat.

### 1.6 Passer une commande

Le panier peut contenir des plats de plusieurs restaurants. Le processus de paiement se fait par restaurant.

**Etape 1 - Mode de service** (par restaurant) :
- Livraison : le plat est livre a l'adresse indiquee.
- Retrait : le plat est recupere directement au restaurant.

Seuls les modes proposes par le restaurant sont disponibles.

**Etape 2 - Adresse de livraison** (si livraison) :
- Adresse de rue
- Quartier (parmi les zones desservies par le restaurant)
- Numero de telephone
- Instructions speciales (optionnel)

Les frais de livraison sont calcules automatiquement selon le quartier choisi.

**Etape 3 - Moyen de paiement** (par restaurant) :

Paiements electroniques :
- Orange Money
- Moov Money
- Wave
- Mobile Money

Pour un paiement electronique, fournir la reference de transaction et le numero de telephone utilise.

Paiements en especes :
- A la livraison
- Au retrait

**Etape 4 - Confirmation** :
Un recapitulatif affiche les articles, sous-totaux, frais de livraison et total general par restaurant. Valider pour envoyer la commande.

Un ecran de confirmation s'affiche avec le numero de commande et le detail complet.

### 1.7 Suivre ses commandes

Acceder a "Mes commandes" depuis le menu utilisateur.

**Onglet Commandes en cours** : commandes non terminees.
**Onglet Commandes terminees** : historique.

Statuts possibles :
| Statut | Signification |
|---|---|
| En preparation | Le restaurant prepare la commande |
| Pret a recuperer | La commande attend au restaurant (retrait) |
| Pret pour livraison | La commande attend le livreur |
| En cours de livraison | Le livreur est en route |
| Livre | La commande a ete livree |
| Recupere | La commande a ete retiree au restaurant |

Chaque commande affiche : le detail des articles, l'adresse, le moyen de paiement, le montant et les frais de livraison.

### 1.8 Gerer son profil

Depuis le menu utilisateur, acceder a "Mon profil" pour :
- Modifier le nom, email, telephone, adresse
- Changer la photo de profil
- Changer le mot de passe (mot de passe actuel requis, nouveau mot de passe de 6 caracteres minimum)
- Consulter ses statistiques (nombre de commandes, restaurants favoris)
- Se deconnecter

---

## 2. Espace Restaurateur

Accessible uniquement aux comptes ayant le role "restaurateur".

### 2.1 Vue d'ensemble

Le tableau de bord affiche :
- Nombre de menus actifs
- Commandes du jour
- Chiffre d'affaires de la semaine
- Note moyenne
- Graphique des commandes de la semaine (lundi a dimanche)
- Graphique des plats les plus vendus
- Tableau des 5 dernieres commandes

### 2.2 Gerer les menus

**Consulter** : la liste des plats s'affiche sous forme de tableau avec nom, description, prix, stock, statut et actions.

Le stock est represente par une jauge coloree :
- Vert : plus de 10 unites
- Jaune : entre 5 et 10
- Rouge : 5 ou moins

**Ajouter un plat** :
- Image (15 Mo maximum)
- Nom et description
- Prix
- Quantite disponible
- Temps de preparation (30 minutes par defaut)
- Categorie : Entrees, Plats principaux, Desserts, Boissons, Menus speciaux
- Ingredients (sous forme de tags)
- Disponible en livraison (oui/non)
- Disponible en retrait (oui/non)

Au moins un mode de service doit etre coche.

Apres creation, le plat est en attente de validation par l'administrateur. Il ne sera visible par les clients qu'apres approbation.

**Modifier** : cliquer sur le bouton d'edition pour modifier les informations du plat.

**Supprimer** : cliquer sur le bouton de suppression. Une confirmation est demandee.

### 2.3 Gerer les commandes

Les commandes sont affichees sous forme de cartes, filtrables par statut :
- Toutes
- En attente
- Confirmees
- Pretes
- En livraison
- Recuperees
- Annulees

Chaque carte affiche : numero de commande, date, statut, informations client, articles commandes avec images, detail du paiement, frais de livraison et total.

**Actions disponibles selon le statut** :

| Statut actuel | Action | Statut suivant |
|---|---|---|
| En attente | Accepter | Confirmee |
| Confirmee | Marquer prete | Prete |
| Prete (livraison) | Mettre en livraison | En livraison |
| Prete (retrait) | Marquer recuperee | Recuperee |
| En livraison | Marquer recuperee | Recuperee |
| Tout statut non termine | Annuler | Annulee |

**Gestion des paiements** :
- Pour les paiements electroniques avec reference, le restaurateur peut verifier la transaction et rejeter le paiement si la reference est invalide, en indiquant un motif.
- Le client recoit un email de notification en cas de rejet.
- Un paiement rejete peut etre revalide ulterieurement.
- Les boutons de changement de statut sont desactives tant que le paiement est rejete.
- Les paiements en especes sont confirmes manuellement.

### 2.4 Historique des commandes

Consulter les commandes passees avec filtrage par :
- Periode : 7 jours, 30 jours, 3 mois, 6 mois, annee en cours, tout
- Plage de dates personnalisee
- Statut : livree/recuperee, annulee

Statistiques affichees :
- Nombre total de commandes
- Chiffre d'affaires total (commandes completees et payees)
- Valeur moyenne des commandes

### 2.5 Parametres de livraison

**Onglet Zones de livraison** :
- Liste de tous les quartiers disponibles sur la plateforme.
- Activer ou desactiver la livraison vers chaque quartier.
- Definir le tarif de livraison par quartier (en FCFA). Par defaut : 500 FCFA.
- Sauvegarder les modifications.

**Onglet Moyens de paiement** :
- Liste des moyens de paiement acceptes par le restaurant.
- Ajouter un moyen de paiement : selectionner le type (Mobile Money, Orange Money, Wave, Carte bancaire, Especes), saisir le numero de compte et le nom du titulaire.
- Modifier ou supprimer un moyen de paiement existant.

### 2.6 Profil du restaurateur

- Modifier le nom, email, telephone, adresse, quartier.
- Changer la photo de profil (2 Mo maximum).
- Changer le mot de passe.

### 2.7 Notifications

Une cloche dans l'en-tete affiche le nombre de notifications non lues. Cliquer pour voir les notifications recentes (commandes, paiements, systeme). Les notifications sont actualisees automatiquement toutes les 60 secondes.

---

## 3. Panneau Administrateur

Accessible uniquement aux comptes ayant le role "admin".

### 3.1 Tableau de bord

Indicateurs affiches :
- Nombre total de restaurateurs
- Nombre total de clients
- Commandes du jour
- Chiffre d'affaires total (commandes payees)

Graphiques :
- Commandes par jour (7 derniers jours)
- Chiffre d'affaires par jour (7 derniers jours)
- Repartition des commandes par statut (camembert)
- Top 5 des restaurants par nombre de commandes

### 3.2 Gestion des utilisateurs

Trois sections distinctes : Restaurateurs, Clients, Administrateurs.

Pour chaque type d'utilisateur, l'administrateur peut :
- Consulter la liste avec recherche et filtrage (nom, email, telephone, quartier)
- Ajouter un utilisateur : nom, email, telephone, quartier, mot de passe (genere automatiquement si non fourni). Le role est affecte automatiquement selon la section.
- Modifier les informations d'un utilisateur.
- Supprimer un utilisateur (confirmation requise).
- Activer ou desactiver un compte. Un compte desactive ne peut plus se connecter.

### 3.3 Gestion des commandes

**Statistiques en haut de page** :
- Total des commandes
- Commandes en attente
- Commandes en cours
- Commandes terminees
- Commandes impayees

**Filtrage** : par statut, par plage de dates, par recherche (numero, nom client, nom restaurant).

**Detail d'une commande** :
- Informations generales : numero, client, restaurant, type de service, statut, statut du paiement.
- Livraison et paiement : adresse, quartier, moyen de paiement, temps de preparation estime.
- Articles commandes : nom du plat, quantite, prix unitaire, sous-total.
- Recapitulatif financier : sous-total des plats, frais de livraison, total general.

L'administrateur peut modifier le statut d'une commande selon le meme flux que le restaurateur.

### 3.4 Moderation des plats

Les plats soumis par les restaurateurs doivent etre approuves avant d'apparaitre dans l'application client.

**Filtrage** : par statut (en attente, approuve), par recherche (nom du plat, description, nom du restaurateur).

**Actions** :
- Approuver : le plat devient visible pour les clients. Le restaurateur recoit un email de confirmation.
- Rejeter : le plat reste invisible. Le restaurateur recoit un email l'informant du rejet.

Un plat rejete peut etre approuve ulterieurement.

### 3.5 Gestion des quartiers

Les quartiers definissent les zones geographiques de livraison.

- Consulter la liste des quartiers.
- Ajouter un nouveau quartier.
- Modifier le nom d'un quartier.
- Supprimer un quartier (impossible si des utilisateurs ou commandes y sont associes).

### 3.6 Gestion des moyens de paiement

Les moyens de paiement sont definis au niveau de la plateforme. Les restaurateurs choisissent ensuite lesquels ils acceptent.

- Consulter la liste des moyens de paiement.
- Ajouter un moyen de paiement : nom et icone.
- Modifier ou supprimer un moyen de paiement existant.

### 3.7 Notifications

Filtrage par type (commande, utilisateur, paiement, systeme, info, avertissement, succes, erreur) et par statut (lu, non lu).

Actions :
- Marquer une ou plusieurs notifications comme lues.
- Supprimer une ou plusieurs notifications.
- Tout marquer comme lu.

### 3.8 Parametres de la plateforme

- Frais de livraison de base (FCFA).
- Commission de la plateforme (pourcentage preleve sur les restaurants).
- Approbation automatique des menus : si active, les nouveaux plats sont publies sans moderation.
- Nombre maximum de menus par restaurant par jour.

---

## Annexes

### Statuts d'une commande (cycle complet)

```
En attente
    |
    v
Confirmee (restaurant a accepte)
    |
    v
Prete (plat prepare)
    |
    +--> [Livraison] --> En livraison --> Recuperee (livree)
    |
    +--> [Retrait] --> Recuperee (retiree au restaurant)

A tout moment (sauf si terminee) : Annulee
```

### Moyens de paiement disponibles

| Moyen | Type | Reference requise |
|---|---|---|
| Orange Money | Electronique | Oui |
| Moov Money | Electronique | Oui |
| Wave | Electronique | Oui |
| Mobile Money | Electronique | Oui |
| Especes a la livraison | Especes | Non |
| Especes au retrait | Especes | Non |

### Emails automatiques

L'application envoie des emails dans les cas suivants :
- Creation de compte (identifiants)
- Confirmation de commande (client)
- Nouvelle commande recue (restaurateur)
- Changement de statut de commande (client)
- Annulation de commande par le client (restaurateur)
- Rejet de paiement (client)
- Approbation ou rejet d'un plat (restaurateur)
- Modification des identifiants du compte
- Suspension de compte
