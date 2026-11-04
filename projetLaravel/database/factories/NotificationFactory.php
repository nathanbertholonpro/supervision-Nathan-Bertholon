<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('NOT-???')),
            'destinataire' => 'Client '.$this->faker->unique()->word(),
            'objet' => 'Information du client',
            'regle' => 'contrat',
            'delai_minutes' => 60,
            'depend_de_id' => null,
            'client' => 'CLI',
        ];
    }

    /** Une notification à l'ANSSI (NIS 2), délai en minutes depuis la qualification. */
    public function anssi(int $delai = 1440): static
    {
        return $this->state(['destinataire' => 'ANSSI', 'regle' => 'nis2', 'delai_minutes' => $delai, 'client' => null]);
    }

    /** Envoyée à « 10:00 » (le jour de l'exercice) ou à une date complète. */
    public function envoyee(string $heure = '10:00', string $par = 'Karim Belaïd'): static
    {
        $quand = strlen($heure) === 5 ? '2027-03-09 '.$heure : $heure;

        return $this->state(['envoyee_a' => $quand, 'envoyee_par' => $par]);
    }
}
