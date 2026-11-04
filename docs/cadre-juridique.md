# ⚖️ Le droit de la supervision : NIS 2 et DORA

> 📖 **Les § 1 et 2 avant la séance 1** ; les autres au fil des séances, quand l'encart ⚖️ du guide y renvoie ;
> **toute la fiche avant l'interrogation n° 2** (15 min). Auto-évaluation à la fin.
> En lien avec le cours de **CEJM** (droit européen, contrat, responsabilité) et avec le bloc 3 (cybersécurité).

Sevranne Datacenter, ses clients et l'incident sont fictifs ; **les textes cités sont réels**. Cette fiche dit ce
qu'ils imposent dans la situation de l'exercice. Ce n'est pas un conseil juridique : dans une vraie entreprise, la
direction décide avec son RSSI, son juriste et ses conseils.

---

## 1. Deux textes européens, deux logiques

| | **NIS 2** | **DORA** |
|---|---|---|
| Texte | directive (UE) 2022/2555 | règlement (UE) 2022/2554 |
| Nature | une **directive** : chaque État la **transpose** dans sa loi | un **règlement** : il s'applique **directement**, partout dans l'Union |
| Depuis | transposition due le 17 octobre 2024 ; en France, la loi « Résilience » qui la transpose **n'est pas promulguée au 31 octobre 2026** | applicable depuis le **17 janvier 2025** |
| Qui | les entités **essentielles** et **importantes** de 18 secteurs (énergie, transports, santé, infrastructure numérique, services TIC gérés…) | les **entités financières** : banques, établissements de paiement, assureurs… — et, par contrat, leurs **prestataires TIC** |
| Autorité en France | **ANSSI** | **ACPR** pour les banques, établissements de paiement et assureurs |
| Idée | relever le niveau de cybersécurité des organisations dont la société dépend | garantir la **résilience opérationnelle numérique** du secteur financier |

Pour une entité financière, **DORA prime** : c'est un texte spécial pour la finance. NIS 2 ne s'applique donc pas à
la banque elle-même… mais il s'applique à **Sevranne**, son hébergeur. **Le même incident déclenche les deux
régimes** : Sevranne notifie l'ANSSI (NIS 2) ; chacun de ses clients financiers évalue l'incident et notifie l'ACPR
(DORA), à partir des informations que Sevranne lui donne. C'est tout le sujet de cette situation.

## 2. Sevranne au regard de NIS 2 : une entité importante

- **Secteurs** : « infrastructure numérique » (fournisseurs de services de centres de données, d'informatique en
  nuage) et « gestion des services TIC (interentreprises) » (fournisseurs de services gérés) — annexe I de la
  directive, secteurs hautement critiques.
- **Taille** : 180 salariés, 32 M€ de chiffre d'affaires : une **moyenne entreprise**. Dans un secteur de l'annexe I,
  une grande entreprise serait **essentielle** ; une moyenne entreprise est **importante** (art. 3). Différence
  pratique : une entité essentielle est contrôlée a priori, une entité importante a posteriori (art. 32 et 33).
- **Obligations** :
  - **gouvernance** (art. 20) : la **direction** approuve les mesures de cybersécurité, en supervise la mise en
    œuvre, se forme, et peut être tenue **responsable** des manquements ;
  - **mesures** (art. 21) : au moins dix, dont la gestion des incidents, la **continuité** (sauvegardes, reprise,
    gestion de crise) et la **sécurité de la chaîne d'approvisionnement** — les relations avec les fournisseurs ;
  - **notification des incidents importants** (art. 23) : voir § 3.
- **Sanctions** (art. 34) : pour une entité importante, une amende d'au moins **7 M€ ou 1,4 %** du chiffre
  d'affaires mondial (le plus élevé) peut être prévue ; 10 M€ ou 2 % pour une entité essentielle.

