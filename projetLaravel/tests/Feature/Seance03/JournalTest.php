<?php

namespace Tests\Feature\Seance03;

use App\Models\Journal;
use App\Regles\RegleNonRespectee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 3 — Moniteur 2 : le journal d'incident, horodaté et inaltérable
 *
 *   php artisan test --group=seance-03
 */
#[Group('seance-03')]
class JournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    private function entree(string $heure, string $texte, string $categorie = 'fait', string $source = 'centre'): Journal
    {
        return Journal::factory()->create([
            'heure' => self::JOUR.' '.$heure, 'texte' => $texte, 'categorie' => $categorie, 'source' => $source,
        ]);
    }

    // ── Mission 3.1 🟢 — La page du journal ───────────────────────

    public function test_m1_le_journal_affiche_les_entrees_les_plus_recentes_en_premier(): void
    {
        $this->aHeure('10:00');
        $this->entree('09:18', 'Les machines virtuelles s\'arrêtent.', 'fait', 'scenario');
        $this->entree('09:26', 'Ouverture de l\'incident INC-2027-031.', 'decision');
        $this->entree('09:40', 'Le DC2 est préparé pour la bascule.', 'action');

        $this->get(route('moniteur.journal'))
            ->assertOk()
            ->assertSeeInOrder(['09:40', 'Le DC2 est préparé', '09:26', 'Ouverture de l', '09:18', 'Les machines virtuelles']);
    }

    public function test_m1_chaque_entree_montre_sa_categorie_et_son_auteur_et_se_distingue_si_elle_vient_du_scenario(): void
    {
        $this->entree('09:18', 'Les machines virtuelles s\'arrêtent.', 'fait', 'scenario');
        $this->entree('09:26', 'Ouverture de l\'incident INC-2027-031.', 'decision');

        $this->get(route('moniteur.journal'))
            ->assertSee('data-categorie="decision"', false)
            ->assertSee('Thomas Guérin')
            ->assertSee('class="scenario', false);
    }

    // ── Mission 3.2 🟢 — Écrire dans le journal ───────────────────

    public function test_m2_une_entree_est_horodatee_a_l_heure_de_l_exercice(): void
    {
        $this->aHeure('10:15');

        $this->post(route('journal.store'), [
            'categorie' => 'decision', 'texte' => 'Bascule de PilatPay sur le DC2 en priorité.', 'auteur' => 'Hélène Vartan',
        ])->assertRedirect(route('moniteur.journal'));

        $e = Journal::firstWhere('texte', 'Bascule de PilatPay sur le DC2 en priorité.');
        $this->assertNotNull($e);
        $this->assertEquals($this->h('10:15'), $e->heure, 'L\'heure est celle de l\'exercice, pas celle de votre ordinateur.');
        $this->assertSame('decision', $e->categorie);
        $this->assertSame('centre', $e->source);
        $this->assertSame('Hélène Vartan', $e->auteur);
    }

    public function test_m2_une_saisie_incomplete_est_refusee_avec_un_message_en_francais(): void
    {
        $this->from(route('moniteur.journal'))
            ->post(route('journal.store'), ['categorie' => 'rumeur', 'texte' => 'ok', 'auteur' => 'Hélène Vartan'])
            ->assertRedirect(route('moniteur.journal'))
            ->assertSessionHasErrors(['categorie', 'texte']);

        $this->assertSame(0, Journal::count());
        $this->assertStringContainsString('texte', strtolower($this->messageErreur('texte') ?? ''));
    }

    public function test_m2_le_moniteur_2_propose_le_formulaire_avec_sa_protection_csrf(): void
    {
        $this->get(route('moniteur.journal'))
            ->assertSee('action="'.route('journal.store').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="categorie"', false);
    }

    // ── Mission 3.3 🟡 — RG4 : une entrée ne se modifie jamais ────

    public function test_m3_aucune_route_ne_modifie_ni_ne_supprime_une_entree(): void
    {
        $this->assertTrue(Route::has('journal.store'), 'La route d\'ajout « journal.store » doit exister.');
        foreach (Route::getRoutes() as $route) {
            if (str_starts_with($route->uri(), 'journal')) {
                $this->assertEmpty(
                    array_intersect($route->methods(), ['PUT', 'PATCH', 'DELETE']),
                    "La route {$route->uri()} permet de modifier le journal."
                );
            }
        }
    }

    public function test_m3_le_modele_refuse_de_modifier_ou_supprimer_une_entree(): void
    {
        $e = $this->entree('09:30', 'Texte d\'origine.');

        try {
            $e->update(['texte' => 'Texte réécrit.']);
            $this->fail('Une entrée du journal ne doit jamais être modifiée.');
        } catch (RegleNonRespectee) {
        }
        try {
            $e->delete();
            $this->fail('Une entrée du journal ne doit jamais être supprimée.');
        } catch (RegleNonRespectee) {
        }

        $this->assertDatabaseHas('journal', ['id' => $e->id, 'texte' => 'Texte d\'origine.']);
    }

    public function test_m3_une_correction_est_une_nouvelle_entree_qui_cite_l_ancienne(): void
    {
        $this->assertTrue(Schema::hasColumn('journal', 'corrige_id'));
        $this->aHeure('10:00');
        $erreur = $this->entree('09:40', 'Bascule de PilatPay vers le DC3.');

        $this->post(route('journal.store'), [
            'categorie' => 'action', 'texte' => 'Bascule de PilatPay vers le DC2 (et non le DC3).',
            'auteur' => 'Thomas Guérin', 'corrige_id' => $erreur->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('journal', ['texte' => 'Bascule de PilatPay vers le DC3.']);
        $this->assertDatabaseHas('journal', ['corrige_id' => $erreur->id]);
        $this->get(route('moniteur.journal'))
            ->assertSee('class="centre corrige"', false)
            ->assertSee('corrige l\'entrée de 09:40', false);
    }

    // ── Mission 3.4 🟡 — Retrouver une information ────────────────

    public function test_m4_filtrer_par_categorie_avec_le_nombre_d_entrees_de_chacune(): void
    {
        $this->entree('09:26', 'Ouverture de l\'incident.', 'decision');
        $this->entree('09:40', 'Le DC2 est préparé.', 'action');
        $this->entree('09:45', 'PilatPay rappelle.', 'fait');

        $this->get(route('moniteur.journal', ['categorie' => 'decision']))
            ->assertSee('Ouverture de l\'incident.')
            ->assertDontSee('PilatPay rappelle.')
            ->assertSee('data-categorie-filtre="decision" data-nombre="1"', false)
            ->assertSee('data-categorie-filtre="notification" data-nombre="0"', false);
    }
}
