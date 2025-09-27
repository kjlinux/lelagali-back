<?php
// TarifLivraisonRequest.php - VERSION FINALE CORRIGÉE

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TarifLivraisonRequest extends FormRequest
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
            $tarifLivraison = $this->route('tarifLivraison');
            if ($tarifLivraison) {
                $this->merge([
                    'restaurateur_id' => $tarifLivraison->restaurateur_id,
                    'quartier_id' => $tarifLivraison->quartier_id
                ]);
            }
        }
    }

    public function rules()
    {
        $rules = [
            'restaurateur_id' => 'required|uuid|exists:users,id',
            'quartier_id' => 'required|uuid|exists:quartiers,id',
            'prix' => 'required|integer|min:0',
        ];

        // ✅ Pour les mises à jour, pas de contrainte unique car on peut changer le prix
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Les IDs ne changent pas, donc pas de validation unique nécessaire
        } else {
            // Pour les créations, on gère l'upsert dans le contrôleur
            // donc pas de contrainte unique stricte ici
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'restaurateur_id.required' => 'Le restaurateur est obligatoire.',
            'restaurateur_id.uuid' => 'L\'ID restaurateur doit être un UUID valide.',
            'restaurateur_id.exists' => 'Le restaurateur spécifié n\'existe pas.',
            'quartier_id.required' => 'Le quartier est obligatoire.',
            'quartier_id.uuid' => 'L\'ID quartier doit être un UUID valide.',
            'quartier_id.exists' => 'Le quartier spécifié n\'existe pas.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.integer' => 'Le prix doit être un nombre entier.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
