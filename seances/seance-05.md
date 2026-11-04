# 5️⃣ Séance 5 — Les quatre moniteurs synchronisés

> **Durée** 2 h (dont 30 min d'interrogation) · **Tests** `php artisan test --group=seance-05` · **Fiche de cours** [API, ETag et communication entre fenêtres](../cours/05-api-etag-fenetres.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Le poste a quatre écrans, et chacun se recharge en entier toutes les 5 secondes : ça clignote, et la saisie du journal s'efface au milieu d'une phrase. Il faut que chaque écran ne se mette à jour **que si quelque chose a changé**, et que les écrans se parlent : je clique sur un service au moniteur 1, le moniteur 3 montre les notifications de son client.
> **Pour le métier :** faire dialoguer des pages et un serveur sans gaspiller (une API, un cache HTTP), et faire communiquer plusieurs fenêtres.
>
> **À la fin, vous saurez :** écrire une petite API JSON · répondre « rien n'a changé » avec un ETag et un 304 · remplacer une partie de page sans la recharger · faire communiquer des fenêtres avec un BroadcastChannel
>
> ⚖️ **Le droit derrière la séance :** rien de nouveau, mais un enjeu : les délais légaux de la séance 4 s'affichent sur plusieurs écrans ; tous doivent montrer **la même échéance au même moment**, sans quoi personne ne voit qu'elle expire → [§ 7](../docs/cadre-juridique.md#7-ce-que-fait-lapplication--et-ce-quelle-ne-décide-pas).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [API, ETag et communication entre fenêtres](../cours/05-api-etag-fenetres.md), puis son auto-évaluation ; réviser l'interrogation n° 1 | 8 min |
| au début | [la situation](#-la-situation) ; RG7 et la section 7 du [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant de modifier | le code existant : `config/poste.json`, les quatre vues de `resources/views/moniteurs/`, `resources/views/layouts/supervision.blade.php` | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| ✍️ | | l'interrogation n° 1 (séances 1 à 4), 30 min, sur papier | — | la copie |
| 2 · Comprendre | 🟢 | 4 réponses écrites | `CARNET.md` | contrôle d'entrée de la séance 6 |
| 5.1 | 🟢 | l'API d'état : heure, version, ETag, 304 | `app/Http/Controllers/EtatController.php` 🆕, une route | tests `m1` |
| 5.2 | 🟢 | des moniteurs qui écoutent au lieu de se recharger | `public/js/moniteur.js` 🆕, le gabarit, les vues | tests `m2` |
| 5.3 | 🟡 | l'API du poste : l'adresse de chaque moniteur | `EtatController`, une route | tests `m3` |
| 5.4 | 🟡 | RG7 : je clique sur le 1, le 3 suit | `moniteur.js`, `NotificationsController`, les vues | tests `m4` |
| 5.5 | 🔴 | le lanceur qui place les quatre fenêtres : `/poste` | une vue 🆕 | bonus `s5_m5` |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé au contrôle d'entrée de la séance 6

**API JSON** · **ETag et 304** · **Rafraîchissement par zones** · **BroadcastChannel** · **Poste** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Thomas Guérin, chef d'équipe NOC
> **Objet :** les écrans clignotent
>
> Bonjour,
> Pendant l'exercice d'essai, les quatre écrans se rechargeaient toutes les cinq secondes : impossible de lire, et
> mon entrée au journal s'est effacée deux fois. Un écran ne doit bouger **que si quelque chose a changé**.
> Et quand je clique sur la passerelle de PilatPay au premier écran, je veux que le troisième me montre tout de suite
> ce que je dois envoyer à PilatPay — et à l'ANSSI.

C'est la règle **RG7** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh
```

Ouvrez le moniteur 1 et l'outil de développement du navigateur (F12, onglet *Réseau*) : une page entière toutes les
5 secondes.

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 5*.

1. Combien d'octets le moniteur 1 télécharge-t-il en une minute aujourd'hui (onglet *Réseau*) ? Et pour un poste de quatre écrans, sur les quatre heures de l'exercice ?
2. Pourquoi l'heure de l'exercice (en minutes) doit-elle faire partie de la « version » de l'état ?
3. Que contient une réponse 304, et pourquoi est-elle si légère ?
4. Les quatre fenêtres sont dans le même navigateur. Pourquoi un BroadcastChannel suffit-il, sans passer par le serveur ?

## 3 ✏️ Modifier

### Mission 5.1 🟢 — Une API qui dit si quelque chose a changé

**Attendu :** `GET /api/etat` (route `api.etat`) renvoie `{"heure": "10:15", "t": "T+01:15", "version": "…"}` et un
en-tête `ETag`. La version change quand un service, un composant, une notification, le journal ou l'exercice change —
et seulement alors. Si la requête porte `If-None-Match` égal à l'ETag actuel : **304**, sans contenu.

```php
$version = sha1(json_encode([
    $maintenant->format('H:i'),
    Service::max('updated_at'),
    // composants, notifications, journal (nombre et dernier id), exercice : à vous
]));
$etag = '"'.$version.'"';
if ($request->header('If-None-Match') === $etag) {
    return response('', 304)->header('ETag', $etag);
}
```

✅ `php artisan test --filter=test_m1 --group=seance-05`

### Mission 5.2 🟢 — Les moniteurs écoutent au lieu de se recharger

**Attendu :** plus de `<meta http-equiv="refresh">` ; le gabarit charge `public/js/moniteur.js`, qui toutes les 2 s
interroge `/api/etat` avec `If-None-Match`, et quand la version change, remplace seulement les zones marquées
`data-zone-vivante` (et l'horloge `data-horloge`) — **jamais** le formulaire du journal.

> 💡 **Indice.** La fiche de cours donne le squelette de `moniteur.js` (`fetch`, `DOMParser`, `replaceWith`).

✅ `php artisan test --filter=test_m2 --group=seance-05`

### Mission 5.3 🟡 — Le poste et ses quatre écrans

**Attendu :** `GET /api/poste` renvoie `config/poste.json` en ajoutant à chaque moniteur l'`url` de sa vue
(`services` → `route('moniteur.services')`, etc.).

✅ `php artisan test --filter=test_m3 --group=seance-05`

### Mission 5.4 🟡 — RG7 : je clique sur le 1, le 3 suit

**Attendu :**
- sur le moniteur 1, chaque ligne de service porte `data-selectionnable` ; un clic la diffuse sur un
  `BroadcastChannel` ;
- le moniteur 3 (`<body data-vue="notifications">`) recharge alors `/moniteur/3?service=PPAY-PSP` : il n'affiche
  que les notifications du **client** de ce service, et celles de l'ANSSI, et porte `data-selection="PPAY-PSP"`.

✅ `php artisan test --filter=test_m4 --group=seance-05`

### Mission 5.5 🔴 — Le lanceur

**Attendu :** `/poste` lit `/api/poste` et ouvre les quatre moniteurs, chacun sur son écran
(`window.getScreenDetails()`) ; s'il manque des écrans, les fenêtres se partagent l'écran courant.
*(test : `php artisan test --filter=s5_m5`)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-05
php artisan test --group=socle
git add -A
git commit -m "feat: API d'état (ETag, 304), moniteurs synchronisés, sélection partagée (RG7)"
git push
```

---

## 🧠 À retenir

*Ce sont les cinq points du contrôle d'entrée de la séance 6.*

1. **API JSON** — une adresse qui renvoie des données (pas une page) pour qu'un programme les lise.
2. **ETag et 304** — l'empreinte d'un état ; le navigateur la renvoie (`If-None-Match`) et le serveur répond 304, sans contenu, si rien n'a changé.
3. **Rafraîchissement par zones** — remplacer seulement les parties vivantes d'une page : pas de clignotement, la saisie en cours n'est pas perdue.
4. **BroadcastChannel** — un canal entre les fenêtres d'un même navigateur et d'un même site, sans passer par le serveur.
5. **Poste** — la configuration (`config/poste.json`) qui dit quelle vue va sur quel écran.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
