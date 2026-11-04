<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Routes d'API (préfixe /api, sans session ni jeton CSRF).
// La sonde de supervision externe pousse l'état des services.
Route::post('/supervision/webhook', [WebhookController::class, 'recevoir'])->name('api.webhook');
