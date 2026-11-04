<?php

/*
| Messages de validation en français — les règles utilisées dans l'application.
| Une règle absente d'ici s'affiche en anglais (APP_FALLBACK_LOCALE=en).
| Liste complète des règles : https://laravel.com/docs/validation#available-validation-rules
*/

return [
    'boolean' => 'Le champ :attribute doit valoir vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'exists' => 'La valeur choisie pour :attribute n\'existe pas.',
    'in' => 'La valeur choisie pour :attribute n\'est pas dans la liste.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit valoir au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le mot de passe doit contenir au moins une lettre.',
        'mixed' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
        'symbols' => 'Le mot de passe doit contenir au moins un symbole.',
        'uncompromised' => 'Ce mot de passe apparaît dans une fuite de données : choisissez-en un autre.',
    ],
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',

    // Noms des champs tels que l'utilisateur les lit
    'attributes' => [
        'categorie' => 'catégorie',
        'texte' => 'texte',
        'auteur' => 'auteur',
        'corrige_id' => 'entrée corrigée',
        'name' => 'nom',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
    ],
];
