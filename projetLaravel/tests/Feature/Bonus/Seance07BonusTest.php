<?php

namespace Tests\Feature\Bonus;

use App\Models\Notification;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 7.5 🔴 — Le fil des alertes, la plus grave d'abord : GET /api/alertes */
#[Group('bonus')]
class Seance07BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s7_m5_les_alertes_rassemblent_gtr_et_notifications(): void
    {
        $this->connecter();
        $this->aHeure('10:00', ['detecte_a' => '09:18', 'important_depuis' => '09:50']);
        Service::factory()->indisponibleDepuis('09:18')->create(['code' => 'PPAY-PSP', 'gtr_minutes' => 30]);  // hors GTR
        Service::factory()->indisponibleDepuis('09:18')->create(['code' => 'CFU-WEB', 'gtr_minutes' => 50]);   // 84 % : risque
        Notification::factory()->create(['code' => 'CLIENT-PPAY', 'delai_minutes' => 30]);                   // en retard
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'delai_minutes' => 50]);                    // 10:08 : proche

        $alertes = $this->getJson('/api/alertes')->assertOk()->json();

        $this->assertSame(['gtr:PPAY-PSP', 'notification:CLIENT-PPAY', 'gtr:CFU-WEB', 'notification:CLIENT-CFU'], array_column($alertes, 'cle'));
    }
}
