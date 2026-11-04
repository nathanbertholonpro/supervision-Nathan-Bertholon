<?php

namespace Tests\Feature\Seance01;

use App\Models\Service;
use App\Regles\Indisponibilite;
use App\Regles\OrdreRetablissement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 1 — Moniteur 1 : les services et leur disponibilité
 *
 *   php artisan test --group=seance-01
 */
#[Group('seance-01')]
class ServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    private function service(string $code, int $criticite, int $gtr, string $etat = 'normal', ?string $depuis = null): Service
    {
        return Service::factory()->create([
            'code' => $code, 'criticite' => $criticite, 'gtr_minutes' => $gtr, 'etat' => $etat,
            'etat_depuis' => $depuis ? self::JOUR.' '.$depuis : null,
            'indisponible_depuis' => $etat === 'indisponible' ? self::JOUR.' '.$depuis : null,
        ]);
    }

    // ── Mission 1.2 🟢 — RG2 : l'ordre de rétablissement ──────────

    public function test_m2_la_marge_est_la_gtr_moins_la_duree_d_indisponibilite(): void
    {
        $web = $this->service('CFU-WEB', 1, 60, 'indisponible', '09:18');
        $mutu = $this->service('MUTU', 3, 480);

        $this->assertSame(18, Indisponibilite::marge($web, $this->h('10:00')));
        $this->assertSame(-12, Indisponibilite::marge($this->service('PPAY-PSP', 1, 30, 'indisponible', '09:18'), $this->h('10:00')));
        $this->assertSame(480, Indisponibilite::marge($mutu, $this->h('10:00')), 'Un service qui fonctionne a toute sa GTR devant lui.');
    }

    public function test_m2_les_indisponibles_d_abord_puis_les_degrades_puis_les_normaux(): void
    {
        $normal = $this->service('NOR', 1, 30);
        $degrade = $this->service('DEG', 1, 30, 'degrade', '09:30');
        $indisponible = $this->service('IND', 3, 480, 'indisponible', '10:00');

        $ordre = OrdreRetablissement::trier([$normal, $degrade, $indisponible], $this->h('10:30'))->pluck('code')->all();

        $this->assertSame(['IND', 'DEG', 'NOR'], $ordre);
    }

    public function test_m2_dans_un_meme_etat_la_criticite_puis_la_marge_puis_le_code(): void
    {
        $c2 = $this->service('C2', 2, 30, 'indisponible', '09:00');          // marge −50, mais criticité 2
        $large = $this->service('LARGE', 1, 240, 'indisponible', '09:20');   // marge 180
        $court = $this->service('COURT', 1, 60, 'indisponible', '09:20');    // marge 0
        $egalB = $this->service('EGAL-B', 2, 240, 'indisponible', '09:20');  // marge 180, criticité 2
        $egalA = $this->service('EGAL-A', 2, 240, 'indisponible', '09:20');  // même marge : le code départage

        $ordre = OrdreRetablissement::trier([$c2, $large, $court, $egalB, $egalA], $this->h('10:20'))->pluck('code')->all();

        $this->assertSame(['COURT', 'LARGE', 'C2', 'EGAL-A', 'EGAL-B'], $ordre);
    }

    public function test_m2_le_moniteur_1_affiche_les_services_dans_l_ordre_de_retablissement(): void
    {
        $this->aHeure('10:00');
        $this->service('MUTU', 3, 480);
        $this->service('CFU-WEB', 1, 60, 'indisponible', '09:18');
        $this->service('PPAY-PSP', 1, 30, 'indisponible', '09:18');

        $this->get(route('moniteur.services'))
            ->assertOk()
            ->assertSeeInOrder(['data-service="PPAY-PSP"', 'data-service="CFU-WEB"', 'data-service="MUTU"'], false);
    }

    // ── Mission 1.3 🟡 — RG1 : la GTR d'un coup d'œil ─────────────

    public function test_m3_duree_d_indisponibilite_et_consommation_de_la_gtr(): void
    {
        $ged = $this->service('MFA-GED', 1, 120, 'indisponible', '09:18');

        $this->assertSame(42, Indisponibilite::duree($ged, $this->h('10:00')));
        $this->assertEqualsWithDelta(42 / 120, Indisponibilite::consommation($ged, $this->h('10:00')), 0.001);
        $this->assertNull(Indisponibilite::duree($this->service('MUTU', 3, 480), $this->h('10:00')));
        $this->assertNull(Indisponibilite::consommation($this->service('CSL-DPI', 1, 60, 'degrade', '10:05'), $this->h('10:30')));
    }

    public function test_m3_les_seuils_75_et_100_pour_cent(): void
    {
        $s = $this->service('X', 1, 120, 'indisponible', '09:00');

        $this->assertSame('ok', Indisponibilite::niveau($s, $this->h('10:29')));        // 89 min = 74 %
        $this->assertSame('risque', Indisponibilite::niveau($s, $this->h('10:30')));    // 90 min = 75 % pile
        $this->assertSame('risque', Indisponibilite::niveau($s, $this->h('11:00')));    // 100 % pile : pas encore au-delà
        $this->assertSame('hors_gtr', Indisponibilite::niveau($s, $this->h('11:01')));  // au-delà
        $this->assertSame('ok', Indisponibilite::niveau($this->service('N', 1, 60), $this->h('11:01')));
    }

    public function test_m3_chaque_ligne_montre_la_duree_la_gtr_et_son_niveau(): void
    {
        $this->aHeure('10:00');
        $this->service('PPAY-PSP', 1, 30, 'indisponible', '09:18');
        $this->service('CFU-WEB', 1, 60, 'indisponible', '08:59');

        $this->get(route('moniteur.services'))
            ->assertSee('data-niveau="hors_gtr"', false)
            ->assertSee('42 min')   // indisponibilité de la passerelle de paiement
            ->assertSee('1 h 01')   // indisponibilité du portail
            ->assertSee('data-gtr="60"', false)
            ->assertSee('>1 h<', false); // la GTR du portail, écrite comme on la lit
    }

    // ── Mission 1.4 🟡 — Combien de services dans chaque état ? ───

    public function test_m4_une_tuile_par_etat_meme_vide(): void
    {
        $this->aHeure('10:00');
        $this->service('A', 1, 60, 'indisponible', '09:18');
        $this->service('B', 2, 60, 'indisponible', '09:18');
        $this->service('C', 3, 60);

        $this->get(route('moniteur.services'))
            ->assertSee('data-etat="indisponible" data-nombre="2"', false)
            ->assertSee('data-etat="degrade" data-nombre="0"', false)
            ->assertSee('data-etat="normal" data-nombre="1"', false);
    }
}
