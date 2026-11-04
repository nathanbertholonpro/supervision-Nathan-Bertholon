<?php

namespace App\Regles;

use App\Models\Service;
use Illuminate\Support\Collection;

/**
 * RG10 — Registre d'informations et risque de concentration sur un prestataire.
 */
class Registre
{
    /**
     * Les prestataires (tiers) dont dépend un service, sans doublon, triés.
     *
     * @return list<string>
     */
    public static function sousTraitants(Service $service): array
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.3).');
    }

    /**
     * Pour chaque prestataire, les services de criticité 1 qui en dépendent de façon bloquante,
     * le prestataire le plus chargé d'abord.
     *
     * @param  iterable<Service>  $services
     * @return Collection<string, list<string>> tiers => codes des services
     */
    public static function concentration(iterable $services): Collection
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.3).');
    }
}
