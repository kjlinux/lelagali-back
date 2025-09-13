<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurateurMoyenPaiementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'moyen_paiement_id' => 'required|uuid|exists:moyen_paiements,id',
            'numero_compte' => 'nullable|string|max:255',
        ];

        // Contrainte unique pour la combinaison restaurateur_id et moyen_paiement_id
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['restaurateur_id'] = 'required|uuid|exists:users,id|unique:restaurateur_moyens_paiement,restaurateur_id,' . $this->route('restaurateurMoyenPaiement')->id . ',id,moyen_paiement_id,' . $this->input('moyen_paiement_id');
        } else {
            $rules['restaurateur_id'] = 'required|uuid|exists:users,id|unique:restaurateur_moyens_paiement,restaurateur_id,NULL,id,moyen_paiement_id,' . $this->input('moyen_paiement_id');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'restaurateur_id.required' => 'Le restaurateur est obligatoire.',
            'restaurateur_id.uuid' => 'L\'ID restaurateur doit être un UUID valide.',
            'restaurateur_id.exists' => 'Le restaurateur spécifié n\'existe pas.',
            'restaurateur_id.unique' => 'Ce moyen de paiement est déjà configuré pour ce restaurateur.',
            'moyen_paiement_id.required' => 'Le moyen de paiement est obligatoire.',
            'moyen_paiement_id.uuid' => 'L\'ID moyen de paiement doit être un UUID valide.',
            'moyen_paiement_id.exists' => 'Le moyen de paiement spécifié n\'existe pas.',
            'numero_compte.max' => 'Le numéro de compte ne peut pas dépasser 255 caractères.',
        ];
    }
}
