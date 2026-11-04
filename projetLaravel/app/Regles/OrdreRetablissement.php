<?php

namespace App\Regles;

use App\Models\Service;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * RG2 — Dans quel ordre le centre de supervision doit-il rétablir les services ?
 */
class OrdreRetablissement
{
    /**
     * @param  iterable<Service>  $services
     * @return Collection<int, Service>
     */
    public static function trier(iterable $services, CarbonInterface $maintenant): Collection
    {
        throw new \LogicException('À écrire en séance 1 (mission 1.2).');
    }
}
