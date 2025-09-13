<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationCommandeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'commande_id' => 'required|uuid|exists:commandes,id',
            'user_id' => 'required|uuid|exists:users,id',
            'type' => 'required|string|max:255',
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'is_read' => 'nullable|boolean',
            'sent_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'commande_id.required' => 'La commande est obligatoire.',
            'commande_id.uuid' => 'L\'ID commande doit être un UUID valide.',
            'commande_id.exists' => 'La commande spécifiée n\'existe pas.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.uuid' => 'L\'ID utilisateur doit être un UUID valide.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'type.required' => 'Le type de notification est obligatoire.',
            'type.max' => 'Le type ne peut pas dépasser 255 caractères.',
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'message.required' => 'Le message est obligatoire.',
            'sent_at.date' => 'La date d\'envoi doit être une date valide.',
        ];
    }
}

