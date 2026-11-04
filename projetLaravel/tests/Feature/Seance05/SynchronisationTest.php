<?php

namespace Tests\Feature\Seance05;

use App\Models\Journal;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 5 — Les quatre moniteurs synchronisés (API, ETag, BroadcastChannel)
 *
 *   php artisan test --group=seance-05
 */
#[Group('seance-05')]
class SynchronisationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    // ── Mission 5.1 🟢 — Une API qui dit si quelque chose a changé ─

    public function test_m1_l_api_d_etat_donne_l_heure_et_une_version(): void
    {
        $this->aHeure('10:15');
        Service::factory()->create(['code' => 'CFU-WEB']);

        $this->getJson('/api/etat')
            ->assertOk()
            ->assertJsonPath('heure', '10:15')
            ->assertJsonPath('t', 'T+01:15')
            ->assertJsonStructure(['heure', 't', 'version']);
    }

    public function test_m1_la_version_change_quand_l_etat_change_et_seulement_alors(): void
    {
        $this->aHeure('10:15');
        $web = Service::factory()->create(['code' => 'CFU-WEB']);
        $n = Notification::factory()->create(['code' => 'CLIENT-CFU']);

        $v1 = $this->getJson('/api/etat')->json('version');
        $this->assertSame($v1, $this->getJson('/api/etat')->json('version'), 'Rien n\'a changé : même version.');

        Journal::factory()->create(['texte' => 'Nouvelle entrée']);
        $v2 = $this->getJson('/api/etat')->json('version');
        $this->assertNotSame($v1, $v2);

        $this->travel(5)->seconds();
        $web->changerEtat('indisponible', $this->h('10:15'));
        $v3 = $this->getJson('/api/etat')->json('version');
        $this->assertNotSame($v2, $v3);

        $this->travel(5)->seconds();
        $n->update(['envoyee_a' => $this->h('10:15'), 'envoyee_par' => 'Léa Fournel']);
        $this->assertNotSame($v3, $this->getJson('/api/etat')->json('version'), 'Une notification envoyée change aussi l\'état.');
    }

    public function test_m1_si_rien_n_a_change_le_serveur_repond_304_sans_contenu(): void
    {
        $this->aHeure('10:15');

        $reponse = $this->getJson('/api/etat');
        $etag = $reponse->headers->get('ETag');
        $this->assertNotEmpty($etag, 'La réponse doit porter un en-tête ETag.');

        $this->getJson('/api/etat', ['If-None-Match' => $etag])->assertStatus(304)->assertContent('');
    }

    // ── Mission 5.2 🟢 — Les moniteurs écoutent au lieu de se recharger ─

    public function test_m2_les_moniteurs_ne_se_rechargent_plus_et_chargent_le_script_commun(): void
    {
        $this->get(route('moniteur.services'))
            ->assertDontSee('http-equiv="refresh"', false)
            ->assertSee('js/moniteur.js', false);

        $script = file_get_contents(public_path('js/moniteur.js'));
        $this->assertStringContainsString('/api/etat', $script);
        $this->assertStringContainsString('If-None-Match', $script);
    }

    // ── Mission 5.3 🟡 — Le poste et ses quatre écrans ────────────

    public function test_m3_l_api_du_poste_donne_l_adresse_de_chaque_moniteur(): void
    {
        $this->getJson('/api/poste')
            ->assertOk()
            ->assertJsonCount(4, 'moniteurs')
            ->assertJsonPath('moniteurs.0.vue', 'services')
            ->assertJsonPath('moniteurs.0.url', route('moniteur.services'))
            ->assertJsonPath('moniteurs.2.url', route('moniteur.notifications'))
            ->assertJsonPath('moniteurs.3.position', 'bas-droite');
    }

    // ── Mission 5.4 🟡 — RG7 : je clique sur le 1, le 3 suit ──────

    public function test_m4_les_notifications_se_filtrent_sur_le_client_du_service_selectionne(): void
    {
        $this->aHeure('10:00', ['detecte_a' => '09:18', 'important_depuis' => '09:50']);
        Service::factory()->create(['code' => 'PPAY-PSP', 'client' => 'PPAY']);
        Notification::factory()->create(['code' => 'CLIENT-PPAY', 'client' => 'PPAY']);
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'client' => 'CFU']);
        Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);

        $this->get(route('moniteur.notifications', ['service' => 'PPAY-PSP']))
            ->assertSee('data-notification="CLIENT-PPAY"', false)
            ->assertSee('data-notification="ANSSI-ALERTE"', false)
            ->assertDontSee('data-notification="CLIENT-CFU"', false)
            ->assertSee('data-selection="PPAY-PSP"', false);
    }

    public function test_m4_le_moniteur_1_diffuse_la_selection_aux_autres_fenetres(): void
    {
        Service::factory()->create(['code' => 'PPAY-PSP']);

        $this->get(route('moniteur.services'))->assertSee('data-selectionnable', false);
        $this->assertStringContainsString('BroadcastChannel', file_get_contents(public_path('js/moniteur.js')));
    }
}
