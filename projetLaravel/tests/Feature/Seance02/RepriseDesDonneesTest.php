<?php

namespace Tests\Feature\Seance02;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 2 — Mission 2.2 🟢 : la migration reprend les données d'une base EN SERVICE.
 *
 * Le test construit la base telle qu'elle était avant votre travail (les migrations du prestataire),
 * y range des dépendances en texte, puis lance VOS migrations et vérifie que rien n'est perdu.
 */
#[Group('seance-02')]
class RepriseDesDonneesTest extends TestCase
{
    /** Les migrations laissées par le prestataire. */
    private const MIGRATIONS_DU_PRESTATAIRE = [
        '0001_01_01_000000_create_users_table.php',
        '0001_01_01_000001_create_cache_table.php',
        '0001_01_01_000002_create_jobs_table.php',
        '2027_01_10_000100_create_exercices_table.php',
        '2027_01_10_000200_create_composants_table.php',
        '2027_01_10_000300_create_services_table.php',
        '2027_01_10_000400_create_injects_table.php',
        '2027_01_10_000500_create_journal_table.php',
        '2027_01_10_000600_create_notifications_table.php',
        '2027_01_10_000700_create_contrats_table.php',
    ];

    protected function tearDown(): void
    {
        $this->artisan('migrate:fresh');   // repartir d'une base propre pour les autres tests
        parent::tearDown();
    }

    public function test_m2_la_migration_transforme_le_texte_en_lignes_sans_rien_perdre(): void
    {
        $this->artisan('migrate:fresh', [
            '--path' => array_map(fn ($f) => 'database/migrations/'.$f, self::MIGRATIONS_DU_PRESTATAIRE),
        ]);

        $maintenant = now();
        foreach (['VIRT-1', 'SAN-A', 'RESEAU-DC1', 'SAUV-DC2'] as $code) {
            DB::table('composants')->insert(['code' => $code, 'nom' => $code, 'created_at' => $maintenant, 'updated_at' => $maintenant]);
        }
        $service = fn (string $code, string $texte) => DB::table('services')->insert([
            'code' => $code, 'client' => 'CLI', 'nom' => $code, 'type' => 'infogerance', 'criticite' => 1, 'gtr_minutes' => 60,
            'composants' => $texte, 'created_at' => $maintenant, 'updated_at' => $maintenant,
        ]);
        $service('WEB', 'VIRT-1; SAN-A');           // une espace saisie par erreur
        $service('GED', 'SAN-A;SAUV-DC2;BANDE');    // BANDE n'existe pas parmi les composants
        $service('SAUV', '');                       // aucune dépendance notée

        $this->artisan('migrate')->assertSuccessful();   // vos migrations

        $liens = DB::table('service_composant')
            ->join('services', 'services.id', '=', 'service_composant.service_id')
            ->join('composants', 'composants.id', '=', 'service_composant.composant_id')
            ->orderBy('services.code')->orderBy('composants.code')
            ->get(['services.code as service', 'composants.code as composant', 'mode'])
            ->map(fn ($l) => "{$l->service}-{$l->composant}-{$l->mode}")
            ->all();

        $this->assertSame([
            'GED-SAN-A-bloquant', 'GED-SAUV-DC2-bloquant',
            'WEB-SAN-A-bloquant', 'WEB-VIRT-1-bloquant',
        ], $liens, 'Chaque dépendance notée en texte doit devenir une ligne, bloquante par prudence.');
        $this->assertFalse(Schema::hasColumn('services', 'composants'));
    }
}
