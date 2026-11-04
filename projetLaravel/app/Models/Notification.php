<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une notification prévue : à l'autorité (ANSSI, NIS 2) ou à un client (contrat, DORA).
 *   regle « nis2 »       : qualification de l'incident important + delai_minutes
 *   regle « nis2_final » : un mois après l'envoi de la notification dont elle dépend
 *   regle « contrat »    : détection de l'incident + delai_minutes (délai fixé au contrat)
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'destinataire', 'objet', 'regle', 'delai_minutes', 'depend_de_id', 'client', 'envoyee_a', 'envoyee_par',
    ];

    protected function casts(): array
    {
        return [
            'delai_minutes' => 'integer',
            'envoyee_a' => 'immutable_datetime',
        ];
    }

    public function dependDe(): BelongsTo
    {
        return $this->belongsTo(self::class, 'depend_de_id');
    }

    public function estEnvoyee(): bool
    {
        return $this->envoyee_a !== null;
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
