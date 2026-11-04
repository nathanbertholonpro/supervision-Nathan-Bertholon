<?php

/*
|--------------------------------------------------------------------------
| L'exercice de supervision rejoué par l'application (FOURNI)
|--------------------------------------------------------------------------
| Le scénario (incident INC-2027-031) se déroule le mardi 9 mars 2027 de 9 h à 13 h.
| L'application le rejoue « acceleration » fois plus vite que la réalité.
| Figer l'horloge (mode recette) :  php artisan exercice:demarrer --figer=10:00
*/

return [
    'nom' => 'Exercice de supervision — incident INC-2027-031',
    'debut' => '2027-03-09 09:00:00',
    'fin' => '2027-03-09 13:00:00',
    'acceleration' => (int) env('EXERCICE_ACCELERATION', 20),
    'scenario' => database_path('data/scenario-exercice.json'),

    // Seuil de la règle RG1 (consommation de la GTR)
    'seuils' => [
        'risque' => 0.75,
    ],

    // Sevranne Datacenter (fictif) : le chiffre d'affaires sert au seuil de pertes de NIS 2 (RG5)
    'organisation' => [
        'nom' => 'Sevranne Datacenter',
        'chiffre_affaires' => 32000000,
    ],
];
