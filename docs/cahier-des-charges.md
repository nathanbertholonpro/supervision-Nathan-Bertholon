# 📐 Cahier des charges — Centre de supervision de Sevranne Datacenter

> Organisation, personnes, données et scénario **fictifs**. Les textes cités (directive NIS 2, règlement DORA et
> leurs actes d'application, RGPD) sont réels : leur mise en œuvre dans l'exercice est expliquée dans
> [le droit de la supervision : NIS 2 et DORA](cadre-juridique.md).

## 1. L'organisation

**Sevranne Datacenter** — un infogéreur-hébergeur de la Loire : deux centres de données (le **DC1** à
Andrézieux-Bouthéon, le **DC2** de secours à Vénissieux), 180 salariés, 32 M€ de chiffre d'affaires, 140 clients.
Il héberge les serveurs de ses clients (**colocation**) et exploite pour eux des services complets
(**infogérance**). Au sens de NIS 2, c'est une **entité importante** : moyenne entreprise des secteurs
« infrastructure numérique » et « gestion des services TIC ».

Parmi ses clients, trois **entités financières** soumises à **DORA** :

| Client | Entité | Autorité | Services chez Sevranne |
|---|---|---|---|
| Crédit Furanais (`CFU`) | établissement de crédit | ACPR | banque en ligne, sauvegarde externalisée |
| PilatPay (`PPAY`) | établissement de paiement (France et Belgique) | ACPR | passerelle de paiement des commerçants |
| Mutuelle Forézienne d'Assurance (`MFA`) | entreprise d'assurance | ACPR | GED des sinistres, extranet des adhérents |

et des clients non financiers : la **Clinique Saint-Lys** (données de santé, Sevranne est certifié hébergeur de
données de santé), **Valfurens Conseil** (cabinet d'expertise comptable), et 62 sites et 38 messageries de PME.

## 2. Le vocabulaire

| Terme | Définition retenue |
|---|---|
| **Supervision** | la surveillance en continu des services et des composants : savoir avant le client que quelque chose ne va pas |
| **NOC / SOC** | centre d'exploitation réseau (disponibilité) / centre des opérations de sécurité (attaques) ; ici, un même poste |
| **Service** | ce que Sevranne rend à un client, avec un contrat : la banque en ligne du Crédit Furanais, par exemple |
| **Composant** | un élément de l'infrastructure dont dépendent les services : baie de stockage, cluster, réseau… Certains sont fournis ou maintenus par un **tiers** (prestataire) |
| **GTR** — garantie de temps de rétablissement | durée maximale d'interruption promise au client dans le contrat ; au-delà, des pénalités |
| **Criticité** | 1 : le service soutient une **fonction critique ou importante** du client (au sens de DORA) ; 2 : important ; 3 : standard |
| **Incident important** (NIS 2) | un incident qui atteint au moins un critère du règlement d'exécution (UE) 2024/2690 : il doit être notifié à l'ANSSI |
| **Incident majeur** (DORA) | un incident que l'**entité financière** classe comme majeur (règlement délégué (UE) 2024/1772) : elle le notifie à son autorité |
| **Registre d'informations** (DORA) | la liste que tient chaque entité financière de ses contrats avec des prestataires TIC |
| **Risque de concentration** | trop de services critiques qui dépendent du même prestataire |
| **Journal d'incident** | le journal horodaté et signé de tout ce qui se passe et se décide pendant l'incident |
| **Inject** | un événement du scénario d'exercice, « injecté » à une heure prévue |
| **RETEX** — retour d'expérience | après l'exercice : ce qui a tenu, ce qui a manqué, ce que l'on change |

## 3. Règles de gestion

| Règle | Énoncé | Séance |
|---|---|---|
| **RG1** | **Indisponibilité** d'un service = heure de l'exercice − début de l'indisponibilité. **Consommation de la GTR** = indisponibilité ÷ GTR. Un service est **à risque** à partir de 75 % de sa GTR, **hors GTR** au-delà de 100 %. Un service qui fonctionne, même dégradé, ne consomme pas sa GTR. | 1 |
| **RG2** | **Ordre de rétablissement** : d'abord les services **indisponibles**, puis les **dégradés**, puis les **normaux** ; dans chaque groupe, la criticité (1 avant 2 avant 3) ; à criticité égale, la **marge** la plus faible avant la fin de la GTR ; à marge égale, le code. | 1 |
| **RG3** | Un service dépend de composants, chacun **bloquant** (sans lui, le service s'arrête) ou **redondant** (le service se dégrade). | 2 |
| **RG4** | **Journal d'incident** : chaque entrée est horodatée à l'heure de l'exercice, signée par son auteur, classée (*fait, décision, action, notification*). Une entrée n'est **jamais modifiée ni supprimée** ; une erreur se corrige par une nouvelle entrée qui cite celle qu'elle corrige. | 3 |
| **RG5** | **Incident important** (NIS 2, règlement 2024/2690) si au moins un critère est atteint : un service d'infogérance totalement indisponible **plus de 30 minutes**, ou un service de colocation totalement indisponible (sans durée minimale) ; un service dégradé **plus d'une heure** ; une **action malveillante** suspectée ; des **pertes directes** supérieures à 500 000 € ou à 5 % du chiffre d'affaires (le plus bas des deux). On évalue la situation à l'instant. | 4 |
| **RG6** | **Notifications** : à l'ANSSI, l'**alerte précoce** sous 24 h et la **notification** sous 72 h après la qualification d'incident important, le **rapport final** un mois après la notification ; à chaque client, dans le **délai du contrat** après la détection. Une notification ne part qu'**après** celle dont elle dépend, et **une seule fois**. Elle est **en retard** au-delà de son échéance (même bloquée), **proche** quand il reste un quart du délai ou moins. Chaque envoi s'écrit au journal. | 4 |
| **RG7** | Chaque vue s'ouvre sur le moniteur prévu par `config/poste.json` ; s'il manque un moniteur, les vues s'ouvrent en fenêtres sur les écrans restants. Sélectionner un service sur le moniteur 1 filtre les notifications du moniteur 3 sur son client (et l'ANSSI). | 5 |
| **RG8** | **Rôles** : la *direction* fait tout, et elle seule envoie le **rapport final** à l'ANSSI ; le *RSSI* envoie l'alerte précoce et la notification ; un chargé de *relation client* ne notifie que les clients de **son portefeuille** ; un *analyste* écrit au journal ; un *auditeur* lit tout et n'écrit rien. | 6 |
| **RG9** | **Aide à la classification DORA** : pour un client financier, Sevranne établit les éléments qu'il connaît (règlement délégué 2024/1772) — services critiques touchés ; seuils *clients* (service coupé pour tous ses utilisateurs), *durée* (plus de 24 h), *interruption* (service critique coupé plus de 2 h), *géographie* (au moins deux États membres), *données* (accès malveillant), *réputation* (médias). **Majeur probable** : services critiques touchés et (accès malveillant, ou au moins deux autres seuils). Le client décide. | 7 |
| **RG10** | **Registre et concentration** : pour chaque contrat, les services, leur criticité, le délai de notification, les sous-traitants (prestataires des composants), la localisation des données ; pour chaque prestataire, les services de criticité 1 qui en dépendent de façon bloquante. | 7 |

## 4. Les services supervisés

| Code | Client | Service | Type | Criticité | GTR | Dépend de |
|---|---|---|---|---|---|---|
| `CFU-WEB` | CFU | Banque en ligne | infogérance | 1 | 1 h | VIRT-1, SAN-A, RESEAU-DC1 |
| `CFU-SAUV` | CFU | Sauvegarde externalisée | infogérance | 1 | 4 h | SAUV-DC2 |
| `PPAY-PSP` | PPAY | Passerelle de paiement | infogérance | 1 | 30 min | VIRT-1, SAN-A, RESEAU-DC1 |
| `MFA-GED` | MFA | GED des sinistres | infogérance | 1 | 2 h | VIRT-1, SAN-A, SAUV-DC2 (redondant) |
| `MFA-EXT` | MFA | Extranet des adhérents | infogérance | 2 | 4 h | VIRT-1, SAN-A |
| `CSL-DPI` | CSL | Serveurs du dossier patient | colocation | 1 | 1 h | RESEAU-DC1, ENERGIE-DC1 |
| `VALF-PAIE` | VALF | Serveur de paie | infogérance | 2 | 4 h | VIRT-1, SAN-A |
| `MESS` | SEV | Messagerie hébergée (38 PME) | infogérance | 2 | 4 h | VIRT-1, SAN-A |
| `MUTU` | SEV | Hébergement mutualisé (62 sites) | infogérance | 3 | 8 h | VIRT-1, SAN-A, RESEAU-DC1 (redondant) |

## 5. Les composants et leurs prestataires

| Code | Composant | Prestataire |
|---|---|---|
| `SAN-A` | baie de stockage du DC1 | Ardoise Stockage (maintenance et assistance à distance) |
| `VIRT-1` / `VIRT-2` | clusters de virtualisation du DC1 / de secours du DC2 | Sevranne |
| `RESEAU-DC1` | réseau et transit internet du DC1 | FibreMont Télécom |
| `ENERGIE-DC1` | alimentation électrique et groupes | Watteo Énergie |
| `SAUV-DC2` | sauvegardes répliquées au DC2 | Sevranne |
| `IAM`, `SUPERV` | annuaire et bastion d'administration ; supervision | Sevranne |

## 6. Les notifications prévues

| Code | Destinataire | Règle | Échéance | Après |
|---|---|---|---|---|
| `ANSSI-ALERTE` | ANSSI | NIS 2, art. 23.4 a | qualification + 24 h | — |
| `ANSSI-NOTIF` | ANSSI | NIS 2, art. 23.4 b | qualification + 72 h | ANSSI-ALERTE |
| `ANSSI-FINAL` | ANSSI | NIS 2, art. 23.4 d | un mois après la notification | ANSSI-NOTIF |
| `CLIENT-PPAY` | PilatPay | contrat (DORA, art. 30) | détection + 30 min | — |
| `CLIENT-CFU` | Crédit Furanais | contrat (DORA, art. 30) | détection + 1 h | — |
| `CLIENT-MFA` | Mutuelle Forézienne | contrat (DORA, art. 30) | détection + 2 h | — |
| `CLIENT-CSL` | Clinique Saint-Lys | contrat, NIS 2 art. 23.1 | détection + 2 h | — |
| `CLIENT-VALF` | Valfurens Conseil | contrat, NIS 2 art. 23.1 | détection + 4 h | — |

Le **texte** de ces notifications n'est pas dans l'application : il se rédige (séances 7 et 8, voir
[les documents](documents-a-produire.md)).

## 7. Les quatre moniteurs

| Moniteur | Adresse | Question à laquelle il répond |
|---|---|---|
| 1 · Services et disponibilité | `/moniteur/1` | Quels services sont coupés, pour quels clients, et combien de temps reste-t-il avant la fin de la GTR ? |
| 2 · Incident et qualification | `/moniteur/2` | Que s'est-il passé, et l'incident est-il « important » au sens de NIS 2 ? |
| 3 · Horloges de notification | `/moniteur/3` | Qui prévenir — ANSSI, clients — et avant quand ? |
| 4 · Tiers, clients et registre | `/moniteur/4` | De quels prestataires dépend-on, et quels clients financiers sont touchés (DORA) ? |

Disposition (`config/poste.json`) : 1 en haut à gauche, 2 en haut à droite, 3 en bas à gauche, 4 en bas à droite.
Tous les moniteurs se mettent à jour sans rechargement complet, dès que l'état change.

## 8. Le scénario de l'exercice

**Mardi 9 mars 2027, 9 h – 13 h.** Pendant la nuit, l'équipe d'assistance de l'éditeur de la baie de stockage a
installé à distance un nouveau micrologiciel. À 9 h 18, le cluster de virtualisation du DC1 perd ses volumes : sept
services tombent, dont la passerelle de paiement de PilatPay et la banque en ligne du Crédit Furanais. À 10 h 10, le
SOC découvre que le compte d'assistance de l'éditeur sert à se connecter depuis une adresse inconnue : l'outil
d'assistance à distance de l'éditeur a été compromis — une attaque par la **chaîne d'approvisionnement**.
Les *injects* sont dans `database/data/scenario-exercice.json` ; l'application rejoue l'exercice 20 fois plus vite.

## 9. Sécurité

- Seules les personnes connectées accèdent aux moniteurs et à l'API, sauf la page de connexion et la sonde de santé.
- Les mots de passe sont stockés sous forme d'empreinte ; le message d'échec ne dit pas si le compte existe ;
  les tentatives répétées sont freinées.
- Chaque entrée du journal et chaque notification envoyée garde son auteur et son heure.
- Un outil externe qui envoie des données à l'application doit **prouver** qui il est.
