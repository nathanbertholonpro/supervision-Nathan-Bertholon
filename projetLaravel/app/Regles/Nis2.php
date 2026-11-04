<?php

namespace App\Regles;

use App\Models\Exercice;
use App\Models\Service;
use Carbon\CarbonInterface;

/**
 * RG5 — L'incident est-il « important » au sens de NIS 2 ?
 * Critères du règlement d'exécution (UE) 2024/2690 retenus pour Sevranne (cahier des charges, RG5).
 */
class Nis2
{
    public const CRITERES = ['indisponibilite_totale', 'disponibilite_limitee', 'malveillance', 'pertes'];

    /**
     * Seuil de pertes financières directes : 500 000 € ou 5 % du chiffre d'affaires, le plus bas des deux.
     */
    public static function seuilPertes(int $chiffreAffaires): int
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.1).');
    }

    /**
     * Les critères atteints à l'heure $maintenant, dans l'ordre de self::CRITERES.
     *
     * @param  iterable<Service>  $services
     * @return list<string>
     */
    public static function criteres(Exercice $exercice, iterable $services, CarbonInterface $maintenant): array
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.1).');
    }
}
