<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandeItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'commande_id' => 'required|uuid|exists:commandes,id',
            'plat_id' => 'required|uuid|exists:plats,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|integer|min:0',
            'prix_total' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'commande_id.required' => 'La commande est obligatoire.',
            'commande_id.uuid' => 'L\'ID commande doit être un UUID valide.',
            'commande_id.exists' => 'La commande spécifiée n\'existe pas.',
            'plat_id.required' => 'Le plat est obligatoire.',
            'plat_id.uuid' => 'L\'ID plat doit être un UUID valide.',
            'plat_id.exists' => 'Le plat spécifié n\'existe pas.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être d\'au moins 1.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.integer' => 'Le prix unitaire doit être un nombre entier.',
            'prix_unitaire.min' => 'Le prix unitaire ne peut pas être négatif.',
            'prix_total.required' => 'Le prix total est obligatoire.',
            'prix_total.integer' => 'Le prix total doit être un nombre entier.',
            'prix_total.min' => 'Le prix total ne peut pas être négatif.',
        ];
    }
}

