<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationCommande;

class NotificationCommandeController extends Controller
{
    /**
     * Récupérer toutes les notifications
     */
    public function index(Request $request)
    {
        $query = NotificationCommande::query()
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'desc');

        // Filtrage par statut de lecture
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Filtrage par type
        if ($request->has('type')) {
            $query->whereIn('type', (array)$request->get('type'));
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $notifications = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => $this->getUnreadCount()
            ]
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = NotificationCommande::where('id', $id)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marquée comme lue',
            'data' => $notification,
            'unread_count' => $this->getUnreadCount()
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        DB::transaction(function () {
            NotificationCommande::where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Toutes les notifications ont été marquées comme lues',
            'unread_count' => 0
        ]);
    }

    /**
     * Supprimer une notification (soft delete)
     */
    public function destroy($id)
    {
        $notification = NotificationCommande::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification supprimée',
            'unread_count' => $this->getUnreadCount()
        ]);
    }

    /**
     * Créer une nouvelle notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:order,user,payment,system,info,warning,success,error',
            'user_id' => 'nullable|uuid|exists:users,id',
            'action_required' => 'boolean',
            'data' => 'nullable|array'
        ]);

        $notification = NotificationCommande::create([
            ...$validated,
            'is_read' => false,
            'read_at' => null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification créée avec succès',
            'data' => $notification
        ], 201);
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function unreadCount()
    {
        return response()->json([
            'status' => 'success',
            'unread_count' => $this->getUnreadCount()
        ]);
    }

    /**
     * Obtenir les notifications récentes
     */
    public function recent()
    {
        $notifications = NotificationCommande::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhereNull('user_id');
        })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => $this->getUnreadCount()
        ]);
    }

    /**
     * Calculer le nombre de notifications non lues
     */
    private function getUnreadCount(): int
    {
        return NotificationCommande::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhereNull('user_id');
        })
            ->where('is_read', false)
            ->count();
    }
}
