<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed  (ou  php artisan exercice:demarrer, qui repart d'une base neuve)
     * L'ordre compte : le référentiel (composants, services, contrats, notifications), puis le scénario.
     */
    public function run(): void
    {
        $this->call([
            ReferentielSeeder::class,
            ScenarioSeeder::class,
        ]);
    }
}
