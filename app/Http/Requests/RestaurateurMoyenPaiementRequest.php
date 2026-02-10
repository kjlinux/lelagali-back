<?php
// RestaurateurMoyenPaiementRequest.php - VERSION FINALE CORRIGÉE

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RestaurateurMoyenPaiementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // ✅ CORRECTION : Assurer que restaurateur_id est toujours présent
        if (!$this->has('restaurateur_id') || empty($this->restaurateur_id)) {
            $user = Auth::user();
            if ($user) {
                $this->merge([
                    'restaurateur_id' => $user->id
                ]);
            }
        }

        // ✅ CORRECTION : Pour les mises à jour, garder les IDs existants
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $restaurateurMoyenPaiement = $this->route('restaurateurMoyenPaiement');
            if ($restaurateurMoyenPaiement) {
                $this->merge([
                    'restaurateur_id' => $restaurateurMoyenPaiement->restaurateur_id,
                    'moyen_paiement_id' => $restaurateurMoyenPaiement->moyen_paiement_id
                ]);
            }
        }
    }

    public function rules()
    {
        $rules = [
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'numero_compte' => 'nullable|string|max:255',
            'nom_titulaire' => 'nullable|string|max:255',
        ];

        // ✅ Gestion différente selon le contexte
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Pour les mises à jour, ne pas valider moyen_paiement_id
            // car on ne le change pas
        } else {
            // Pour les créations
            $rules['moyen_paiement_id'] = 'required|uuid|exists:moyen_paiements,id';

            // ✅ Vérifier l'unicité pour les créations seulement
            $rules['restaurateur_id'] = $rules['restaurateur_id'] . '|unique:restaurateur_moyens_paiement,restaurateur_id,NULL,id,moyen_paiement_id,' . ($this->input('moyen_paiement_id') ?? 'NULL');
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
            'nom_titulaire.max' => 'Le nom du titulaire ne peut pas dépasser 255 caractères.',
        ];
    }
}
