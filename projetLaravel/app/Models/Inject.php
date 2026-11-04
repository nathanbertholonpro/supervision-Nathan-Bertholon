<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un événement du scénario d'exercice, joué à son heure (FOURNI).
 */
class Inject extends Model
{
    protected $fillable = ['heure', 'categorie', 'titre', 'donnees', 'joue_a'];

    protected function casts(): array
    {
        return [
            'heure' => 'immutable_datetime',
            'joue_a' => 'immutable_datetime',
            'donnees' => 'array',
        ];
    }
}
