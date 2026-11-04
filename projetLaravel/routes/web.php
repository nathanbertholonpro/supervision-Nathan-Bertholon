<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web du centre de supervision
|--------------------------------------------------------------------------
| Chaque ligne associe une URL (et un verbe HTTP) à une action de contrôleur.
| ->name('…') donne un nom à la route : dans les vues on écrit route('moniteur.services').
*/

Route::get('/', [AccueilController::class, 'index'])->name('accueil');

// Moniteur 1 — les services et leur disponibilité
Route::get('/moniteur/1', [ServicesController::class, 'index'])->name('moniteur.services');