> **En France, aujourd'hui** : la loi de transposition a été adoptée par le Sénat en mars 2025 ; à l'Assemblée
> nationale, son examen en séance, prévu les 7 et 9 octobre 2026, a été retiré de l'ordre du jour sans nouvelle date.
> Elle n'est donc pas promulguée au 31 octobre 2026. L'exercice applique les règles de la directive et de son règlement d'exécution,
> telles qu'elles s'imposeront. Suivre la publication de la loi et des décrets est un bon sujet de **veille**.

## 3. Quand un incident est-il « important », et que faut-il notifier ? (NIS 2)

**Critères** (règlement d'exécution (UE) 2024/2690, art. 3, 8 et 10) — il suffit d'**un** :

| Critère | Texte | Dans l'application (RG5) |
|---|---|---|
| un service géré **totalement indisponible plus de 30 minutes** | art. 10 a | `indisponibilite_totale` |
| un service de centre de données **totalement indisponible** (sans durée minimale) | art. 8 a | `indisponibilite_totale` (colocation) |
| une disponibilité **limitée plus d'une heure** | art. 8 b, 10 b | `disponibilite_limitee` |
| une atteinte aux données à la suite d'une **action malveillante suspectée** ; un accès malveillant suspecté, non autorisé, capable de provoquer une perturbation grave | art. 8 c, 10 c, 3.1 e | `malveillance` |
| des **pertes financières directes** de plus de 500 000 € ou de 5 % du chiffre d'affaires (le plus bas des deux) | art. 3.1 a | `pertes` |

(Le règlement en prévoit d'autres : décès ou atteinte à la santé, exfiltration de secrets d'affaires, accès physique
compromis à un centre de données, incidents récurrents. Ils ne se présentent pas dans l'exercice.)

**Notifications à l'ANSSI** (directive, art. 23.4) — le délai part de la **prise de connaissance** de l'incident
important :

| Étape | Délai | Contenu |
|---|---|---|
| **Alerte précoce** | 24 h | l'incident est-il dû à une action illicite ou malveillante ? peut-il avoir un effet transfrontière ? |
| **Notification d'incident** | 72 h | mise à jour, première évaluation : gravité, impact, indicateurs de compromission |
| Rapport intermédiaire | à la demande | où en est-on |
| **Rapport final** | un mois après la notification | description détaillée, gravité, **cause profonde**, mesures, effet transfrontière |

Et **les destinataires des services** — les clients — sont informés « sans retard injustifié » d'un incident
important qui peut perturber leurs services (art. 23.1).

## 4. Les clients financiers au regard de DORA

**C'est le client qui classe et qui notifie.** Le Crédit Furanais, PilatPay et la Mutuelle Forézienne doivent, pour
chaque incident lié aux TIC, le **classer** (règlement délégué (UE) 2024/1772) et, s'il est **majeur**, le notifier à
l'ACPR (règlement DORA, art. 19 ; délais du règlement délégué (UE) 2025/301) :

| Étape | Délai |
|---|---|
| **Notification initiale** | 4 h après la classification comme majeur, et **au plus tard 24 h** après la prise de connaissance |
| **Rapport intermédiaire** | 72 h après la notification initiale |
| **Rapport final** | un mois après le dernier rapport intermédiaire |

Si l'incident touche les **intérêts financiers de ses clients**, l'entité financière les informe sans retard
injustifié (art. 19.3) — les commerçants de PilatPay, par exemple.

**Incident majeur** (règlement délégué 2024/1772, art. 8) : il touche des **services critiques** ET, soit un **accès
malveillant** non autorisé susceptible d'entraîner des pertes de données est constaté, soit **au moins deux** autres
seuils sont atteints (art. 9) :

| Seuil | Texte | Ce que Sevranne peut établir (RG9) |
|---|---|---|
| clients touchés : plus de 10 % des clients du service, ou plus de 100 000 | 9.1 | le service est coupé pour tous ses utilisateurs → `clients` |
| réputation : couverture médiatique, plaintes… | 9.2 | un média relaie l'incident → `reputation` |
| durée : plus de 24 h ; interruption de plus de **2 h** d'un service soutenant une fonction critique | 9.3 | `duree`, `interruption` |
| au moins **deux États membres** | 9.4 | PilatPay opère en France et en Belgique → `geographie` |
| pertes de données ; accès malveillant | 9.5 | `donnees` |
| conséquences économiques de plus de 100 000 € | 9.6 | **le client seul le sait** |

**Et Sevranne ?** DORA s'adresse aux entités financières, mais il les oblige à imposer à leurs prestataires TIC des
**clauses contractuelles** (art. 28 à 30) : niveaux de service, **assistance en cas d'incident**, délais et
obligations de **notification**, droits d'**audit** et d'accès, stratégie de sortie, localisation des données. Chaque
entité tient un **registre d'informations** de ces contrats (art. 28.3) et surveille le **risque de concentration**
(art. 29). Le délai de notification de chaque contrat (30 min à 2 h) vient de là : **Sevranne qui prévient tard, c'est
la banque qui notifie tard l'ACPR**, car ses 24 heures courent dès qu'elle a connaissance de l'incident.

(Les autorités européennes de surveillance peuvent aussi désigner des **prestataires TIC critiques**, surveillés
directement — art. 31. Un hébergeur régional comme Sevranne n'en fait pas partie.)

## 5. Et les données personnelles ? (RGPD)

Pour les services qu'il exploite, Sevranne traite des données personnelles **pour le compte** de ses clients : il est
**sous-traitant** (RGPD, art. 28). Si des données sont touchées — l'accès malveillant à la baie de stockage le laisse
craindre —, il **alerte ses clients** dans les meilleurs délais (art. 33.2) ; c'est **chaque client**, responsable du
traitement, qui notifie la CNIL sous 72 h. La Clinique Saint-Lys a une exigence de plus : ses données de santé ne
peuvent être confiées qu'à un **hébergeur certifié** (code de la santé publique, art. L1111-8).

## 6. Le contrat : GTR, pénalités, preuve

- La **GTR** est une promesse contractuelle : la dépasser expose à des **pénalités** et à une action en
  responsabilité (code civil, art. 1231-1). Une cyberattaque n'est presque jamais un cas de **force majeure**
  (art. 1218) : elle est prévisible, et ses effets peuvent être limités.
- La **sécurité de la chaîne d'approvisionnement** (NIS 2, art. 21.2 d) commence par les contrats des prestataires :
  Ardoise Stockage avait un accès d'assistance permanent à la baie. Qui l'a autorisé, et comment était-il contrôlé ?
- Le **journal d'incident** prouve ce qui a été fait, et quand, s'il est intègre et si son auteur est identifié
  (code civil, art. 1366) : c'est la règle RG4 et la signature de la séance 6. Il sert au rapport final, aux
  clients, aux auditeurs de DORA, et devant un juge.

## 7. Ce que fait l'application — et ce qu'elle ne décide pas

| L'application… | …mais ce sont des personnes qui décident |
|---|---|
| dit quels critères NIS 2 sont atteints (RG5) | de **qualifier** l'incident d'important : c'est la qualification qui fait partir les 24 h |
| calcule les échéances, signale les retards (RG6) | le **contenu** de l'alerte précoce, de la notification, du rapport final |
| établit les seuils DORA qu'elle connaît (RG9) | la **classification** de l'incident : c'est le client financier qui décide, et qui notifie l'ACPR |
| trace qui a envoyé quoi, et quand (RG4, RG8) | qui a le droit de parler au nom de Sevranne — la direction pour le rapport final |

## 📚 Vocabulaire

| Terme | Définition |
|---|---|
| **Directive / règlement** | une directive fixe un objectif que chaque État atteint par sa loi (transposition) ; un règlement s'applique tel quel |
| **Entité essentielle / importante** | les deux catégories de NIS 2, selon le secteur et la taille ; elles ont les mêmes obligations, pas le même contrôle |
| **Incident important** (NIS 2) | un incident qui atteint un critère du règlement 2024/2690 : il se notifie à l'ANSSI |
| **Incident majeur** (DORA) | un incident que l'entité financière classe comme majeur (règlement 2024/1772) : elle le notifie à son autorité |
| **Fonction critique ou importante** | une fonction dont la perturbation nuirait gravement à l'entité financière (ses paiements, sa banque en ligne…) |
| **Prestataire tiers de services TIC** | une entreprise qui fournit des services numériques à une entité financière : Sevranne pour ses trois clients financiers |
| **Registre d'informations** | la liste des contrats d'une entité financière avec ses prestataires TIC (DORA, art. 28.3) |
| **Chaîne d'approvisionnement** | les fournisseurs dont on dépend, et les fournisseurs de ses fournisseurs |
| **Sous-traitant** (RGPD) | celui qui traite des données personnelles pour le compte d'un autre |

## 🧠 Auto-évaluation

1. Pourquoi NIS 2 s'applique-t-il à Sevranne mais pas au Crédit Furanais ? Quel texte s'applique au Crédit Furanais ?
2. Sevranne est-elle une entité essentielle ou importante ? Qu'est-ce qui le décide ?
3. Le service de PilatPay est totalement coupé depuis 09:18. À partir de quand l'incident est-il « important » ? Et si c'était un service de colocation ?
4. L'incident est qualifié d'important à 09:50. Quelles sont les échéances de l'alerte précoce et de la notification ?
5. Pourquoi l'application ne dit-elle pas « incident majeur » pour PilatPay, mais « majeur probable » ? Qui décide ?
6. PilatPay est prévenu par Sevranne à 10:30, au lieu de 09:48 comme le prévoit le contrat. Pourquoi est-ce grave pour PilatPay, au regard de DORA ?

<details><summary>Réponses</summary>

1. DORA est le texte spécial pour la finance : il prime sur NIS 2 pour les entités financières. Le Crédit Furanais relève de DORA ; Sevranne, hébergeur non financier, relève de NIS 2.
2. Importante : secteurs hautement critiques (annexe I) et moyenne entreprise (180 salariés, 32 M€). Une grande entreprise du même secteur serait essentielle.
3. À 09:49 : « plus de 30 minutes » d'indisponibilité totale d'un service géré (art. 10). Pour un service de colocation (centre de données), dès la coupure (art. 8).
4. Alerte précoce : mercredi 10 mars à 09:50 (24 h) ; notification : vendredi 12 mars à 09:50 (72 h).
5. Parce que c'est l'entité financière qui classe l'incident et notifie l'ACPR : Sevranne ne connaît pas tout (l'impact économique, par exemple). L'application aide, le client décide.
6. Les 24 h dont PilatPay dispose pour sa notification initiale courent dès qu'il a connaissance de l'incident : chaque minute perdue par Sevranne est une minute de moins pour PilatPay — et un manquement au contrat exigé par DORA.

</details>

## Sources

- Directive (UE) 2022/2555 (NIS 2) et règlement (UE) 2022/2554 (DORA) : [EUR-Lex](https://eur-lex.europa.eu)
- Règlement d'exécution (UE) 2024/2690, incidents importants : [EUR-Lex](https://eur-lex.europa.eu/legal-content/FR/TXT/?uri=CELEX:32024R2690) ; articles 8 et 10 commentés : [Advisera](https://advisera.com/cir-2024-2690/significant-incidents-with-regard-to-managed-service-providers-and-managed-security-service-providers/)
- Règlement délégué (UE) 2024/1772, classification DORA : [EUR-Lex](https://eur-lex.europa.eu/legal-content/FR/TXT/HTML/?uri=OJ%3AL_202401772)
- Délais de notification DORA (règlement délégué 2025/301) : [regulation-dora.eu](https://www.regulation-dora.eu/fr/dora-incident-reporting)
- Où en est la transposition de NIS 2 en France : [Myra Security, octobre 2026](https://www.myrasecurity.com/fr/savoir/nis2-loi-resilience/)
- ANSSI : [cyber.gouv.fr](https://cyber.gouv.fr) · ACPR : [acpr.banque-france.fr](https://acpr.banque-france.fr) · CNIL, violations de données : [cnil.fr](https://www.cnil.fr/fr/les-violations-de-donnees-personnelles)
