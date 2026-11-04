<?php

namespace Tests\Feature\Bonus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/** Mission 6.5 🔴 — Freiner la force brute : au-delà de 5 échecs, une minute d'attente */
#[Group('bonus')]
class Seance06BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s6_m5_apres_cinq_echecs_meme_le_bon_mot_de_passe_doit_attendre(): void
    {
        $helene = $this->membre();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/connexion', ['email' => $helene->email, 'password' => 'mauvais']);
        }
        $this->post('/connexion', ['email' => $helene->email, 'password' => self::MOT_DE_PASSE]);

        $this->assertGuest();
        $this->assertStringContainsString('Trop de tentatives', $this->messageErreur('email') ?? '');
    }
}
