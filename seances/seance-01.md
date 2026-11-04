# 1️⃣ Séance 1 — Moniteur 1 : les services et leur GTR

> **Durée** 2 h · **Tests** `php artisan test --group=seance-01` · **Fiche de cours** [Supervision, GTR et MVC Laravel](../cours/01-supervision-mvc.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Le jour de l'incident, sept services sont tombés en même temps. L'équipe a rétabli d'abord ceux qui criaient le plus fort : la passerelle de paiement de PilatPay, garantie 30 minutes, est restée coupée plus de deux heures. Le moniteur 1 doit dire d'un coup d'œil **quoi rétablir d'abord**, et combien de temps il reste avant la fin de chaque garantie.
> **Pour le métier :** reprendre un code existant en commençant par le **comprendre**, puis isoler une règle de gestion pour pouvoir la tester.
>
> **À la fin, vous saurez :** suivre le trajet d'un événement du scénario jusqu'à l'écran · écrire une règle de gestion à un seul endroit, testable · trier sur plusieurs critères et présenter des données lisibles d'un coup d'œil
>
> ⚖️ **Le droit derrière la séance :** la GTR est une **promesse du contrat** : la dépasser coûte des pénalités. Et pour les trois clients financiers, la **criticité 1** a un sens juridique : le service soutient une « fonction critique ou importante » au sens de DORA → [§ 4](../docs/cadre-juridique.md#4-les-clients-financiers-au-regard-de-dora), [§ 6](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | [📂 Les documents de la supervision](../docs/documents-a-produire.md) et les § 1 et 2 du [⚖️ droit : NIS 2 et DORA](../docs/cadre-juridique.md) | 10 min |
| **avant** la séance | la fiche [Supervision, GTR et MVC Laravel](../cours/01-supervision-mvc.md), puis son auto-évaluation | 8 min |
| au début | [la situation](#-la-situation) ; RG1, RG2 et la section 4 (les services) du [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant de modifier | le code existant : `app/Exercice/Moteur.php`, `app/Models/Service.php`, `app/Http/Controllers/ServicesController.php`, `resources/views/moniteurs/services.blade.php` | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| 1.1 | 🟢 | 6 réponses écrites : le trajet d'un événement | `CARNET.md` | contrôle d'entrée de la séance 2 |
| 1.2 | 🟢 | RG2 : les services dans l'ordre de rétablissement | `app/Regles/Indisponibilite.php` (marge), `app/Regles/OrdreRetablissement.php`, le contrôleur, la vue | tests `m2` |
| 1.3 | 🟡 | RG1 : indisponibilité, GTR, jauge et niveau | `app/Regles/Indisponibilite.php`, la vue | tests `m3` |
| 1.4 | 🟡 | une tuile par état, avec son nombre de services | le contrôleur, la vue | tests `m4` |
| 1.5 | 🔴 | le filtre par client `?client=CFU` | le contrôleur | bonus `s1_m5` |
| **Fin** | ✅ | commit + push · cases cochées dans [`PROGRESSION.md`](../PROGRESSION.md) · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé au contrôle d'entrée de la séance 2

**Supervision** · **GTR** · **Règle de gestion isolée** · **Tri à plusieurs clés** · **Route → contrôleur → vue** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Hélène Vartan, directrice des opérations
> **Objet :** le premier écran du poste de supervision
>
> Bonjour,
> Le prestataire nous a laissé un premier écran : la liste des services, dans le désordre, avec des durées en
> minutes. Pendant l'incident, il faut savoir **par quoi commencer** : d'abord ce qui est coupé, et parmi ce qui est
> coupé, ce qui compte le plus pour nos clients — et ce qui va dépasser sa garantie en premier.
> Pour chaque service coupé, je veux voir depuis quand, combien de sa **GTR** est déjà consommé, en orange à partir
> des trois quarts, en rouge au-delà. Et en haut, combien de services sont indisponibles, dégradés, normaux.

Ce sont les règles **RG1** et **RG2** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh                 # l'exercice démarre à 09:00 et va 20 fois plus vite
```

Ouvrez l'application (Ctrl+clic sur l'adresse), puis le moniteur 1 : à 09:18 de l'exercice (moins d'une minute
réelle), sept services passent « indisponible ». Puis :

```bash
cd projetLaravel
php artisan test --group=socle       # 9 tests verts
php artisan test --group=seance-01   # rouges : c'est votre travail du jour
```

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 1* : quelques lignes, avec vos mots.

1. Ouvrez `database/data/scenario-exercice.json`. Que se passe-t-il à 09:18 ? Quelles données l'*inject* porte-t-il ?
2. Dans `app/Exercice/Moteur.php`, méthode `rattraper()` : quels injects sont joués, et comment est-on sûr qu'un inject n'est joué qu'**une** fois ?
3. Qui appelle `Moteur::rattraper()`, et à quel moment ? (cherchez dans `bootstrap/app.php`)
4. Dans `app/Models/Service.php`, méthode `changerEtat()` : que devient `indisponible_depuis` quand le service tombe ? quand il repasse en mode dégradé ?
5. Suivez une requête vers `/moniteur/1` : quelle route, quel contrôleur, quelle vue ? Qui fournit les services à la vue ?
6. Pourquoi la règle de gestion reçoit-elle l'heure en paramètre (`$maintenant`) au lieu d'appeler `now()` ?

## 3 ✏️ Modifier

### Mission 1.2 🟢 — RG2 : l'ordre de rétablissement

**Attendu :**
- `Indisponibilite::marge($service, $maintenant)` = GTR − durée d'indisponibilité (négative si la GTR est dépassée) ;
  un service qui fonctionne a toute sa GTR devant lui ;
- `OrdreRetablissement::trier($services, $maintenant)` : indisponibles, puis dégradés, puis normaux ; dans chaque
  groupe, la criticité (1 d'abord), puis la marge la plus faible, puis le code ;
- sur le moniteur 1, chaque ligne est `<tr data-service="PPAY-PSP" …>`, dans cet ordre.

1. Dans `app/Regles/Indisponibilite.php`, écrivez `marge()` (elle utilisera `duree()`, que vous écrirez en 1.3 :
   commencez par elle si vous préférez).
2. Dans `app/Regles/OrdreRetablissement.php` : un tri **à plusieurs clés**, en une seule fois.
   ```php
   $rangEtat = ['indisponible' => 0, 'degrade' => 1, 'normal' => 2];

   return collect($services)
       ->sortBy([
           fn ($a, $b) => $rangEtat[$a->etat] <=> $rangEtat[$b->etat],
           fn ($a, $b) => $a->criticite <=> $b->criticite,
           // la marge, puis le code : à vous
       ])
       ->values();
   ```
3. Dans le contrôleur : `OrdreRetablissement::trier(Service::all(), Horloge::maintenant())` part vers la vue, avec l'heure.
4. Dans la vue : `data-service="{{ $s->code }}"` sur chaque `<tr>`.

✅ `php artisan test --filter=test_m2 --group=seance-01`

### Mission 1.3 🟡 — RG1 : la GTR d'un coup d'œil

**Attendu :**
- `duree()` : minutes depuis `indisponible_depuis`, ou `null` si le service n'est pas indisponible ;
- `consommation()` : durée ÷ GTR (`0.5` = la moitié), ou `null` ;
- `niveau()` : `"risque"` à partir de 75 % (le seuil est dans `config('exercice.seuils.risque')`), `"hors_gtr"`
  **au-delà** de 100 %, sinon `"ok"` ;
- chaque ligne porte `data-niveau="…"` et montre la durée, la GTR (`data-gtr="60"`) **écrite comme on la lit**
  (`App\Support\Duree::format(60)` donne « 1 h »), une jauge et le pourcentage.

> 💡 **Indice.** `$service->indisponible_depuis->diffInMinutes($maintenant)` donne des minutes (un nombre à virgule :
> `(int)`). « Au-delà de 100 % » : `>`, pas `>=`.

✅ `php artisan test --filter=test_m3 --group=seance-01`

### Mission 1.4 🟡 — Combien de services dans chaque état ?

**Attendu :** en haut du moniteur, une tuile par état — **même vide** — :
`<div data-etat="indisponible" data-nombre="7">`. Les libellés lisibles sont dans `lang/fr/libelles.php`
(`__('libelles.etats_pluriel.degrade')`).

✅ `php artisan test --filter=test_m4 --group=seance-01`

### Mission 1.5 🔴 — Les services d'un client

**Attendu :** `/moniteur/1?client=CFU` n'affiche que les services du Crédit Furanais ; les tuiles comptent toujours tout.
*(test : `php artisan test --filter=s1_m5`)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-01
php artisan test --group=socle
git add -A
git commit -m "feat: moniteur 1, ordre de rétablissement (RG2) et consommation de la GTR (RG1)"
git push
```

---

## 🧠 À retenir

*Ce sont les cinq points du contrôle d'entrée de la séance 2.*

1. **Supervision** — surveiller en continu services et composants, pour savoir avant le client que quelque chose ne va pas, et quoi faire d'abord.
2. **GTR** — garantie de temps de rétablissement : la durée maximale d'interruption promise au client par le contrat.
3. **Règle de gestion isolée** — une classe de `app/Regles`, qui reçoit tout ce qu'il lui faut (dont l'heure) : on la teste seule, à n'importe quelle heure.
4. **Tri à plusieurs clés** — `sortBy([…])` avec une liste de comparaisons : la deuxième départage les égalités de la première, et ainsi de suite.
5. **Route → contrôleur → vue** — la route associe une URL à une méthode du contrôleur ; le contrôleur prépare les données ; la vue Blade les affiche.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
