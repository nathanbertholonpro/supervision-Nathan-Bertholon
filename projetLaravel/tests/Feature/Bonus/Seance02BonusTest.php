<?php

namespace Tests\Feature\Bonus;

use App\Models\Composant;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 2.5 🔴 — Si ce composant tombe : /composants/{code}/impact */
#[Group('bonus')]
class Seance02BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s2_m5_l_impact_d_un_composant_liste_les_services_qui_tombent(): void
    {
        $this->connecter();
        $san = Composant::factory()->create(['code' => 'SAN-A']);
        $web = Service::factory()->create(['code' => 'CFU-WEB']);
        $ged = Service::factory()->create(['code' => 'MFA-GED']);
        $sauv = Service::factory()->create(['code' => 'CFU-SAUV']);
        $web->composants()->attach($san, ['mode' => 'bloquant']);
        $ged->composants()->attach($san, ['mode' => 'redondant']);

        $this->get(route('composants.impact', 'SAN-A'))
            ->assertOk()
            ->assertSee('data-service="CFU-WEB" data-impact="indisponible"', false)
            ->assertSee('data-service="MFA-GED" data-impact="degrade"', false)
            ->assertDontSee('data-service="CFU-SAUV"', false);
    }
}
