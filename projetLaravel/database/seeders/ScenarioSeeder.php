<?php

namespace Database\Seeders;

use App\Models\Inject;
use Illuminate\Database\Seeder;

/**
 * FOURNI — Le scénario de l'exercice du 9 mars 2027 : database/data/scenario-exercice.json
 */
class ScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $scenario = json_decode(file_get_contents(config('exercice.scenario')), true);

        foreach ($scenario['injects'] as $i) {
            Inject::create($i);
        }
    }
}
