# 8️⃣ Séance 8 — L'exercice : audit, tests, retour d'expérience

> **Durée** 2 h (dont 30 min d'interrogation) · **Tests** `php artisan test --group=seance-08` · **Fiche de cours** [Audit, signature HMAC et retour d'expérience](../cours/08-audit-signature-retex.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Mardi 9 mars, l'exercice a lieu pour de bon, sous les yeux de l'auditeur du Crédit Furanais. Avant : le RSSI veut un **audit** — l'incident est venu d'un prestataire, il se méfie de ce que le précédent a laissé dans le code ; l'exploitation veut une adresse à surveiller ; la direction veut une **recette**. Après : le **retour d'expérience**, et ce que l'on change.
> **Pour le métier :** **livrer** : prouver que l'application est sûre, testée, exploitable, et montrer ce qu'elle apporte — puis le présenter dans votre dossier E5.
>
> **À la fin, vous saurez :** auditer une application avec une grille ; trouver, prouver, corriger une faille · vérifier l'origine et l'intégrité d'un message avec une signature HMAC · écrire des tests qui protègent vraiment · préparer l'exploitation, recetter, faire un retour d'expérience
>
> ⚖️ **Le droit derrière la séance :** NIS 2 exige d'**évaluer l'efficacité** des mesures (art. 21.2 f) et de sécuriser la **chaîne d'approvisionnement** (art. 21.2 d) ; le **rapport final** à l'ANSSI dit la cause profonde et les mesures (art. 23.4 d). DORA donne à l'auditeur du client un droit d'**audit** (art. 30) → [§ 3](../docs/cadre-juridique.md#3-quand-un-incident-est-il--important--et-que-faut-il-notifier--nis-2), [§ 6](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [Audit, signature HMAC et retour d'expérience](../cours/08-audit-signature-retex.md), puis son auto-évaluation ; réviser l'interrogation n° 2 et le [droit](../docs/cadre-juridique.md) en entier | 15 min |
| au début | [la situation](#-la-situation) ; le [cahier des charges](../docs/cahier-des-charges.md) en entier : la recette le vérifie point par point | 5 min |
| avant de modifier | le code existant : `routes/web.php`, `routes/api.php`, `bootstrap/app.php`, et tout ce que la grille d'audit vous fera ouvrir | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| ✍️ | | l'interrogation n° 2 (séances 5 à 7), 30 min, sur papier | — | la copie |
| 2 · Comprendre | 🟡 | la grille d'audit remplie (8 lignes) | `CARNET.md` | la recette et l'oral E5 |
| 8.1 | 🟡 | la faille : trouvée, prouvée, corrigée, expliquée | le code que l'audit désigne, `config/services.php`, `.env.example`, `CARNET.md` | tests `m1` |
| 8.2 | 🟢 | au moins deux tests à vous, vérifiés en cassant le code | `tests/Feature/Etudiant/` : vos fichiers 🆕 | tests `m2` + groupe `etudiant` |
| 8.3 | 🟢 | le style du code sans écart | tout le projet (`./vendor/bin/pint`) | ligne 🎨 de l'onglet *Actions* |
| 8.4 | 🟡 | sonde de santé, pages d'erreur, retour d'expérience, recette | une route, un contrôleur 🆕, des vues 🆕 | tests `m4` + recette |
| 8.5 | 🟢 | la fiche de réalisation E5 | `e5/FICHE-REALISATION.md` | votre dossier E5 |
| 📂 Supports | 🟡 | le **projet de rapport final à l'ANSSI** et le **rapport d'exercice** | `CARNET.md` (trames prêtes) | grille d'évaluation · [critères](../docs/documents-a-produire.md#2-les-quatre-supports-que-vous-rédigez) |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé à l'oral E5 : vous les expliquerez à votre jury

**Contrôle d'accès et intégrité** · **Signature HMAC** · **Test qui protège** · **Sonde de santé** · **Exercice et RETEX** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Hélène Vartan
> **Objet :** mardi, c'est l'exercice
>
> Bonjour,
> Mardi, on joue l'exercice avec l'outil, devant l'auditeur du Crédit Furanais. Avant, trois demandes :
> Karim veut un **audit** — l'incident est venu d'un accès laissé ouvert chez un prestataire, et le développeur qui a
> commencé l'outil a « branché sa sonde pour ses essais » ; l'exploitation veut une **adresse à surveiller** ; et moi,
> je veux une **recette** : on fige l'exercice à 10 h, je regarde chaque écran, et je vérifie qu'il dit ce que
> j'attends.
> Après l'exercice : une page de **retour d'expérience** — quels services ont dépassé leur GTR, quelles notifications
> sont parties en retard. Et deux écrits : le **projet de rapport final** pour l'ANSSI, et un **rapport d'exercice**
> d'une page pour la direction.

---

## 1 ▶️ Lancer

```bash
./lancer.sh recette
php artisan route:list -v
```

## 2 🔍 Comprendre — la grille d'audit

> ✏️ Remplissez la grille dans [`CARNET.md`](../CARNET.md), section *Séance 8* : pour chaque ligne, ce que vous avez
> trouvé (fichier, ligne, adresse) et un verdict ✅ / ⚠️ / ❌.

| # | Risque (OWASP Top 10, 2021) | La question à se poser | Où regarder |
|---|---|---|---|
| 1 | **A01 Contrôle d'accès** | Chaque adresse exige-t-elle d'être connecté, ou de prouver qui l'on est ? Un rôle peut-il faire plus que prévu ? | `php artisan route:list -v`, les Gates |
| 2 | A02 Cryptographie | Les mots de passe sont-ils hachés ? Un secret traîne-t-il dans le code ? | `users`, `.env`, `config/` |
| 3 | A03 Injection | Une saisie peut-elle se retrouver telle quelle dans une requête SQL ou dans la page ? | `whereRaw`, `{!! !!}` |
| 4 | A04 Conception | Une règle importante ne tient-elle qu'à l'interface (bouton caché) ? | contrôleurs, `@can` / `Gate::authorize` |
| 5 | A05 Configuration | En production, une erreur affiche-t-elle des détails techniques ? | `APP_DEBUG`, pages d'erreur |
| 6 | A07 Authentification | Le message d'échec trahit-il les comptes ? La force brute est-elle freinée ? | `ConnexionController` |
| 7 | **A08 Intégrité des données** | Les données qui **arrivent d'un autre système** sont-elles vérifiées (origine, intégrité) ? | `routes/api.php`, ce qu'elles appellent |
| 8 | A09 Journalisation | Peut-on dire qui a envoyé une notification, écrit au journal, et quand ? Et qui a changé l'état d'un service ? | `notifications`, `journal` |

## 3 ✏️ Modifier

### Mission 8.1 🟡 — Trouver, prouver, corriger la faille

Une ligne de la grille est rouge. Ne la cherchez pas dans le guide : la grille et `php artisan route:list -v` suffisent.

1. **Trouvez**-la : quelle adresse, quel fichier la déclare, pourquoi la séance 6 ne l'a-t-elle pas protégée ?
2. **Prouvez**-la : sans être connecté, que pouvez-vous faire ? (une requête `curl` suffit) Qu'est-ce que cela change sur
   les écrans — pour la GTR, pour les notifications, pour les clients financiers ?
3. **Corrigez**-la : l'outil de supervision externe doit continuer de fonctionner, mais **prouver** qu'il est bien
   lui : il signe le corps de chaque requête avec un secret partagé (en-tête `X-Signature: sha256=…`, HMAC-SHA256).
   Toute requête sans signature valide reçoit **401** et ne change rien ; sans secret configuré, tout est refusé.
   Le secret se lit dans `config('services.supervision.secret')` (variable `SUPERVISION_WEBHOOK_SECRET`).
4. Écrivez dans [`CARNET.md`](../CARNET.md) ce que la faille permettait, et pourquoi votre correction la ferme.

> 💡 **Indice.** `hash_hmac('sha256', $request->getContent(), $secret)` calcule la signature attendue ;
> `hash_equals($attendue, $recue)` compare **en temps constant** (une comparaison `===` peut laisser deviner la
> signature caractère par caractère).

✅ `php artisan test --filter=test_m1 --group=seance-08`

### Mission 8.2 🟢 — Vos propres tests

**Attendu :** au moins **deux** tests à vous dans `tests/Feature/Etudiant/` (groupe `etudiant`), qui protègent une règle
que vous avez codée. Un bon test **échoue** quand on casse la règle : cassez-la exprès, relancez, constatez le rouge,
remettez le code. Le mode d'emploi est dans `tests/Feature/Etudiant/README.md`.

✅ `php artisan test --filter=test_m2 --group=seance-08` puis `php artisan test --group=etudiant`

### Mission 8.3 🟢 — Le style du code

```bash
./vendor/bin/pint          # corrige
./vendor/bin/pint --test   # vérifie, comme l'onglet Actions
```

### Mission 8.4 🟡 — Prête pour l'exploitation, et le retour d'expérience

**Attendu :**
- `GET /sante` (route `sante`), **sans connexion** : `{"statut": "ok", "base": "ok", "heure_exercice": "10:00"}` ; aucune
  donnée métier ;
- des pages d'erreur 403, 404, 409 et 500 sans détail technique (`resources/views/errors/`) ; en production
  `APP_DEBUG=false` ;
- `/retex` (route `retex`) : pour chaque service qui a été coupé, sa plus longue indisponibilité face à sa GTR
  (`data-retex-service="PPAY-PSP" data-tenue="non"`) — réutilisez `Historique` ; les notifications envoyées en retard,
  ou pas envoyées alors que l'échéance est passée (`data-retex-notification="CLIENT-PPAY"`).

✅ `php artisan test --filter=test_m4 --group=seance-08`

**La recette avec Mme Vartan** (`./lancer.sh recette`, l'exercice figé à 10:00, rien n'est encore envoyé) :

| Écran | Ce qu'elle attend |
|---|---|
| 1 · Services | 7 indisponibles, 0 dégradé, 2 normaux ; dans l'ordre PPAY-PSP (140 %, hors GTR, marge −12 min), CFU-WEB (70 %), MFA-GED (35 %), MESS, MFA-EXT, VALF-PAIE, MUTU, puis CSL-DPI et CFU-SAUV |
| 2 · Incident | critère « indisponibilité totale » atteint, incident qualifié d'important à 09:50 ; 6 entrées au journal |
| 3 · Notifications | CLIENT-PPAY **en retard** (09:48) ; CLIENT-CFU 10:18 ; CLIENT-MFA et CLIENT-CSL 11:18 ; CLIENT-VALF 13:18 ; ANSSI-ALERTE **mer. 10/03 09:50** ; ANSSI-NOTIF bloquée (ven. 12/03 09:50) ; ANSSI-FINAL bloquée |
| 4 · Tiers et clients | 1 service hors GTR, 1 notification en retard ; PilatPay **majeur probable** (clients, géographie) ; Crédit Furanais et Mutuelle non (clients) ; concentration : Ardoise Stockage 3 services critiques, FibreMont Télécom 3, Watteo Énergie 1 |

Puis `./lancer.sh recette 13:00` : la page RETEX doit montrer **trois GTR non tenues** — CFU-WEB (2 h 02 pour 1 h),
PPAY-PSP (2 h 17 pour 30 min), MFA-GED (2 h 52 pour 2 h).

Rejouez enfin un exercice accéléré (`./lancer.sh`) **en envoyant** les notifications au fil de l'eau, un rôle chacun
(comptes de démonstration) : le RETEX change-t-il comme vous l'attendez ?

### Mission 8.5 🟢 — La fiche de réalisation E5

Complétez [e5/FICHE-REALISATION.md](../e5/FICHE-REALISATION.md) : la demande, ce que vous avez livré, une difficulté
et comment vous l'avez résolue, les preuves.

### 📂 Les supports — le rapport final et le rapport d'exercice 🟡

Dans [`CARNET.md`](../CARNET.md), section *Séance 8* (trames prêtes). Critères : [les supports que vous
rédigez](../docs/documents-a-produire.md#2-les-quatre-supports-que-vous-rédigez).

- **Projet de rapport final à l'ANSSI** (NIS 2, art. 23.4 d) : description, gravité et impact, **cause profonde** — la
  chaîne d'approvisionnement —, mesures prises et à prendre, effet transfrontière (PilatPay en Belgique).
- **Rapport d'exercice** : une page pour la direction, à partir de la page RETEX et du moniteur 4 à 13:00 ; trois
  changements — dans les contrats, les procédures, l'organisation, pas seulement dans l'application.

## 4 ✅ Valider

```bash
php artisan test
./vendor/bin/pint --test
git add -A
git commit -m "fix: webhook signé (HMAC) ; sonde de santé, RETEX, pages d'erreur ; mes tests"
git push
```

---

## 🧠 À retenir

*Vous les expliquerez à l'oral E5.*

1. **Contrôle d'accès et intégrité** (OWASP A01, A08) — chaque entrée de l'application doit vérifier qui parle : une session pour une personne, une preuve pour un programme ; `route:list -v` montre ce qui est ouvert.
2. **Signature HMAC** — une empreinte du message calculée avec un secret partagé : elle prouve l'origine et l'intégrité ; on la compare en temps constant (`hash_equals`).
3. **Test qui protège** — un test vaut s'il échoue quand on casse la règle : on le vérifie en la cassant.
4. **Sonde de santé** — une adresse publique, légère, qui dit si l'application et sa base répondent, sans donnée métier.
5. **Exercice et RETEX** — on teste le dispositif en le jouant (NIS 2 demande d'évaluer l'efficacité des mesures) ; le retour d'expérience dit ce qui n'a pas tenu, et ce que l'on change.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
