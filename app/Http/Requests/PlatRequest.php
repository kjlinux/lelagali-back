<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlatRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer|min:0',
            'quantite_disponible' => 'nullable|integer|min:0',
            'quantite_vendue' => 'nullable|integer|min:0',
            'image' => 'nullable|string|max:255',
            'categorie_id' => 'nullable|uuid|exists:categorie_plats,id',
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'date_disponibilite' => 'required|date|after_or_equal:tomorrow',
            'is_approved' => 'nullable|boolean',
            'approved_by' => 'nullable|uuid|exists:users,id',
            'approved_at' => 'nullable|date',
            'temps_preparation' => 'nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du plat est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.integer' => 'Le prix doit être un nombre entier.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'quantite_disponible.integer' => 'La quantité disponible doit être un nombre entier.',
            'quantite_disponible.min' => 'La quantité disponible ne peut pas être négative.',
            'quantite_vendue.integer' => 'La quantité vendue doit être un nombre entier.',
            'quantite_vendue.min' => 'La quantité vendue ne peut pas être négative.',
            'categorie_id.uuid' => 'L\'ID catégorie doit être un UUID valide.',
            'categorie_id.exists' => 'La catégorie spécifiée n\'existe pas.',
            'restaurateur_id.required' => 'Le restaurateur est obligatoire.',
            'restaurateur_id.uuid' => 'L\'ID restaurateur doit être un UUID valide.',
            'restaurateur_id.exists' => 'Le restaurateur spécifié n\'existe pas.',
            'date_disponibilite.required' => 'La date de disponibilité est obligatoire.',
            'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
            'date_disponibilite.after_or_equal' => 'La date de disponibilité doit être au minimum demain.',
            'approved_by.uuid' => 'L\'ID de l\'approbateur doit être un UUID valide.',
            'approved_by.exists' => 'L\'approbateur spécifié n\'existe pas.',
            'temps_preparation.integer' => 'Le temps de préparation doit être un nombre entier.',
            'temps_preparation.min' => 'Le temps de préparation doit être d\'au moins 1 minute.',
        ];
    }
}

