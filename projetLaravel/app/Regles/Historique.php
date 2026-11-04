<?php

namespace App\Regles;

use App\Models\Service;
use Carbon\CarbonInterface;

/**
 * Ce que l'historique des états d'un service (séance 2) permet de dire, à l'heure $maintenant.
 * Utilisé par l'aide à la classification DORA (séance 7) et par le retour d'expérience (séance 8).
 */
class Historique
{
    /**
     * Vrai si le service a été indisponible, ou dégradé, à un moment de l'incident (jusqu'à $maintenant).
     */
    public static function aEteTouche(Service $service, CarbonInterface $maintenant): bool
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.2).');
    }

    /**
     * Vrai si le service a été totalement indisponible à un moment de l'incident.
     */
    public static function aEteIndisponible(Service $service, CarbonInterface $maintenant): bool
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.2).');
    }

    /**
     * La plus longue indisponibilité continue, en minutes (0 s'il n'a jamais été indisponible).
     * Une indisponibilité en cours compte jusqu'à $maintenant.
     */
    public static function plusLongueIndisponibilite(Service $service, CarbonInterface $maintenant): int
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.2).');
    }
}
