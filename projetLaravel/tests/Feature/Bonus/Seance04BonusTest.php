<?php

namespace Tests\Feature\Bonus;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 4.5 🔴 — Les notifications en JSON, pour l'outil de tickets : GET /api/notifications */
#[Group('bonus')]
class Seance04BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s4_m5_l_api_liste_les_notifications_avec_statut_et_echeance(): void
    {
        $this->connecter();
        $this->aHeure('10:00', ['detecte_a' => '09:18', 'important_depuis' => '09:50']);
        Notification::factory()->create(['code' => 'CLIENT-PPAY', 'delai_minutes' => 30]);
        Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('0.code', 'CLIENT-PPAY')
            ->assertJsonPath('0.statut', 'en_retard')
            ->assertJsonPath('0.echeance', '2027-03-09 09:48')
            ->assertJsonPath('1.code', 'ANSSI-ALERTE');
    }
}
