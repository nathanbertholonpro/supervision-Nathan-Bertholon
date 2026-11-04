<?php

namespace App\Regles;

use App\Models\Contrat;
use App\Models\Exercice;
use App\Models\Service;
use Carbon\CarbonInterface;

/**
 * RG9 — Aide à la classification DORA d'un client financier (règlement délégué (UE) 2024/1772).
 * C'est le CLIENT qui classifie et notifie son autorité : Sevranne lui fournit les éléments.
 */
class Dora
{
    public const SEUILS = ['clients', 'duree', 'interruption', 'geographie', 'donnees', 'reputation'];

    /**
     * Les éléments de classification que Sevranne peut établir pour ce client à l'heure $maintenant.
     *
     * @param  iterable<Service>  $services  les services du client
     * @return array{critique: bool, seuils: list<string>}
     */
    public static function evaluer(Contrat $contrat, iterable $services, Exercice $exercice, CarbonInterface $maintenant): array
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.2).');
    }

    /**
     * Incident majeur probable : services critiques touchés ET (accès malveillant OU au moins deux autres seuils).
     *
     * @param  array{critique: bool, seuils: list<string>}  $evaluation
     */
    public static function majeurProbable(array $evaluation): bool
    {
        throw new \LogicException('À écrire en séance 7 (mission 7.2).');
    }
}
