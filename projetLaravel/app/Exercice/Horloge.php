<?php

namespace App\Exercice;

use App\Models\Exercice;
use Carbon\CarbonImmutable;

/**
 * FOURNI — L'heure de l'exercice.
 *
 * L'exercice rejoue l'incident du 09/03/2027 de 09:00 à 13:00, plus vite que la réalité :
 * heure de l'exercice = début + (temps réel écoulé depuis le lancement × accélération).
 * En mode recette, l'horloge est figée (colonne « fige_a »).
 *
 * Toutes les règles reçoivent l'heure en paramètre au lieu de l'appeler : on les teste à l'heure voulue.
 */
class Horloge
{
    public static function maintenant(): CarbonImmutable
    {
        $exercice = Exercice::courant();

        if ($exercice === null) {
            return CarbonImmutable::parse(config('exercice.debut'));
        }
        if ($exercice->fige_a !== null) {
            return $exercice->fige_a;
        }
        if ($exercice->demarre_a === null) {
            return $exercice->debut;
        }

        $ecoule = max(0, $exercice->demarre_a->diffInSeconds(CarbonImmutable::now())) * $exercice->acceleration;
        $heure = $exercice->debut->addSeconds((int) $ecoule);

        return $heure->greaterThan($exercice->fin) ? $exercice->fin : $heure;
    }

    /** Début de l'exercice. */
    public static function debut(): CarbonImmutable
    {
        return Exercice::courant()?->debut ?? CarbonImmutable::parse(config('exercice.debut'));
    }

    /** « T+01:15 » : temps écoulé depuis le début de l'exercice. */
    public static function libelleT(): string
    {
        $minutes = (int) self::debut()->diffInMinutes(self::maintenant());

        return sprintf('T+%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
