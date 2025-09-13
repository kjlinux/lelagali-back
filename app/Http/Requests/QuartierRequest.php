<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuartierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'created_by' => 'required|uuid|exists:users,id',
        ];

        // Règles spécifiques pour la mise à jour
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['nom'] = 'required|string|max:255|unique:quartiers,nom,' . $this->route('quartier')->id;
        } else {
            $rules['nom'] = 'required|string|max:255|unique:quartiers,nom';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du quartier est obligatoire.',
            'nom.unique' => 'Ce quartier existe déjà.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'created_by.required' => 'L\'utilisateur créateur est obligatoire.',
            'created_by.uuid' => 'L\'ID utilisateur doit être un UUID valide.',
            'created_by.exists' => 'L\'utilisateur spécifié n\'existe pas.',
        ];
    }
}
