<?php

namespace Database\Factories;

use App\Models\Contrat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contrat>
 */
class ContratFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client' => 'CLI',
            'nom' => 'Client '.$this->faker->unique()->word(),
            'entite' => 'Établissement de crédit',
            'dora' => true,
            'autorite' => 'ACPR',
            'etats_membres' => 'FR',
            'delai_notification_minutes' => 60,
            'localisation' => 'France',
        ];
    }
}
