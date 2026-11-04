<?php

namespace App\Exercice;

use App\Models\Composant;
use App\Models\Exercice;
use App\Models\Inject;
use App\Models\Journal;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

/**
 * FOURNI — Joue les injects du scénario dont l'heure est arrivée.
 *
 * Appelé au début de chaque requête (middleware RattraperExercice) : pas besoin de tâche de fond.
 * Chaque inject est joué UNE fois (colonne « joue_a »), même si deux requêtes arrivent ensemble :
 * la lecture se fait sous verrou, dans une transaction.
 *
 * Un inject peut porter, dans « donnees » :
 *   composants    : {"SAN-A": "hors_service"}   → état des composants
 *   services      : {"CFU-WEB": "indisponible"} → état des services
 *   detection     : true      → l'incident est détecté (départ des délais contractuels des clients)
 *   qualification : true      → l'incident est qualifié d'important au sens de NIS 2 (départ des délais ANSSI)
 *   malveillance  : true      → une action malveillante est suspectée
 *   reputation    : true      → l'incident est relayé par les médias
 *   pertes        : 620000    → pertes directes estimées, en euros
 * et il laisse toujours une entrée dans le journal d'incident.
 */
class Moteur
{
    public const AUTEUR = "Animation de l'exercice";

    private const REPERES = [
        'detection' => 'detecte_a',
        'qualification' => 'important_depuis',
        'malveillance' => 'malveillance_a',
        'reputation' => 'reputation_a',
    ];

    public static function rattraper(): int
    {
        if (Exercice::courant() === null) {
            return 0;
        }
        $maintenant = Horloge::maintenant();

        return DB::transaction(function () use ($maintenant) {
            $dus = Inject::query()
                ->whereNull('joue_a')
                ->where('heure', '<=', $maintenant)
                ->orderBy('heure')->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($dus as $inject) {
                self::jouer($inject);
                $inject->update(['joue_a' => $inject->heure]);
            }

            return $dus->count();
        });
    }

    public static function jouer(Inject $inject): void
    {
        $d = $inject->donnees ?? [];
        $heure = $inject->heure;

        foreach ($d['composants'] ?? [] as $code => $etat) {
            Composant::where('code', $code)->first()?->changerEtat($etat, $heure);
        }
        foreach ($d['services'] ?? [] as $code => $etat) {
            Service::where('code', $code)->first()?->changerEtat($etat, $heure);
        }

        $exercice = Exercice::courant();
        if ($exercice) {
            foreach (self::REPERES as $cle => $colonne) {
                if (! empty($d[$cle]) && $exercice->{$colonne} === null) {
                    $exercice->{$colonne} = $heure;
                }
            }
            if (isset($d['pertes'])) {
                $exercice->pertes_estimees = (int) $d['pertes'];
            }
            $exercice->save();
        }

        Journal::create([
            'heure' => $heure,
            'categorie' => $inject->categorie,
            'texte' => $inject->titre,
            'auteur' => self::AUTEUR,
            'source' => 'scenario',
            'inject_id' => $inject->id,
        ]);
    }
}
