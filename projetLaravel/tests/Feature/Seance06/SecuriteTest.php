<?php

namespace Tests\Feature\Seance06;

use App\Models\Journal;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 6 — Comptes, rôles et signature : qui a le droit de notifier qui ?
 *
 *   php artisan test --group=seance-06
 */
#[Group('seance-06')]
class SecuriteTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 6.1 🟢 — Les comptes du centre et la connexion ─────

    public function test_m1_les_comptes_ont_un_role_un_portefeuille_et_un_mot_de_passe_hache(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['role', 'clients']));
        $this->seed();

        $hvartan = User::firstWhere('email', 'hvartan@sevranne.test');
        $this->assertNotNull($hvartan, 'Le compte de la directrice des opérations doit exister après le seed.');
        $this->assertSame('directeur', $hvartan->role);
        $this->assertNotSame(self::MOT_DE_PASSE, $hvartan->password);
        $this->assertTrue(Hash::check(self::MOT_DE_PASSE, $hvartan->password));
        $this->assertSame('rssi', User::firstWhere('email', 'kbelaid@sevranne.test')->role);
        $this->assertSame('auditeur', User::firstWhere('email', 'mdelorme@sevranne.test')->role);
        $this->assertSame('CFU,PPAY,MFA', User::firstWhere('email', 'lfournel@sevranne.test')->clients);
    }

    public function test_m1_on_se_connecte_avec_son_adresse_et_son_mot_de_passe(): void
    {
        $helene = $this->membre();

        $this->get('/connexion')->assertOk()->assertSee('name="password"', false);
        $this->post('/connexion', ['email' => $helene->email, 'password' => self::MOT_DE_PASSE])
            ->assertRedirect(route('accueil'));
        $this->assertAuthenticatedAs($helene);
    }

    public function test_m1_le_message_d_echec_ne_dit_pas_si_le_compte_existe(): void
    {
        $helene = $this->membre();

        $this->post('/connexion', ['email' => $helene->email, 'password' => 'mauvais']);
        $mauvaisMotDePasse = $this->messageErreur('email');
        $this->post('/connexion', ['email' => 'personne@sevranne.test', 'password' => 'mauvais']);
        $compteInconnu = $this->messageErreur('email');

        $this->assertGuest();
        $this->assertNotEmpty($mauvaisMotDePasse);
        $this->assertSame($mauvaisMotDePasse, $compteInconnu);
    }

    // ── Mission 6.2 🟢 — Tout protéger, puis ouvrir ce qui doit l'être ─

    public function test_m2_sans_connexion_les_moniteurs_renvoient_vers_la_connexion_et_l_api_repond_401(): void
    {
        $this->get('/')->assertRedirect('/connexion');
        $this->get('/moniteur/1')->assertRedirect('/connexion');
        $this->get('/moniteur/3')->assertRedirect('/connexion');
        $this->post('/journal', ['texte' => 'Intrus'])->assertRedirect('/connexion');
        $this->getJson('/api/etat')->assertUnauthorized();
        $this->assertSame(0, Journal::count());
    }

    public function test_m2_le_bandeau_montre_qui_est_connecte_et_permet_de_se_deconnecter(): void
    {
        $this->connecter();

        $this->get('/')->assertSee('Hélène Vartan')->assertSee('action="'.route('deconnexion').'"', false);
        $this->post(route('deconnexion'))->assertRedirect('/connexion');
        $this->assertGuest();
    }

    // ── Mission 6.3 🟡 — RG8 : chacun son rôle ────────────────────

    public function test_m3_un_auditeur_lit_tout_et_n_ecrit_rien(): void
    {
        $this->connecter($this->membre('Marc Delorme', 'auditeur'));
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'client' => 'CFU']);

        $this->get('/moniteur/3')->assertOk();
        $this->post('/journal', ['categorie' => 'fait', 'texte' => 'Je n\'ai pas le droit.'])->assertForbidden();
        $this->post('/notifications/CLIENT-CFU/envoyee')->assertForbidden();
        $this->assertSame(0, Journal::count());
    }

    public function test_m3_la_relation_client_ne_notifie_que_les_clients_de_son_portefeuille(): void
    {
        $this->aHeure('10:00');
        $this->connecter($this->membre('Léa Fournel', 'relation_client', 'CFU,PPAY,MFA'));
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'client' => 'CFU']);
        Notification::factory()->create(['code' => 'CLIENT-CSL', 'client' => 'CSL']);
        Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);

        $this->post('/notifications/CLIENT-CSL/envoyee')->assertForbidden();
        $this->post('/notifications/ANSSI-ALERTE/envoyee')->assertForbidden();
        $this->post('/notifications/CLIENT-CFU/envoyee')->assertRedirect();
        $this->assertNotNull(Notification::firstWhere('code', 'CLIENT-CFU')->envoyee_a);
    }

    public function test_m3_le_rssi_notifie_l_anssi_mais_le_rapport_final_engage_la_direction(): void
    {
        $this->aHeure('10:00', ['detecte_a' => '09:18', 'important_depuis' => '09:50']);
        $rssi = $this->membre('Karim Belaïd', 'rssi');
        $notif = Notification::factory()->anssi(4320)->envoyee('09:58')->create(['code' => 'ANSSI-NOTIF']);
        Notification::factory()->anssi()->create(['code' => 'ANSSI-ALERTE']);
        Notification::factory()->create(['code' => 'ANSSI-FINAL', 'regle' => 'nis2_final', 'delai_minutes' => null, 'depend_de_id' => $notif->id, 'client' => null]);

        $this->connecter($rssi);
        $this->post('/notifications/ANSSI-ALERTE/envoyee')->assertRedirect();
        $this->post('/notifications/ANSSI-FINAL/envoyee')->assertForbidden();

        $this->connecter($this->membre('Hélène Vartan', 'directeur'));
        $this->post('/notifications/ANSSI-FINAL/envoyee')->assertRedirect();
        $this->assertSame('Hélène Vartan', Notification::firstWhere('code', 'ANSSI-FINAL')->envoyee_par);
    }

    // ── Mission 6.4 🟡 — Signé par la personne connectée ──────────

    public function test_m4_l_auteur_est_toujours_la_personne_connectee(): void
    {
        $this->aHeure('10:00');
        $this->connecter($this->membre('Léa Fournel', 'relation_client', 'CFU,PPAY,MFA'));
        Notification::factory()->create(['code' => 'CLIENT-CFU', 'client' => 'CFU']);

        $this->post('/journal', ['categorie' => 'fait', 'texte' => 'Le Crédit Furanais rappelle.', 'auteur' => 'Hélène Vartan']);
        $this->post('/notifications/CLIENT-CFU/envoyee', ['auteur' => 'Hélène Vartan']);

        $this->assertSame('Léa Fournel', Journal::firstWhere('texte', 'Le Crédit Furanais rappelle.')->auteur);
        $this->assertSame('Léa Fournel', Notification::firstWhere('code', 'CLIENT-CFU')->envoyee_par);
    }
}
