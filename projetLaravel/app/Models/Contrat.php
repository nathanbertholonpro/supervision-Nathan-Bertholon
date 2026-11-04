<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Le contrat avec un client. Pour une entité financière (DORA), c'est une ligne de son registre
 * d'informations : services couverts, délai de notification, sous-traitants, localisation des données.
 */
class Contrat extends Model
{
    use HasFactory;

    protected $fillable = ['client', 'nom', 'entite', 'dora', 'autorite', 'etats_membres', 'delai_notification_minutes', 'localisation'];

    protected function casts(): array
    {
        return [
            'dora' => 'boolean',
            'delai_notification_minutes' => 'integer',
        ];
    }

    /** ['FR', 'BE'] */
    public function etatsMembres(): array
    {
        return array_values(array_filter(array_map('trim', explode(';', (string) $this->etats_membres))));
    }
}
