<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'required|uuid|exists:users,id',
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'total_plats' => 'required|integer|min:0',
            'frais_livraison' => 'nullable|integer|min:0',
            'total_general' => 'required|integer|min:0',
            'type_service' => 'required|in:livraison,retrait',
            'adresse_livraison' => 'required_if:type_service,livraison|nullable|string',
            'quartier_livraison_id' => 'required_if:type_service,livraison|nullable|uuid|exists:quartiers,id',
            'moyen_paiement_id' => 'required|uuid|exists:moyen_paiements,id',
            'status' => 'nullable|in:en_attente,confirmee,prete,en_livraison,recuperee',
            'status_paiement' => 'nullable|boolean',
            'temps_preparation_estime' => 'nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Le client est obligatoire.',
            'client_id.uuid' => 'L\'ID client doit être un UUID valide.',
            'client_id.exists' => 'Le client spécifié n\'existe pas.',
            'restaurateur_id.required' => 'Le restaurateur est obligatoire.',
            'restaurateur_id.uuid' => 'L\'ID restaurateur doit être un UUID valide.',
            'restaurateur_id.exists' => 'Le restaurateur spécifié n\'existe pas.',
            'total_plats.required' => 'Le total des plats est obligatoire.',
            'total_plats.integer' => 'Le total des plats doit être un nombre entier.',
            'total_plats.min' => 'Le total des plats ne peut pas être négatif.',
            'frais_livraison.integer' => 'Les frais de livraison doivent être un nombre entier.',
            'frais_livraison.min' => 'Les frais de livraison ne peuvent pas être négatifs.',
            'total_general.required' => 'Le total général est obligatoire.',
            'total_general.integer' => 'Le total général doit être un nombre entier.',
            'total_general.min' => 'Le total général ne peut pas être négatif.',
            'type_service.required' => 'Le type de service est obligatoire.',
            'type_service.in' => 'Le type de service doit être soit "livraison" soit "retrait".',
            'adresse_livraison.required_if' => 'L\'adresse de livraison est obligatoire pour une livraison.',
            'quartier_livraison_id.required_if' => 'Le quartier de livraison est obligatoire pour une livraison.',
            'quartier_livraison_id.uuid' => 'L\'ID quartier doit être un UUID valide.',
            'quartier_livraison_id.exists' => 'Le quartier spécifié n\'existe pas.',
            'moyen_paiement_id.required' => 'Le moyen de paiement est obligatoire.',
            'moyen_paiement_id.uuid' => 'L\'ID moyen de paiement doit être un UUID valide.',
            'moyen_paiement_id.exists' => 'Le moyen de paiement spécifié n\'existe pas.',
            'status.in' => 'Le statut doit être: en_attente, confirmee, prete, en_livraison ou recuperee.',
            'temps_preparation_estime.integer' => 'Le temps de préparation doit être un nombre entier.',
            'temps_preparation_estime.min' => 'Le temps de préparation doit être d\'au moins 1 minute.',
        ];
    }
}

