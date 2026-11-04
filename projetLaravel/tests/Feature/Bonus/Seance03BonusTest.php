<?php

namespace Tests\Feature\Bonus;

use App\Models\Journal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 3.5 🔴 — Chercher un mot dans le journal : /moniteur/2?q=ardoise */
#[Group('bonus')]
class Seance03BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s3_m5_la_recherche_trouve_un_mot_sans_tenir_compte_des_majuscules(): void
    {
        $this->connecter();
        Journal::factory()->create(['texte' => 'L\'éditeur ARDOISE Stockage confirme la compromission.']);
        Journal::factory()->create(['texte' => 'Le DC2 est démarré.']);

        $this->get(route('moniteur.journal', ['q' => 'ardoise']))
            ->assertSee('L\'éditeur ARDOISE Stockage confirme la compromission.')
            ->assertDontSee('Le DC2 est démarré.');
    }
}
