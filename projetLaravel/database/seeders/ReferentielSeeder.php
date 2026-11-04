<?php

namespace Database\Seeders;

use App\Models\Composant;
use App\Models\Contrat;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * FOURNI — Sevranne Datacenter : composants, services, contrats et notifications prévues.
 * Source : database/data/referentiel.json
 */
class ReferentielSeeder extends Seeder
{
    public function run(): void
    {
        $ref = json_decode(file_get_contents(database_path('data/referentiel.json')), true);

        foreach ($ref['composants'] as $c) {
            Composant::create(['code' => $c['code'], 'nom' => $c['nom'], 'tiers' => $c['tiers'], 'etat' => 'operationnel']);
        }

        foreach ($ref['services'] as $s) {
            Service::create([
                'code' => $s['code'],
                'client' => $s['client'],
                'nom' => $s['nom'],
                'type' => $s['type'],
                'criticite' => $s['criticite'],
                'gtr_minutes' => $s['gtr_minutes'],
                'utilisateurs' => $s['utilisateurs'],
                // Le prestataire range les dépendances dans une colonne texte : « VIRT-1;SAN-A »
                'composants' => implode(';', array_column($s['dependances'], 'composant')),
                'etat' => 'normal',
            ]);
        }

        foreach ($ref['contrats'] as $c) {
            Contrat::create(array_merge($c, ['etats_membres' => implode(';', $c['etats_membres'])]));
        }

        foreach ($ref['notifications'] as $n) {
            Notification::create([
                'code' => $n['code'],
                'destinataire' => $n['destinataire'],
                'objet' => $n['objet'],
                'regle' => $n['regle'],
                'delai_minutes' => $n['delai_minutes'],
                'depend_de_id' => $n['depend_de'] ? Notification::where('code', $n['depend_de'])->value('id') : null,
                'client' => $n['client'],
            ]);
        }
    }
}
