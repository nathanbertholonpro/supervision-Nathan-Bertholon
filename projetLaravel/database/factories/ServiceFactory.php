<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('SRV-???')),
            'client' => 'CLI',
            'nom' => 'Service '.$this->faker->unique()->word(),
            'type' => 'infogerance',
            'criticite' => 2,
            'gtr_minutes' => 240,
            'utilisateurs' => 100,
            'composants' => '',
            'etat' => 'normal',
            'etat_depuis' => null,
            'indisponible_depuis' => null,
        ];
    }

    /** Indisponible depuis « 09:20 » (le jour de l'exercice) ou une date complète. */
    public function indisponibleDepuis(string $heure): static
    {
        $quand = strlen($heure) === 5 ? '2027-03-09 '.$heure : $heure;

        return $this->state(['etat' => 'indisponible', 'etat_depuis' => $quand, 'indisponible_depuis' => $quand]);
    }

    /** Dégradé depuis « 10:05 » (le jour de l'exercice). */
    public function degradeDepuis(string $heure = '09:00'): static
    {
        $quand = strlen($heure) === 5 ? '2027-03-09 '.$heure : $heure;

        return $this->state(['etat' => 'degrade', 'etat_depuis' => $quand]);
    }

    public function colocation(): static
    {
        return $this->state(['type' => 'colocation']);
    }
}
