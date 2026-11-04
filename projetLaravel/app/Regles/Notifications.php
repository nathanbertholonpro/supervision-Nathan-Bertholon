<?php

namespace App\Regles;

use App\Models\Exercice;
use App\Models\Notification;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * RG6 — Les notifications : échéances, ordre (prérequis), envoi une seule fois.
 */
class Notifications
{
    /**
     * Heure limite de la notification, ou null si son délai ne court pas encore.
     */
    public static function echeance(Notification $notification, Exercice $exercice): ?CarbonImmutable
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.1).');
    }

    /**
     * Vrai si la notification dont elle dépend est envoyée (ou si elle ne dépend de rien).
     */
    public static function peutEtreEnvoyee(Notification $notification): bool
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.1).');
    }

    /**
     * « envoyee », « en_retard », « bloquee », « en_attente », « proche » ou « a_envoyer ».
     */
    public static function statut(Notification $notification, Exercice $exercice, CarbonInterface $maintenant): string
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.1).');
    }

    /**
     * Marque la notification envoyée par $auteur à $heure et l'écrit au journal — une seule fois.
     *
     * @throws RegleNonRespectee si elle est déjà envoyée ou si son prérequis ne l'est pas
     */
    public static function envoyer(Notification $notification, string $auteur, CarbonInterface $heure): Notification
    {
        throw new \LogicException('À écrire en séance 4 (mission 4.2).');
    }
}
