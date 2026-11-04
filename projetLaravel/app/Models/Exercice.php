<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * L'exercice en cours (FOURNI). Une seule ligne utile : la dernière.
 */
class Exercice extends Model
{
    protected $fillable = [
        'nom', 'debut', 'fin', 'demarre_a', 'acceleration', 'fige_a',
        'detecte_a', 'important_depuis', 'malveillance_a', 'reputation_a', 'pertes_estimees',
    ];

    protected function casts(): array
    {
        return [
            'debut' => 'immutable_datetime',
            'fin' => 'immutable_datetime',
            'demarre_a' => 'immutable_datetime',
            'fige_a' => 'immutable_datetime',
            'detecte_a' => 'immutable_datetime',
            'important_depuis' => 'immutable_datetime',
            'malveillance_a' => 'immutable_datetime',
            'reputation_a' => 'immutable_datetime',
            'pertes_estimees' => 'integer',
        ];
    }

    public static function courant(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
