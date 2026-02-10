<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Plat;
use Illuminate\Http\Request;
use App\Http\Requests\PlatRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Commande;
use App\Helpers\StorageHelper;

class PlatController extends Controller
{
    public function index()
    {
        try {
            $plats = Plat::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $plats->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $plats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Plat $plat)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $plat
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $plats = Plat::onlyTrashed()->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $plats->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $plats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore(Plat $plat)
    {
        try {
            $plat->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $plat
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des plats pour la modération
     */
    public function moderation(Request $request)
    {
        try {
            $query = Plat::with(['restaurateur:id,name'])
                ->select([
                    'id',
                    'nom',
                    'description',
                    'prix',
                    'quantite_disponible',
                    'date_disponibilite',
                    'is_approved',
                    'approved_by',
                    'approved_at',
                    'restaurateur_id',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc');

            // Filtrer par statut d'approbation si demandé
            if ($request->has('status')) {
                $status = $request->get('status');
                if ($status === 'pending') {
                    $query->where('is_approved', false);
                } elseif ($status === 'approved') {
                    $query->where('is_approved', true);
                }
            }

            // Recherche par nom, description ou restaurateur
            if ($request->has('search') && !empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('restaurateur', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $plats = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'status' => 'success',
                'message' => 'Plats récupérés avec succès',
                'data' => $plats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des plats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approuver un plat
     */
    public function approve(Request $request, Plat $plat)
    {
        try {
            $user = $request->user();

            // Vérifier si l'utilisateur a le rôle admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas les permissions nécessaires'
                ], 403);
            }

            // Si le plat est déjà approuvé
            if ($plat->is_approved) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Ce plat est déjà approuvé'
                ], 200);
            }

            $plat->update([
                'is_approved' => true,
                'approved_by' => $user->id,
                'approved_at' => now()
            ]);

            $plat->load('restaurateur:id,name');

            return response()->json([
                'status' => 'success',
                'message' => 'Plat approuvé avec succès',
                'data' => $plat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'approbation du plat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter un plat (le désapprouver)
     */
    public function reject(Request $request, Plat $plat)
    {
        try {
            $user = $request->user();

            // Vérifier si l'utilisateur a le rôle admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas les permissions nécessaires'
                ], 403);
            }

            // Si le plat n'est pas approuvé
            if (!$plat->is_approved) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Ce plat n\'est pas approuvé'
                ], 200);
            }

            $plat->update([
                'is_approved' => false,
                'approved_by' => $user->id,
                'approved_at' => now()
            ]);

            $plat->load('restaurateur:id,name');

            return response()->json([
                'status' => 'success',
                'message' => 'Plat rejeté avec succès',
                'data' => $plat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du rejet du plat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

/**
 * Récupérer les menus du jour pour le restaurateur connecté
 */
public function todayMenus(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'restaurateur') {
        return response()->json(['error' => 'Accès non autorisé'], 403);
    }

    $today = Carbon::today();

    $menus = Plat::where('restaurateur_id', $user->id)
        ->where('date_disponibilite', $today)
        ->withCount(['commandeItems as commandes_count' => function ($query) {
            $query->whereDate('created_at', Carbon::today());
        }])
        ->get()
        ->map(function ($plat) {
            return [
                'id' => $plat->id,
                'nom' => $plat->nom,
                'description' => $plat->description,
                'prix' => $plat->prix,
                'quantite' => $plat->quantite_disponible,
                'quantite_vendue' => $plat->quantite_vendue ?? 0,
                'image' => $plat->image ? Storage::url($plat->image) : '/default-menu.jpg',
                'statut' => $this->getMenuStatus($plat),
                'categorie' => 'Menu du jour',
                'temps_preparation' => $plat->temps_preparation,
                'commandes' => $plat->commandes_count, // Utilise le count optimisé
                'note' => rand(40, 50) / 10,
                'created_at' => $plat->created_at,
                'updated_at' => $plat->updated_at,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $menus
    ]);
}

    /**
     * Récupérer les statistiques pour le dashboard
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'restaurateur') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Statistiques des menus
        $activeMenusToday = Plat::where('restaurateur_id', $user->id)
            ->where('date_disponibilite', $today)
            ->where('quantite_disponible', '>', 0)
            ->count();

        // Commandes du jour
        $todayOrdersCount = Commande::where('restaurateur_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        // Revenus de la semaine
        $weeklyRevenue = Commande::where('restaurateur_id', $user->id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('status_paiement', true)
            ->sum('total_general');

        // Données pour le graphique (derniers 7 jours)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $orders = Commande::where('restaurateur_id', $user->id)
                ->whereDate('created_at', $date)
                ->count();

            $chartData[] = [
                'date' => $date->format('d/m'),
                'commandes' => $orders
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kpi' => [
                    'activeMenus' => $activeMenusToday,
                    'todayOrders' => $todayOrdersCount,
                    'weeklyRevenue' => $weeklyRevenue,
                    'averageRating' => 4.2 // À implémenter avec un vrai système de notation
                ],
                'chartData' => $chartData
            ]
        ]);
    }

    /**
     * Créer un nouveau menu
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'quantite_disponible' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temps_preparation' => 'nullable|integer|min:0',
            'date_disponibilite' => 'nullable|date'
        ]);

        $user = $request->user();

        if ($user->role !== 'restaurateur') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $data = $request->all();
        $data['restaurateur_id'] = $user->id;
        $data['date_disponibilite'] = $request->date_disponibilite ?? Carbon::tomorrow();

        // Gestion de l'upload d'image vers S3
        if ($request->hasFile('image')) {
            $imagePath = StorageHelper::storeImage($request->file('image'), 'menus');
            $data['image'] = $imagePath;
        }

        $plat = Plat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Menu créé avec succès',
            'data' => $plat
        ], 201);
    }

    /**
     * Mettre à jour un menu
     */
    public function update(Request $request, Plat $plat)
    {
        $user = $request->user();

        // Vérifier que le plat appartient au restaurateur
        if ($plat->restaurateur_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'prix' => 'sometimes|required|integer|min:0',
            'quantite_disponible' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temps_preparation' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['nom', 'description', 'prix', 'quantite_disponible', 'temps_preparation']);

        // Gestion de l'upload d'image vers S3
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image de S3
            if ($plat->image) {
                StorageHelper::delete($plat->image);
            }

            $imagePath = StorageHelper::storeImage($request->file('image'), 'menus');
            $data['image'] = $imagePath;
        }

        $plat->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Menu mis à jour avec succès',
            'data' => $plat
        ]);
    }

    /**
     * Supprimer un menu (soft delete)
     */
    public function destroy(Plat $plat)
    {
        $user = Auth::user();

        if ($plat->restaurateur_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $plat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu supprimé avec succès'
        ]);
    }

    /**
     * Déterminer le statut d'un menu
     */
    private function getMenuStatus($plat)
    {
        if ($plat->quantite_disponible <= 0) {
            return 'Épuisé';
        }

        if ($plat->quantite_disponible <= 5) {
            return 'Stock faible';
        }

        return 'Actif';
    }
}
