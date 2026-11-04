<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Un composant de l'infrastructure dont dépendent les services : baie de stockage, cluster, réseau…
 * « tiers » : le prestataire qui le fournit ou le maintient (chaîne d'approvisionnement).
 */
class Composant extends Model
{
    use HasFactory;

    public const ETATS = ['operationnel', 'degrade', 'hors_service', 'secours'];

    protected $fillable = ['code', 'nom', 'tiers', 'etat', 'etat_depuis'];

    protected function casts(): array
    {
        return ['etat_depuis' => 'immutable_datetime'];
    }

    public function changerEtat(string $etat, \DateTimeInterface $heure): void
    {
        if ($this->etat === $etat) {
            return;
        }
        $this->update(['etat' => $etat, 'etat_depuis' => $heure]);
    }
}
