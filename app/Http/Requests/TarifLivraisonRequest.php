<?php
// TarifLivraisonRequest.php - VERSION CORRIGÉE

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
        if (!$this->has('restaurateur_id') || empty($this->restaurateur_id)) {
            $user = Auth::user();
            if ($user) {
                $this->merge([
                    'restaurateur_id' => $user->id
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

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $currentId = $this->route('tarifLivraison') ? $this->route('tarifLivraison')->id : null;
            if ($currentId) {
                $rules['restaurateur_id'] = 'required|uuid|exists:users,id|unique:tarif_livraisons,restaurateur_id,' . $currentId . ',id,quartier_id,' . $this->input('quartier_id');
            }
        } else {
            // Pour les créations - permettre l'upsert en supprimant la contrainte unique stricte
            // On gèrera la logique d'upsert dans le contrôleur
            $rules['restaurateur_id'] = 'required|uuid|exists:users,id';
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
