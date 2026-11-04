<?php

namespace App\Http\Controllers;

use App\Exercice\Horloge;
use App\Models\Journal;
use App\Models\Service;
use Illuminate\Http\Request;

/**
 * Point d'entrée de l'outil de supervision externe : il pousse l'état d'un service.
 * Ajouté par le prestataire pour brancher sa sonde pendant ses essais.
 *
 *   POST /api/supervision/webhook   {"service": "CFU-WEB", "etat": "normal"}
 */
class WebhookController extends Controller
{
    public function recevoir(Request $request)
    {
        $donnees = $request->validate([
            'service' => ['required', 'string', 'exists:services,code'],
            'etat' => ['required', 'in:'.implode(',', Service::ETATS)],
        ]);

        $heure = Horloge::maintenant();
        $service = Service::where('code', $donnees['service'])->firstOrFail();
        $service->changerEtat($donnees['etat'], $heure);

        Journal::create([
            'heure' => $heure,
            'categorie' => 'fait',
            'texte' => "Supervision externe : {$service->code} est {$donnees['etat']}.",
            'auteur' => 'Sonde de supervision',
            'source' => 'sonde',
        ]);

        return response()->json(['ok' => true, 'service' => $service->code, 'etat' => $service->etat]);
    }
}
