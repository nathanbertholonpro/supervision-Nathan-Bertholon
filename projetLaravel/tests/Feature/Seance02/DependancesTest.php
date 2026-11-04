<?php

namespace Tests\Feature\Seance02;

use App\Models\Composant;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 2 — Les dépendances des services (association porteuse) et l'historique des états
 *
 *   php artisan test --group=seance-02
 */
#[Group('seance-02')]
class DependancesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    // ── Mission 2.1 🟢 — L'association porteuse service_composant ──

    public function test_m1_la_table_d_association_porte_le_mode(): void
    {
        $this->assertTrue(Schema::hasTable('service_composant'), 'La table « service_composant » n\'existe pas.');
        $this->assertTrue(Schema::hasColumns('service_composant', ['service_id', 'composant_id', 'mode']));
        $this->assertFalse(Schema::hasColumn('services', 'composants'), 'L\'ancienne colonne texte services.composants doit disparaître.');
    }

    public function test_m1_un_service_depend_de_composants_et_reciproquement(): void
    {
        $web = Service::factory()->create(['code' => 'CFU-WEB']);
        $virt = Composant::factory()->create(['code' => 'VIRT-1']);
        $reseau = Composant::factory()->create(['code' => 'RESEAU-DC1']);

        $web->composants()->attach($virt, ['mode' => 'bloquant']);
        $web->composants()->attach($reseau, ['mode' => 'redondant']);

        $this->assertSame(['RESEAU-DC1', 'VIRT-1'], $web->composants()->orderBy('code')->pluck('code')->all());
        $this->assertSame('redondant', $web->composants()->where('code', 'RESEAU-DC1')->first()->pivot->mode);
        $this->assertSame(['CFU-WEB'], $virt->services->pluck('code')->all());
    }

    // ── Mission 2.3 🟡 — La fiche d'un service ────────────────────

    public function test_m3_le_referentiel_indique_le_mode_de_chaque_dependance(): void
    {
        $this->seed();

        $ged = Service::where('code', 'MFA-GED')->first();
        $this->assertSame('bloquant', $ged->composants()->where('code', 'SAN-A')->first()->pivot->mode);
        $this->assertSame('redondant', $ged->composants()->where('code', 'SAUV-DC2')->first()->pivot->mode);
        $this->assertSame(3, Service::where('code', 'PPAY-PSP')->first()->composants()->count());
    }

    public function test_m3_la_fiche_service_montre_ses_composants_leur_mode_et_leur_etat(): void
    {
        $psp = Service::factory()->create(['code' => 'PPAY-PSP', 'nom' => 'Passerelle de paiement des commerçants']);
        $san = Composant::factory()->create(['code' => 'SAN-A', 'nom' => 'Baie de stockage du DC1', 'etat' => 'hors_service', 'tiers' => 'Ardoise Stockage']);
        $psp->composants()->attach($san, ['mode' => 'bloquant']);

        $this->get(route('services.show', 'PPAY-PSP'))
            ->assertOk()
            ->assertSee('Passerelle de paiement des commerçants')
            ->assertSee('Ardoise Stockage')
            ->assertSee('data-composant="SAN-A" data-mode="bloquant" data-etat="hors_service"', false);
    }

    public function test_m3_le_moniteur_1_mene_a_la_fiche_de_chaque_service(): void
    {
        Service::factory()->create(['code' => 'PPAY-PSP']);

        $this->get(route('moniteur.services'))->assertSee(route('services.show', 'PPAY-PSP'), false);
    }

    // ── Mission 2.4 🟡 — L'historique des états ───────────────────

    public function test_m4_chaque_changement_d_etat_est_conserve(): void
    {
        $this->assertTrue(Schema::hasTable('etats_service'), 'La table « etats_service » n\'existe pas.');
        $web = Service::factory()->create(['code' => 'CFU-WEB']);

        $web->changerEtat('indisponible', $this->h('09:18'));
        $web->changerEtat('degrade', $this->h('11:20'));
        $web->changerEtat('degrade', $this->h('11:30'));   // même état : rien de nouveau

        $this->assertSame(['indisponible', 'degrade'], $web->historique()->pluck('etat')->all());
        $this->assertEquals($this->h('09:18'), $web->historique()->first()->debut);
    }
}
