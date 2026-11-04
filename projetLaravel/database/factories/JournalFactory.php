<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Journal>
 */
class JournalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'heure' => '2027-03-09 09:30',
            'categorie' => 'fait',
            'texte' => $this->faker->sentence(),
            'auteur' => 'Thomas Guérin',
            'source' => 'centre',
        ];
    }
}
