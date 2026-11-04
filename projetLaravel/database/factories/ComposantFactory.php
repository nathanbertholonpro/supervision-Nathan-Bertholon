<?php

namespace Database\Factories;

use App\Models\Composant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Composant>
 */
class ComposantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('CMP-???')),
            'nom' => 'Composant '.$this->faker->unique()->word(),
            'tiers' => null,
            'etat' => 'operationnel',
            'etat_depuis' => null,
        ];
    }
}
