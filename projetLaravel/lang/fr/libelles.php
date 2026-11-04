<?php

/*
| FOURNI — Les valeurs enregistrées en base (sans accent) telles qu'on les lit à l'écran.
| Dans une vue : {{ __('libelles.etat.degrade') }} affiche « dégradé ».
*/

return [
    'etat' => [
        'normal' => 'normal', 'degrade' => 'dégradé', 'indisponible' => 'indisponible',
        'operationnel' => 'opérationnel', 'hors_service' => 'hors service', 'secours' => 'secours activé',
    ],
    'etats_pluriel' => ['indisponible' => 'indisponible(s)', 'degrade' => 'dégradé(s)', 'normal' => 'normal(aux)'],
    'niveau' => ['ok' => 'dans la GTR', 'risque' => 'à risque', 'hors_gtr' => 'hors GTR'],
    'statut' => [
        'envoyee' => 'envoyée', 'en_retard' => 'en retard', 'bloquee' => 'bloquée', 'en_attente' => 'délai non commencé',
        'proche' => 'échéance proche', 'a_envoyer' => 'à envoyer',
    ],
    'categorie' => ['fait' => 'fait', 'decision' => 'décision', 'action' => 'action', 'notification' => 'notification'],
    'type' => ['infogerance' => 'infogérance', 'colocation' => 'centre de données (colocation)'],
    'critere' => [
        'indisponibilite_totale' => 'Service totalement indisponible (plus de 30 min en infogérance ; dès la coupure en colocation)',
        'disponibilite_limitee' => 'Disponibilité limitée plus d\'une heure',
        'malveillance' => 'Action malveillante suspectée',
        'pertes' => 'Pertes directes au-delà de 500 000 € (ou 5 % du chiffre d\'affaires)',
    ],
    'seuil' => [
        'clients' => 'Clients touchés (service coupé pour tous ses utilisateurs)',
        'duree' => 'Incident de plus de 24 h',
        'interruption' => 'Service critique interrompu plus de 2 h',
        'geographie' => 'Au moins deux États membres',
        'donnees' => 'Accès malveillant pouvant entraîner des pertes de données',
        'reputation' => 'Atteinte à la réputation (médias)',
    ],
    'role' => [
        'directeur' => 'direction des opérations', 'rssi' => 'RSSI', 'relation_client' => 'relation client',
        'analyste' => 'analyste NOC-SOC', 'auditeur' => 'auditeur',
    ],
];
