# 📝 Mon carnet — Centre de supervision

> ✏️ **Ici, vous écrivez.** Les réponses aux questions *Comprendre* de chaque séance, ce que les guides vous demandent
> de noter, et les quatre **supports** des séances 7 et 8. Quelques lignes suffisent : avec vos mots, sans copier la
> fiche. Committez ce fichier à chaque séance : c'est une trace de ce que vous avez compris, relue par l'enseignant
> et utile pour réviser les interrogations.

**Nom :** · **Prénom :**

---

## Séance 1 — Moniteur 1 : les services et leur GTR

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-01.md).*

**1.** Ouvrez `database/data/scenario-exercice.json`. Que se passe-t-il à 09:18 ? Quelles données l'*inject* porte-t-il ?

> ✍️

**2.** Dans `app/Exercice/Moteur.php`, méthode `rattraper()` : quels injects sont joués, et comment est-on sûr qu'un inject n'est joué qu'**une** fois ?

> ✍️

**3.** Qui appelle `Moteur::rattraper()`, et à quel moment ? (cherchez dans `bootstrap/app.php`)

> ✍️

**4.** Dans `app/Models/Service.php`, méthode `changerEtat()` : que devient `indisponible_depuis` quand le service tombe ? quand il repasse en mode dégradé ?

> ✍️

**5.** Suivez une requête vers `/moniteur/1` : quelle route, quel contrôleur, quelle vue ? Qui fournit les services à la vue ?

> ✍️

**6.** Pourquoi la règle de gestion reçoit-elle l'heure en paramètre (`$maintenant`) au lieu d'appeler `now()` ?

> ✍️

---

## Séance 2 — Les dépendances et l'historique

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-02.md).*

**1.** Dessinez le MCD : SERVICE — DÉPENDRE (mode) — COMPOSANT. Quelles sont les cardinalités ? Où va la propriété `mode` ?

> ✍️

**2.** Pourquoi une colonne texte « VIRT-1;SAN-A » ne permet-elle pas de répondre à « quels services dépendent de SAN-A ? » de façon fiable ?

> ✍️

**3.** Dans quel ordre une migration doit-elle travailler sur une base en service : ajouter, transférer, supprimer ? Que se passe-t-il si on supprime d'abord ?

> ✍️

**4.** Si l'état d'un service est simplement écrasé à chaque changement, que ne peut-on plus savoir au retour d'expérience ?

> ✍️

---

## Séance 3 — Moniteur 2 : le journal d'incident

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-03.md).*

**1.** Pourquoi l'heure d'une entrée est-elle fixée par le serveur (`Horloge::maintenant()`) et non saisie dans le formulaire ?

> ✍️

**2.** Un formulaire sans `@csrf` reçoit une erreur 419. Contre quelle attaque ce jeton protège-t-il ?

> ✍️

**3.** Supprimer les routes de modification suffit-il à rendre le journal inaltérable ? Qui d'autre pourrait le modifier ?

> ✍️

**4.** Si on pouvait corriger une entrée en la réécrivant, que perdrait-on au retour d'expérience ?

> ✍️

**5.** ⚖️ Un écrit électronique a valeur de preuve à deux conditions (code civil, art. 1366 — [§ 6 du droit](docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve)). Lesquelles ? Quelle partie de votre code répond à chacune (cette séance, ou la séance 6) ?

> ✍️

---

## Séance 4 — Incident important (NIS 2) et moniteur 3 : les notifications

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-04.md).*

**1.** ⚖️ La passerelle de PilatPay (infogérance) est totalement coupée depuis 09:18. À 09:48 pile, l'incident est-il important ? À 09:49 ? Et si c'était un service de colocation ?

> ✍️

**2.** ⚖️ Le RSSI qualifie l'incident à 09:50. Échéances de l'alerte précoce et de la notification à l'ANSSI ? Pourquoi ne partent-elles pas de 09:18 ?

> ✍️

**3.** L'incident est détecté à 09:18 ; le contrat de PilatPay prévoit 30 minutes. Échéance ? À partir de quelle heure le statut est-il « proche » ?

> ✍️

**4.** On demande d'envoyer la notification ANSSI alors que l'alerte précoce n'est pas partie. Quel code HTTP, et pourquoi pas 400 ni 404 ?

> ✍️

**5.** La notification est marquée « envoyée », puis l'écriture au journal échoue (disque plein). Qu'a-t-on perdu ?

> ✍️

---

## Séance 5 — Les quatre moniteurs synchronisés

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-05.md).*

**1.** Combien d'octets le moniteur 1 télécharge-t-il en une minute aujourd'hui (onglet *Réseau*) ? Et pour un poste de quatre écrans, sur les quatre heures de l'exercice ?

> ✍️

**2.** Pourquoi l'heure de l'exercice (en minutes) doit-elle faire partie de la « version » de l'état ?

> ✍️

**3.** Que contient une réponse 304, et pourquoi est-elle si légère ?

> ✍️

**4.** Les quatre fenêtres sont dans le même navigateur. Pourquoi un BroadcastChannel suffit-il, sans passer par le serveur ?

> ✍️

---

## Séance 6 — Sécurité et rôles : qui a le droit de notifier qui ?

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-06.md).*

**1.** Pourquoi ne stocke-t-on jamais un mot de passe, même chiffré ? Que stocke-t-on à la place ?

> ✍️

**2.** Pourquoi le message d'échec doit-il être le même, que le compte existe ou non ?

> ✍️

**3.** Authentifier, autoriser : quelle différence ? Donnez un exemple de chaque dans le centre de supervision.

> ✍️

