<?php

namespace Tests\Feature\Seance07;

use App\Models\Composant;
use App\Models\Contrat;
use App\Models\Exercice;
use App\Models\Notification;
use App\Models\Service;
use App\Regles\Dora;
use App\Regles\Historique;
use App\Regles\Registre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 7 — Moniteur 4 : tiers, clients financiers (DORA), registre et indicateurs
 *
 *   php artisan test --group=seance-07
 */
#[Group('seance-07')]
class TiersClientsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    /** Un service de criticité donnée, passé par les états donnés : ['09:18' => 'indisponible', '11:20' => 'degrade']. */
    private function service(string $code, string $client, int $criticite, array $etats = []): Service
    {
        $s = Service::factory()->create(['code' => $code, 'client' => $client, 'criticite' => $criticite]);
        foreach ($etats as $heure => $etat) {
            $s->changerEtat($etat, $this->h($heure));
        }

        return $s->refresh();
    }

    // ── Mission 7.1 🟢 — Le moniteur 4 : composants et prestataires ─

    public function test_m1_le_moniteur_4_montre_chaque_composant_son_etat_et_son_prestataire(): void
    {
        $this->aHeure('10:00');
        Composant::factory()->create(['code' => 'SAN-A', 'etat' => 'hors_service', 'tiers' => 'Ardoise Stockage (maintenance)']);
        Composant::factory()->create(['code' => 'VIRT-2', 'etat' => 'secours']);

        $this->get(route('moniteur.tiers'))
            ->assertOk()
            ->assertSee('data-composant="SAN-A" data-etat="hors_service"', false)
            ->assertSee('data-composant="VIRT-2" data-etat="secours"', false)
            ->assertSee('Ardoise Stockage (maintenance)');
    }

    // ── Mission 7.2 🟡 — RG9 : aider le client à classifier (DORA) ─

    public function test_m2_l_historique_donne_la_plus_longue_indisponibilite(): void
    {
        $web = $this->service('CFU-WEB', 'CFU', 1, ['09:18' => 'indisponible', '11:20' => 'degrade', '11:40' => 'indisponible']);

        $this->assertSame(122, Historique::plusLongueIndisponibilite($web, $this->h('12:00')), '09:18 → 11:20, plus long que 11:40 → 12:00.');
        $this->assertSame(42, Historique::plusLongueIndisponibilite($web, $this->h('10:00')), 'Une indisponibilité en cours compte jusqu\'à maintenant.');
        $this->assertTrue(Historique::aEteIndisponible($web, $this->h('12:00')));
        $this->assertFalse(Historique::aEteIndisponible($web, $this->h('09:00')));
        $this->assertSame(0, Historique::plusLongueIndisponibilite($this->service('MUTU', 'SEV', 3), $this->h('12:00')));
    }

    public function test_m2_deux_seuils_sur_un_service_critique_font_un_incident_majeur_probable(): void
    {
        $this->aHeure('10:00');
        $exercice = Exercice::courant();
        $ppay = Contrat::factory()->create(['client' => 'PPAY', 'etats_membres' => 'FR;BE']);
        $psp = $this->service('PPAY-PSP', 'PPAY', 1, ['09:18' => 'indisponible']);
        $cfu = Contrat::factory()->create(['client' => 'CFU']);
        $web = $this->service('CFU-WEB', 'CFU', 1, ['09:18' => 'indisponible']);

        $evalPpay = Dora::evaluer($ppay, [$psp], $exercice, $this->h('10:00'));
        $this->assertSame(['critique' => true, 'seuils' => ['clients', 'geographie']], $evalPpay);
        $this->assertTrue(Dora::majeurProbable($evalPpay));

        $evalCfu = Dora::evaluer($cfu, [$web], $exercice, $this->h('10:00'));
        $this->assertSame(['clients'], $evalCfu['seuils']);
        $this->assertFalse(Dora::majeurProbable($evalCfu), 'Un seul seuil : pas encore majeur.');

        $this->assertSame(['clients', 'interruption'], Dora::evaluer($cfu, [$web], $exercice, $this->h('11:19'))['seuils'],
            'Service critique interrompu plus de 2 heures (09:18 → 11:19).');
    }

    public function test_m2_un_acces_malveillant_suffit_et_sans_service_critique_rien_n_est_majeur(): void
    {
        $this->aHeure('10:15', ['detecte_a' => '09:18', 'malveillance_a' => '10:10', 'reputation_a' => '10:12']);
        $exercice = Exercice::courant();
        $mfa = Contrat::factory()->create(['client' => 'MFA']);
        $ged = $this->service('MFA-GED', 'MFA', 1, ['09:30' => 'degrade']);

        $eval = Dora::evaluer($mfa, [$ged], $exercice, $this->h('10:15'));
        $this->assertSame(['critique' => true, 'seuils' => ['donnees', 'reputation']], $eval);
        $this->assertTrue(Dora::majeurProbable(['critique' => true, 'seuils' => ['donnees']]), 'L\'accès malveillant suffit (art. 8 a).');

        $ext = $this->service('MFA-EXT', 'MFA', 2, ['09:18' => 'indisponible']);
        $this->assertFalse(Dora::majeurProbable(Dora::evaluer($mfa, [$ext], $exercice, $this->h('10:15'))),
            'Aucun service soutenant une fonction critique : pas majeur, quels que soient les seuils.');
    }

    public function test_m2_le_moniteur_4_montre_l_evaluation_de_chaque_client_financier(): void
    {
        $this->aHeure('10:00');
        Contrat::factory()->create(['client' => 'PPAY', 'nom' => 'PilatPay', 'etats_membres' => 'FR;BE']);
        Contrat::factory()->create(['client' => 'VALF', 'nom' => 'Valfurens Conseil', 'dora' => false]);
        $this->service('PPAY-PSP', 'PPAY', 1, ['09:18' => 'indisponible']);

        $this->get(route('moniteur.tiers'))
            ->assertSee('data-client="PPAY" data-majeur="1"', false)
            ->assertSee('data-seuil="geographie"', false)
            ->assertDontSee('data-client="VALF" data-majeur', false);
    }

    // ── Mission 7.3 🟡 — RG10 : registre et concentration ─────────

    public function test_m3_les_sous_traitants_et_la_concentration_sur_un_prestataire(): void
    {
        $san = Composant::factory()->create(['code' => 'SAN-A', 'tiers' => 'Ardoise Stockage']);
        $reseau = Composant::factory()->create(['code' => 'RESEAU-DC1', 'tiers' => 'FibreMont Télécom']);
        $virt = Composant::factory()->create(['code' => 'VIRT-1', 'tiers' => null]);
        $web = Service::factory()->create(['code' => 'CFU-WEB', 'criticite' => 1]);
        $psp = Service::factory()->create(['code' => 'PPAY-PSP', 'criticite' => 1]);
        $mutu = Service::factory()->create(['code' => 'MUTU', 'criticite' => 3]);
        $web->composants()->attach([$san->id => ['mode' => 'bloquant'], $virt->id => ['mode' => 'bloquant'], $reseau->id => ['mode' => 'bloquant']]);
        $psp->composants()->attach([$san->id => ['mode' => 'bloquant'], $reseau->id => ['mode' => 'redondant']]);
        $mutu->composants()->attach([$san->id => ['mode' => 'bloquant']]);

        $this->assertSame(['Ardoise Stockage', 'FibreMont Télécom'], Registre::sousTraitants($web));
        $this->assertSame([
            'Ardoise Stockage' => ['CFU-WEB', 'PPAY-PSP'],
            'FibreMont Télécom' => ['CFU-WEB'],
        ], Registre::concentration([$web, $psp, $mutu])->all(), 'Seules les dépendances bloquantes de services critiques comptent.');
    }

    public function test_m3_le_moniteur_4_montre_le_registre_des_contrats(): void
    {
        $this->aHeure('10:00');
        Contrat::factory()->create(['client' => 'PPAY', 'nom' => 'PilatPay', 'delai_notification_minutes' => 30, 'autorite' => 'ACPR']);
        $psp = Service::factory()->create(['code' => 'PPAY-PSP', 'client' => 'PPAY', 'criticite' => 1]);
        $psp->composants()->attach(Composant::factory()->create(['tiers' => 'Ardoise Stockage']), ['mode' => 'bloquant']);

        $this->get(route('moniteur.tiers'))
            ->assertSee('data-contrat="PPAY" data-dora="1"', false)
            ->assertSee('30 min')
            ->assertSee('ACPR')
            ->assertSee('data-concentration="Ardoise Stockage" data-nombre="1"', false);
    }

    // ── Mission 7.4 🟢 — Les indicateurs ──────────────────────────

    public function test_m4_les_indicateurs_reutilisent_les_regles(): void
    {
        $this->aHeure('10:00', ['detecte_a' => '09:18', 'important_depuis' => '09:50']);
        Service::factory()->indisponibleDepuis('09:18')->create(['gtr_minutes' => 30]);     // hors GTR
        Service::factory()->indisponibleDepuis('09:18')->create(['gtr_minutes' => 240]);
        Service::factory()->degradeDepuis('09:30')->create();
        Notification::factory()->create(['delai_minutes' => 30]);                         // 09:48 : en retard
        Notification::factory()->envoyee('09:40')->create();
        Notification::factory()->anssi()->create();

        $this->getJson('/api/indicateurs')
            ->assertOk()
            ->assertJsonPath('services_indisponibles', 2)
            ->assertJsonPath('services_hors_gtr', 1)
            ->assertJsonPath('notifications_envoyees', 1)
            ->assertJsonPath('notifications_en_retard', 1);

        $this->get(route('moniteur.tiers'))->assertSee('data-indicateur="notifications_en_retard"', false);
    }
}
