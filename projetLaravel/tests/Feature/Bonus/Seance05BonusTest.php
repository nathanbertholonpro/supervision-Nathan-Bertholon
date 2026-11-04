<?php

namespace Tests\Feature\Bonus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 5.5 🔴 — Le lanceur du poste place les quatre fenêtres : /poste */
#[Group('bonus')]
class Seance05BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s5_m5_le_lanceur_lit_le_poste_et_place_les_fenetres(): void
    {
        $this->connecter();

        $this->get('/poste')
            ->assertOk()
            ->assertSee('/api/poste', false)
            ->assertSee('getScreenDetails', false)
            ->assertSee('window.open', false);
    }
}