**4.** Pourquoi l'API répond-elle 401 alors qu'une page redirige vers la connexion (302) ?

> ✍️

**5.** ⚖️ Pourquoi le rapport final à l'ANSSI est-il réservé à la direction ([§ 2 du droit](docs/cadre-juridique.md#2-sevranne-au-regard-de-nis-2--une-entité-importante)) ?

> ✍️

---

## Séance 7 — Moniteur 4 : tiers, clients financiers (DORA) et registre

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-07.md).*

**1.** D'après l'historique, combien de temps la banque en ligne du Crédit Furanais a-t-elle été coupée ? Pourquoi ne peut-on pas le savoir avec la seule colonne `indisponible_depuis` ?

> ✍️

**2.** ⚖️ À 10:00, la passerelle de PilatPay (criticité 1) est coupée depuis 09:18 ; PilatPay opère en France et en Belgique ; aucune malveillance n'est encore suspectée. Quels seuils sont atteints ? L'incident est-il majeur probable ? Et pour le Crédit Furanais (France seulement) ?

> ✍️

**3.** ⚖️ À 10:00, la Mutuelle Forézienne n'est pas en « majeur probable » ; à 10:10, elle l'est. Qu'est-ce qui a changé, et pourquoi ce seuil suffit-il à lui seul ?

> ✍️

**4.** ⚖️ Pourquoi l'écran dit-il « majeur probable » et jamais « majeur » ? Qu'est-ce que Sevranne ne peut pas savoir ?

> ✍️

**5.** Trois services critiques dépendent d'Ardoise Stockage. Quel risque cela représente-t-il pour nos clients, et que leur demande DORA à ce sujet ?

> ✍️

### 📂 Support 1 — Alerte précoce à l'ANSSI (NIS 2, art. 23.4 a)

*À envoyer dans les 24 h qui suivent la qualification d'incident important. Critères : [docs/documents-a-produire.md](docs/documents-a-produire.md#2-les-quatre-supports-que-vous-rédigez).*

| Rubrique | Votre texte |
|---|---|
| Entité (nom, secteur, catégorie NIS 2), contact | |
| Date et heure de la détection ; de la qualification d'incident important | |
| Ce qui se passe (en quelques lignes, sans jargon) | |
| Services et clients touchés (combien, lesquels critiques) | |
| Critères d'incident important atteints (règlement 2024/2690) | |
| L'incident est-il dû à une action illicite ou malveillante ? (oui / non / suspecté, sur quoi on se fonde) | |
| Peut-il avoir un effet transfrontière ? (pourquoi) | |
| Premières mesures prises | |
| Rédigé par (nom, rôle), le · à quelle heure | |

### 📂 Support 2 — Information de PilatPay (client financier soumis à DORA)

*Le client classe l'incident et notifie l'ACPR : vous lui donnez les éléments, vous ne décidez pas à sa place.*

**Objet :**

> ✍️ Ce qui s'est passé et depuis quand · le service touché et sa criticité · les éléments de classification que Sevranne
> établit (seuils atteints, d'après le moniteur 4) · ce que Sevranne ne sait pas · les mesures et le rétablissement
> prévu · un contact joignable

---

## Séance 8 — L'exercice : audit, tests, retour d'expérience

*Questions de la partie « 2 · Comprendre » du [guide de la séance](seances/seance-08.md).*

**La grille d'audit** — pour chaque ligne : ce que vous avez trouvé, et le verdict ✅ / ⚠️ / ❌.

| # | Risque (question dans le guide) | Ce que j'ai trouvé (fichier, ligne, adresse) | Verdict |
|---|---|---|---|
| 1 | **A01 Contrôle d'accès** | | |
| 2 | A02 Cryptographie | | |
| 3 | A03 Injection | | |
| 4 | A04 Conception | | |
| 5 | A05 Configuration | | |
| 6 | A07 Authentification | | |
| 7 | **A08 Intégrité des données** | | |
| 8 | A09 Journalisation | | |

**Mission 8.1 — la faille.** Ce qu'elle permettait (et comment vous l'avez prouvé), ce que cela changeait sur les écrans :

> ✍️

Pourquoi votre correction la ferme :

> ✍️

**Mission 8.4 — la recette.** Ce qui était conforme, ce qui ne l'était pas :

> ✍️

**Le retour d'expérience.** Trois choses à changer — dans les contrats, les procédures ou l'organisation de Sevranne —, et pourquoi :

> ✍️

### 📂 Support 3 — Projet de rapport final à l'ANSSI (NIS 2, art. 23.4 d)

| Rubrique | Votre texte |
|---|---|
| Description détaillée de l'incident (chronologie : détection, qualification, rétablissements) | |
| Gravité et impact : services, clients (dont financiers), durées d'indisponibilité, pertes estimées | |
| Type de menace et cause profonde (la chaîne d'approvisionnement) | |
| Mesures d'atténuation prises | |
| Mesures à prendre pour que cela ne se reproduise pas | |
| Effet transfrontière | |
| Validé par (direction), le | |

### 📂 Support 4 — Rapport d'exercice (une page, pour la direction)

*À partir de la page RETEX et du moniteur 4 à 13:00 (`./lancer.sh recette 13:00`). Chaque chiffre doit se retrouver à l'écran.*

**Le déroulé en cinq lignes :**

> ✍️

**Les indicateurs** (GTR tenues et non tenues ; notifications à l'heure, en retard ; clients financiers en « majeur probable ») :

> ✍️

**Trois points forts, trois écarts :**

> ✍️

**Trois changements, et pourquoi** (ceux du retour d'expérience ci-dessus, mis au propre) :

> ✍️
