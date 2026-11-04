<?php

namespace App\Console\Commands;

use App\Models\Exercice;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * FOURNI — (Re)démarre l'exercice : base remise à neuf, horloge à 09:00.
 *
 *   php artisan exercice:demarrer                  l'exercice se déroule, 20 fois plus vite
 *   php artisan exercice:demarrer --figer=10:00    mode recette : tout est joué jusqu'à 10:00, puis l'horloge s'arrête
 *   php artisan exercice:demarrer --figer=11:30 --sans-remise-a-zero
 *                                                  avance l'horloge figée en gardant tout ce qui a été fait
 */
class DemarrerExercice extends Command
{
    protected $signature = 'exercice:demarrer {--figer= : heure HH:MM à laquelle figer l\'horloge (09:00 à 13:00)} {--sans-remise-a-zero : garder la base telle qu\'elle est}';

    protected $description = "Démarre (ou redémarre) l'exercice de supervision";

    private const REPERES = ['detecte_a', 'important_depuis', 'malveillance_a', 'reputation_a', 'pertes_estimees'];

    public function handle(): int
    {
        $figer = $this->option('figer');
        if ($figer !== null && ! preg_match('/^(09|1[0-2]):[0-5]\d$|^13:00$/', $figer)) {
            $this->error('Heure attendue au format HH:MM, entre 09:00 et 13:00 (exemple : --figer=10:00).');

            return self::FAILURE;
        }

        if (! $this->option('sans-remise-a-zero')) {
            $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        }

        $debut = CarbonImmutable::parse(config('exercice.debut'));
        $fin = CarbonImmutable::parse(config('exercice.fin'));
        $fige = $figer ? CarbonImmutable::parse($debut->format('Y-m-d').' '.$figer) : null;

        // Sans remise à zéro, on garde les repères déjà posés par le scénario (détection, qualification…)
        $precedent = $this->option('sans-remise-a-zero') ? Exercice::courant() : null;

        Exercice::query()->delete();
        Exercice::create(array_merge([
            'nom' => config('exercice.nom'),
            'debut' => $debut,
            'fin' => $fin,
            'demarre_a' => CarbonImmutable::now(),
            'acceleration' => config('exercice.acceleration'),
            'fige_a' => $fige,
        ], $precedent ? $precedent->only(self::REPERES) : []));

        $this->info($fige
            ? "Exercice figé à {$figer} (mode recette)."
            : 'Exercice démarré : 09:00 → 13:00, '.config('exercice.acceleration').' fois plus vite que la réalité.');

        return self::SUCCESS;
    }
}
