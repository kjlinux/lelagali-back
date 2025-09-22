<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CommandeItemController;
use App\Http\Controllers\TarifLivraisonController;
use App\Http\Controllers\NotificationCommandeController;
use App\Http\Controllers\RestaurateurMoyenPaiementController;

// Routes des commandes
Route::get('commandes', [CommandeController::class, 'index']);
Route::post('commandes', [CommandeController::class, 'store']);
Route::get('commandes/{commande}', [CommandeController::class, 'show']);

// Routes des items de commande
Route::get('commande-items', [CommandeItemController::class, 'index']);
Route::post('commande-items', [CommandeItemController::class, 'store']);
Route::put('commande-items/{commandeItem}', [CommandeItemController::class, 'update']);

// Routes des notifications de commande
Route::get('/notifications', [NotificationCommandeController::class, 'index']);
Route::get('/notifications/recent', [NotificationCommandeController::class, 'recent']);
Route::get('/notifications/unread-count', [NotificationCommandeController::class, 'unreadCount']);
Route::post('/notifications', [NotificationCommandeController::class, 'store']);
Route::patch('/notifications/{id}/read', [NotificationCommandeController::class, 'markAsRead']);
Route::patch('/notifications/mark-all-read', [NotificationCommandeController::class, 'markAllAsRead']);
Route::delete('/notifications/{id}', [NotificationCommandeController::class, 'destroy']);


Route::get('plats', [PlatController::class, 'index']);
Route::post('plats', [PlatController::class, 'store']);
Route::get('plats/{plat}', [PlatController::class, 'show']);
Route::put('plats/{plat}', [PlatController::class, 'update']);
Route::delete('plats/{plat}', [PlatController::class, 'destroy']);
Route::get('plats-trashed', [PlatController::class, 'trashed']);
Route::post('plats/{plat}/restore', [PlatController::class, 'restore']);
Route::put('plats/{plat}/approve', [App\Http\Controllers\PlatController::class, 'approve'])
    ->name('plats.approve');
Route::put('plats/{plat}/reject', [App\Http\Controllers\PlatController::class, 'reject'])
    ->name('plats.reject');
Route::get('plats-moderation', [App\Http\Controllers\PlatController::class, 'moderation'])
    ->name('plats.moderation');

// Routes des moyens de paiement des restaurateurs
Route::get('restaurateur-moyen-paiements', [RestaurateurMoyenPaiementController::class, 'index']);
Route::post('restaurateur-moyen-paiements', [RestaurateurMoyenPaiementController::class, 'store']);
Route::get('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'show']);
Route::put('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'update']);
Route::delete('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'destroy']);
Route::get('restaurateur-moyen-paiements-trashed', [RestaurateurMoyenPaiementController::class, 'trashed']);
Route::post('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}/restore', [RestaurateurMoyenPaiementController::class, 'restore']);

// Routes des tarifs de livraison
Route::get('tarif-livraisons', [TarifLivraisonController::class, 'index']);
Route::post('tarif-livraisons', [TarifLivraisonController::class, 'store']);
Route::get('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'show']);
Route::put('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'update']);
Route::delete('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'destroy']);
Route::get('tarif-livraisons-trashed', [TarifLivraisonController::class, 'trashed']);
Route::post('tarif-livraisons/{tarifLivraison}/restore', [TarifLivraisonController::class, 'restore']);
