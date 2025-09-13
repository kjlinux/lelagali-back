<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifLivraisonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'quartier_id' => 'required|uuid|exists:quartiers,id',
            'prix' => 'required|integer|min:0',
        ];

        // Contrainte unique pour la combinaison restaurateur_id et quartier_id
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['restaurateur_id'] = 'required|uuid|exists:users,id|unique:tarif_livraisons,restaurateur_id,' . $this->route('tarifLivraison')->id . ',id,quartier_id,' . $this->input('quartier_id');
        } else {
            $rules['restaurateur_id'] = 'required|uuid|exists:users,id|unique:tarif_livraisons,restaurateur_id,NULL,id,quartier_id,' . $this->input('quartier_id');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'restaurateur_id.required' => 'Le restaurateur est obligatoire.',
            'restaurateur_id.uuid' => 'L\'ID restaurateur doit être un UUID valide.',
            'restaurateur_id.exists' => 'Le restaurateur spécifié n\'existe pas.',
            'restaurateur_id.unique' => 'Un tarif existe déjà pour ce restaurateur dans ce quartier.',
            'quartier_id.required' => 'Le quartier est obligatoire.',
            'quartier_id.uuid' => 'L\'ID quartier doit être un UUID valide.',
            'quartier_id.exists' => 'Le quartier spécifié n\'existe pas.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.integer' => 'Le prix doit être un nombre entier.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
