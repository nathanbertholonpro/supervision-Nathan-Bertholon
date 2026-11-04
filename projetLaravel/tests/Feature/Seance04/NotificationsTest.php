<?php

namespace Tests\Feature\Seance04;

use App\Models\Exercice;
use App\Models\Journal;
use App\Models\Notification;
use App\Models\Service;
use App\Regles\Nis2;
use App\Regles\Notifications;
use App\Regles\RegleNonRespectee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 4 — L'incident est-il important (NIS 2) ? Moniteur 3 : les horloges de notification
 *
 *   php artisan test --group=seance-04
 */
#[Group('seance-04')]
class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter($this->membre('Hélène Vartan', 'directeur'));
    }

    private function exercice(array $reperes = ['detecte_a' => '09:18', 'important_depuis' => '09:50']): Exercice
    {
        $this->aHeure('10:00', $reperes);

        return Exercice::courant();
    }

    // ── Mission 4.1 🟢 — RG5 et RG6 : qualifier, puis compter ─────

    public function test_m1_le_seuil_de_pertes_est_le_plus_bas_de_500_000_euros_et_5_pour_cent_du_ca(): void
    {
        $this->assertSame(500000, Nis2::seuilPertes(32000000));
        $this->assertSame(200000, Nis2::seuilPertes(4000000));
    }

    public function test_m1_les_criteres_de_l_incident_important(): void
    {
        $exercice = $this->exercice(['detecte_a' => '09:18', 'malveillance_a' => '10:10']);
        $infogerance = Service::factory()->indisponibleDepuis('09:18')->create();
        $colocation = Service::factory()->colocation()->degradeDepuis('10:05')->create();

        $this->assertSame([], Nis2::criteres($exercice, [$infogerance], $this->h('09:48')), '30 minutes pile : pas encore « plus de 30 minutes ».');
        $this->assertSame(['indisponibilite_totale'], Nis2::criteres($exercice, [$infogerance], $this->h('09:49')));
        $this->assertSame(['malveillance'], Nis2::criteres($exercice, [$colocation], $this->h('11:05')), 'Dégradé depuis 60 min pile : pas encore.');
        $this->assertSame(['disponibilite_limitee', 'malveillance'], Nis2::criteres($exercice, [$colocation], $this->h('11:06')));

        $coupe = Service::factory()->colocation()->indisponibleDepuis('10:59')->create();
        $this->assertSame(['indisponibilite_totale'], Nis2::criteres($this->exercice([]), [$coupe], $this->h('11:00')),
            'Un service de centre de données totalement indisponible : sans durée minimale (art. 8).');

        $this->aHeure('12:30', [], 620000);
        $this->assertSame(['pertes'], Nis2::criteres(Exercice::courant(), [], $this->h('12:30')));
    }

    public function test_m1_l_echeance_depend_de_la_regle(): void
    {
        $exercice = $this->exercice();
        $client = Notification::factory()->create(['delai_minutes' => 60]);
        $alerte = Notification::factory()->anssi(1440)->create();
        $notif = Notification::factory()->anssi(4320)->envoyee('2027-03-11 15:00')->create();
        $final = Notification::factory()->create(['regle' => 'nis2_final', 'delai_minutes' => null, 'depend_de_id' => $notif->id, 'client' => null]);

        $this->assertEquals($this->h('10:18'), Notifications::echeance($client, $exercice), 'Contrat : détection + délai.');
        $this->assertEquals($this->h('09:50')->addDay(), Notifications::echeance($alerte, $exercice), 'NIS 2 : qualification + 24 h.');
        $this->assertEquals('2027-04-11 15:00', Notifications::echeance($final, $exercice)->format('Y-m-d H:i'), 'Rapport final : un mois après la notification.');

        $this->assertNull(Notifications::echeance($alerte, $this->exercice(['detecte_a' => '09:18'])), 'Pas encore qualifié : le délai ne court pas.');
    }

    public function test_m1_le_statut_d_une_notification(): void
    {
        $exercice = $this->exercice();
        $client = Notification::factory()->create(['code' => 'CLIENT-CFU', 'delai_minutes' => 60]);          // 10:18
        $alerte = Notification::factory()->anssi(1440)->create(['code' => 'ANSSI-ALERTE']);
        $notif = Notification::factory()->anssi(30)->create(['code' => 'ANSSI-NOTIF', 'depend_de_id' => $alerte->id]); // 10:20
        $envoyee = Notification::factory()->envoyee('09:40')->create();

        $this->assertSame('a_envoyer', Notifications::statut($client, $exercice, $this->h('10:02')));
        $this->assertSame('proche', Notifications::statut($client, $exercice, $this->h('10:03')), 'Il reste un quart du délai : proche.');
        $this->assertSame('proche', Notifications::statut($client, $exercice, $this->h('10:18')), 'À l\'heure pile, pas encore en retard.');
        $this->assertSame('en_retard', Notifications::statut($client, $exercice, $this->h('10:19')));
        $this->assertSame('bloquee', Notifications::statut($notif, $exercice, $this->h('10:00')));
        $this->assertSame('en_retard', Notifications::statut($notif, $exercice, $this->h('10:21')), 'Bloquée ET échue : le retard prime.');
        $this->assertSame('envoyee', Notifications::statut($envoyee, $exercice, $this->h('12:00')));
        $this->assertSame('en_attente', Notifications::statut($alerte, $this->exercice(['detecte_a' => '09:18']), $this->h('10:00')));
        $this->assertFalse(Notifications::peutEtreEnvoyee($notif));
        $this->assertTrue(Notifications::peutEtreEnvoyee($alerte));
    }

    public function test_m1_le_moniteur_2_montre_les_criteres_atteints_et_l_heure_de_qualification(): void
    {
        $this->aHeure('10:15', ['detecte_a' => '09:18', 'important_depuis' => '09:50', 'malveillance_a' => '10:10']);
        Service::factory()->indisponibleDepuis('09:18')->create();

        $this->get(route('moniteur.journal'))
            ->assertSee('data-critere="indisponibilite_totale" data-atteint="1"', false)
            ->assertSee('data-critere="malveillance" data-atteint="1"', false)
            ->assertSee('data-critere="pertes" data-atteint="0"', false)
            ->assertSee('data-important-depuis="09:50"', false);
    }

    // ── Mission 4.2 🟢 — Moniteur 3 : envoyer une notification ────

    public function test_m2_le_moniteur_3_liste_les_notifications_par_echeance_avec_leur_statut(): void
    {
        $this->exercice();
        Notification::factory()->create(['code' => 'CLIENT-MFA', 'delai_minutes' => 120]);    // 11:18
        Notification::factory()->create(['code' => 'CLIENT-PPAY', 'delai_minutes' => 30]);    // 09:48
        Notification::factory()->anssi(1440)->create(['code' => 'ANSSI-ALERTE']);             // mer. 09:50
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'delai_minutes' => 60]);     // 10:18

        $this->get(route('moniteur.notifications'))
            ->assertOk()
            ->assertSeeInOrder(['data-notification="CLIENT-PPAY"', 'data-notification="CLIENT-CFU"', 'data-notification="CLIENT-MFA"', 'data-notification="ANSSI-ALERTE"'], false)
            ->assertSee('data-notification="CLIENT-PPAY" data-statut="en_retard"', false)
            ->assertSee('data-notification="CLIENT-MFA" data-statut="a_envoyer"', false)
            ->assertSee('11:18');
    }

    public function test_m2_envoyer_une_notification_la_date_la_signe_et_l_ecrit_au_journal(): void
    {
        $this->exercice();
        Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);

        $this->post(route('notifications.envoyee', 'ANSSI-ALERTE'), ['auteur' => 'Hélène Vartan'])
            ->assertRedirect(route('moniteur.notifications'));

        $n = Notification::firstWhere('code', 'ANSSI-ALERTE');
        $this->assertEquals($this->h('10:00'), $n->envoyee_a);
        $this->assertSame('Hélène Vartan', $n->envoyee_par);
        $this->assertDatabaseHas('journal', [
            'categorie' => 'notification', 'auteur' => 'Hélène Vartan', 'texte' => 'Notification envoyée — ANSSI (ANSSI-ALERTE)',
        ]);
    }

    // ── Mission 4.3 🟡 — Les refus ────────────────────────────────

    public function test_m3_une_notification_dont_le_prerequis_n_est_pas_envoye_est_refusee_avec_409(): void
    {
        $this->exercice();
        $alerte = Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);
        Notification::factory()->anssi(4320)->create(['code' => 'ANSSI-NOTIF', 'depend_de_id' => $alerte->id]);

        $this->postJson(route('notifications.envoyee', 'ANSSI-NOTIF'), ['auteur' => 'Karim Belaïd'])
            ->assertStatus(409)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'ANSSI-ALERTE'));

        $this->assertNull(Notification::firstWhere('code', 'ANSSI-NOTIF')->envoyee_a);
        $this->assertSame(0, Journal::count());
    }

    public function test_m3_une_notification_deja_envoyee_ne_s_ecrit_pas_deux_fois(): void
    {
        $this->exercice();
        Notification::factory()->create(['code' => 'CLIENT-CFU']);

        $this->postJson(route('notifications.envoyee', 'CLIENT-CFU'), ['auteur' => 'Léa Fournel'])->assertOk();
        $this->postJson(route('notifications.envoyee', 'CLIENT-CFU'), ['auteur' => 'Léa Fournel'])->assertStatus(409);

        $this->assertSame(1, Journal::where('categorie', 'notification')->count());
    }

    public function test_m3_une_notification_inconnue_donne_404_et_la_regle_leve_une_exception_metier(): void
    {
        $this->postJson(route('notifications.envoyee', 'INCONNUE'), ['auteur' => 'Léa Fournel'])->assertNotFound();

        $alerte = Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);
        $notif = Notification::factory()->anssi(4320)->create(['code' => 'ANSSI-NOTIF', 'depend_de_id' => $alerte->id]);
        $this->expectException(RegleNonRespectee::class);
        Notifications::envoyer($notif, 'Karim Belaïd', $this->h('10:00'));
    }

    // ── Mission 4.4 🟡 — Tout ou rien : la transaction ────────────

    public function test_m4_si_le_journal_ne_peut_pas_s_ecrire_la_notification_n_est_pas_envoyee(): void
    {
        $this->exercice();
        Notification::factory()->create(['code' => 'CLIENT-CFU']);
        Journal::creating(fn () => throw new \RuntimeException('Disque plein'));

        $this->withoutExceptionHandling();
        try {
            $this->post(route('notifications.envoyee', 'CLIENT-CFU'), ['auteur' => 'Léa Fournel']);
            $this->fail('L\'erreur aurait dû remonter.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Disque plein', $e->getMessage());
        }

        $this->assertNull(Notification::firstWhere('code', 'CLIENT-CFU')->envoyee_a,
            'Sans transaction, la notification est « envoyée » mais le journal n\'en garde aucune trace.');
    }
}
