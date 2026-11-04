<?php

namespace App\Regles;

use App\Models\Service;
use Carbon\CarbonInterface;

/**
 * RG1 — Durée d'indisponibilité et consommation de la GTR (garantie de temps de rétablissement).
 *
 * Une règle de gestion ne lit ni l'horloge ni la requête : on lui donne l'heure de l'exercice.
 * Elle se teste donc seule, à n'importe quelle heure.
 */
class Indisponibilite
{
    /**
     * Minutes écoulées depuis le début de l'indisponibilité, ou null si le service n'est pas indisponible.
     */
    public static function duree(Service $service, CarbonInterface $maintenant): ?int
    {
        throw new \LogicException('À écrire en séance 1 (mission 1.3).');
    }

    /**
     * Part de la GTR consommée (0.5 = la moitié), ou null si le service n'est pas indisponible.
     */
    public static function consommation(Service $service, CarbonInterface $maintenant): ?float
    {
        throw new \LogicException('À écrire en séance 1 (mission 1.3).');
    }

    /**
     * « ok », « risque » (à partir de 75 %) ou « hors_gtr » (au-delà de 100 %).
     */
    public static function niveau(Service $service, CarbonInterface $maintenant): string
    {
        throw new \LogicException('À écrire en séance 1 (mission 1.3).');
    }

    /**
     * Minutes restantes avant la fin de la GTR (négatif si elle est dépassée). Un service qui
     * fonctionne a toute sa GTR devant lui.
     */
    public static function marge(Service $service, CarbonInterface $maintenant): int
    {
        throw new \LogicException('À écrire en séance 1 (mission 1.2).');
    }
}
