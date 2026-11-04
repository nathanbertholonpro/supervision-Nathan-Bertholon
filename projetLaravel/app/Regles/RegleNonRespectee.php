<?php

namespace App\Regles;

/**
 * FOURNI — Levée par une règle de gestion qui refuse une opération (déjà envoyée, prérequis manquant…).
 * Le contrôleur la traduit en réponse HTTP 409.
 */
class RegleNonRespectee extends \RuntimeException {}
