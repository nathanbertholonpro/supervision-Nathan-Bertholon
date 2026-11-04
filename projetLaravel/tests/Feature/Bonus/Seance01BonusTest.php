<?php

namespace Tests\Feature\Bonus;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 1.5 🔴 — Filtrer par client : /moniteur/1?client=CFU */
#[Group('bonus')]
class Seance01BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s1_m5_le_moniteur_1_filtre_par_client(): void
    {
        $this->connecter();
        $this->aHeure('10:00');
        Service::factory()->create(['code' => 'CFU-WEB', 'client' => 'CFU']);
        Service::factory()->create(['code' => 'MUTU', 'client' => 'SEV']);

        $this->get(route('moniteur.services', ['client' => 'CFU']))
            ->assertSee('data-service="CFU-WEB"', false)
            ->assertDontSee('data-service="MUTU"', false);
    }
}
