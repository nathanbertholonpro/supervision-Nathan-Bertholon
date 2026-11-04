<?php

namespace Tests\Feature\Socle;

use App\Exercice\Horloge;
use App\Exercice\Moteur;
use App\Models\Composant;
use App\Models\Contrat;
use App\Models\Exercice;
use App\Models\Inject;
use App\Models\Journal;
use App\Models\Notification;
use App\Models\Service;
use App\Support\Duree;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SOCLE — ce qui marche dès le premier jour et doit le rester.
 *
 *   php artisan test --group=socle
 */
#[Group('socle')]
class ExerciceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connecter();
    }

    public function test_l_accueil_repond(): void
    {
        $this->get('/')->assertOk()->assertSee('Centre de supervision');
    }

    public function test_le_moniteur_1_liste_les_services(): void
    {
        Service::factory()->create(['code' => 'CFU-WEB', 'nom' => 'Banque en ligne (portail des clients)']);

        $this->get('/moniteur/1')->assertOk()->assertSee('Banque en ligne (portail des clients)');
    }

    public function test_l_horloge_figee_donne_l_heure_de_recette(): void
    {
        $this->aHeure('10:15');

        $this->assertEquals($this->h('10:15'), Horloge::maintenant());
        $this->assertSame('T+01:15', Horloge::libelleT());
    }

    public function test_l_horloge_avance_vingt_fois_plus_vite(): void
    {
        $this->travelTo(CarbonImmutable::parse('2027-01-10 14:00:00'));
        Exercice::create([
            'nom' => 'Essai', 'debut' => self::JOUR.' 09:00', 'fin' => self::JOUR.' 13:00',
            'demarre_a' => '2027-01-10 13:59:00', 'acceleration' => 20,
        ]);

        // une minute réelle = vingt minutes d'exercice
        $this->assertEquals($this->h('09:20'), Horloge::maintenant());
    }

    public function test_le_moteur_joue_un_inject_dont_l_heure_est_passee(): void
    {
        $this->aHeure('09:30', []);
        Composant::factory()->create(['code' => 'SAN-A']);
        Service::factory()->create(['code' => 'CFU-WEB']);
        Inject::create([
            'heure' => self::JOUR.' 09:18', 'categorie' => 'fait', 'titre' => 'Les machines virtuelles s\'arrêtent.',
            'donnees' => ['composants' => ['SAN-A' => 'hors_service'], 'services' => ['CFU-WEB' => 'indisponible'], 'detection' => true],
        ]);

        Moteur::rattraper();

        $this->assertSame('hors_service', Composant::where('code', 'SAN-A')->value('etat'));
        $web = Service::where('code', 'CFU-WEB')->first();
        $this->assertSame('indisponible', $web->etat);
        $this->assertEquals($this->h('09:18'), $web->indisponible_depuis);
        $this->assertEquals($this->h('09:18'), Exercice::courant()->detecte_a);
        $this->assertDatabaseHas('journal', ['texte' => 'Les machines virtuelles s\'arrêtent.', 'source' => 'scenario']);
    }

    public function test_un_inject_n_est_joue_qu_une_fois_et_pas_avant_son_heure(): void
    {
        $this->aHeure('09:30');
        Inject::create(['heure' => self::JOUR.' 09:10', 'categorie' => 'fait', 'titre' => 'Déjà arrivé']);
        Inject::create(['heure' => self::JOUR.' 10:00', 'categorie' => 'fait', 'titre' => 'Pas encore']);

        Moteur::rattraper();
        Moteur::rattraper();
        $this->get('/');

        $this->assertSame(1, Journal::where('texte', 'Déjà arrivé')->count());
        $this->assertSame(0, Journal::where('texte', 'Pas encore')->count());
    }

    public function test_le_referentiel_et_le_scenario_sont_charges(): void
    {
        $this->seed();

        $this->assertSame(9, Service::count());
        $this->assertSame(8, Composant::count());
        $this->assertSame(5, Contrat::count());
        $this->assertSame(8, Notification::count());
        $this->assertSame(18, Inject::count());
        $this->assertSame('ANSSI-ALERTE', Notification::where('code', 'ANSSI-NOTIF')->first()->dependDe->code);
        $this->assertSame(['FR', 'BE'], Contrat::where('client', 'PPAY')->first()->etatsMembres());
    }

    public function test_les_durees_s_ecrivent_comme_on_les_lit(): void
    {
        $this->assertSame('45 min', Duree::format(45));
        $this->assertSame('1 h', Duree::format(60));
        $this->assertSame('2 h 04', Duree::format(124));
        $this->assertSame('3 j', Duree::format(4320));
        $this->assertSame('−12 min', Duree::format(-12));
        $this->assertSame('—', Duree::format(null));
    }

    public function test_la_commande_de_demarrage_fige_l_horloge_en_mode_recette(): void
    {
        $this->artisan('exercice:demarrer', ['--figer' => '10:00', '--sans-remise-a-zero' => true])->assertSuccessful();
        $this->assertEquals($this->h('10:00'), Horloge::maintenant());

        $this->artisan('exercice:demarrer', ['--figer' => '25:00', '--sans-remise-a-zero' => true])->assertFailed();
    }
}
