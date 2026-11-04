<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Un service rendu à un client : son contrat (GTR), sa criticité pour le client, son état.
 * Criticité 1 : le service soutient une fonction critique ou importante du client (au sens de DORA).
 */
class Service extends Model
{
    use HasFactory;

    public const ETATS = ['normal', 'degrade', 'indisponible'];

    protected $fillable = [
        'code', 'client', 'nom', 'type', 'criticite', 'gtr_minutes', 'utilisateurs',
        'composants', 'etat', 'etat_depuis', 'indisponible_depuis',
    ];

    protected function casts(): array
    {
        return [
            'criticite' => 'integer',
            'gtr_minutes' => 'integer',
            'utilisateurs' => 'integer',
            'etat_depuis' => 'immutable_datetime',
            'indisponible_depuis' => 'immutable_datetime',
        ];
    }

    /**
     * Change l'état du service à l'heure de l'exercice donnée.
     * « etat_depuis » : depuis quand il est dans cet état ; « indisponible_depuis » : le début de
     * l'indisponibilité en cours (RG1), vide quand le service fonctionne, même dégradé.
     */
    public function changerEtat(string $etat, \DateTimeInterface $heure): void
    {
        if ($this->etat === $etat) {
            return;
        }

        $this->etat = $etat;
        $this->etat_depuis = $heure;
        $this->indisponible_depuis = $etat === 'indisponible' ? $heure : null;
        $this->save();
    }
}
