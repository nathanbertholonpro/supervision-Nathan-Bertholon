<?php

namespace Tests;

use App\Models\Exercice;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    public const JOUR = '2027-03-09';

    public const MOT_DE_PASSE = 'Supervision2027!';

    /**
     * Fige l'horloge de l'exercice à HH:MM (comme le mode recette).
     * $reperes : heures des repères posés par le scénario, par exemple
     *   ['detecte_a' => '09:18', 'important_depuis' => '09:50', 'malveillance_a' => '10:10'].
     * Par défaut l'incident est détecté à 09:18 et rien d'autre n'est posé.
     */
    protected function aHeure(string $hhmm, array $reperes = ['detecte_a' => '09:18'], ?int $pertes = null): CarbonImmutable
    {
        Exercice::query()->delete();
        Exercice::create(array_merge([
            'nom' => 'Exercice de test',
            'debut' => self::JOUR.' 09:00',
            'fin' => self::JOUR.' 13:00',
            'acceleration' => 20,
            'fige_a' => self::JOUR.' '.$hhmm,
            'pertes_estimees' => $pertes,
        ], array_map(fn ($h) => $h === null ? null : self::JOUR.' '.$h, $reperes)));

        return CarbonImmutable::parse(self::JOUR.' '.$hhmm);
    }

    /** « 2027-03-09 09:15 » à partir de « 09:15 ». */
    protected function h(string $hhmm): CarbonImmutable
    {
        return CarbonImmutable::parse(self::JOUR.' '.$hhmm);
    }

    /**
     * Un membre du centre de supervision. Avant la séance 6, les colonnes « role » et « clients »
     * n'existent pas encore : on crée simplement un utilisateur. Les tests des séances 1 à 5
     * restent ainsi valables avant ET après l'arrivée de la connexion.
     */
    protected function membre(string $nom = 'Hélène Vartan', string $role = 'directeur', ?string $clients = null): User
    {
        $user = User::factory()->create(['name' => $nom, 'password' => self::MOT_DE_PASSE]);

        if (Schema::hasColumn('users', 'role')) {
            $user->forceFill(['role' => $role])->save();
        }
        if (Schema::hasColumn('users', 'clients')) {
            $user->forceFill(['clients' => $clients])->save();
        }

        return $user->refresh();
    }

    /** Le premier message d'erreur de validation d'un champ, après une requête. */
    protected function messageErreur(string $champ): ?string
    {
        $erreurs = session('errors');   // les sessions sont enregistrées en JSON : un tableau, pas un objet

        return is_array($erreurs)
            ? ($erreurs['default']['messages'][$champ][0] ?? null)
            : $erreurs?->first($champ);
    }

    /** Se connecter comme la directrice des opérations (sans effet avant la séance 6). */
    protected function connecter(?User $user = null): User
    {
        $user ??= $this->membre();
        $this->actingAs($user);

        return $user;
    }
}
