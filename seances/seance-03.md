# 3️⃣ Séance 3 — Moniteur 2 : le journal d'incident

> **Durée** 2 h · **Tests** `php artisan test --group=seance-03` · **Fiche de cours** [Journal d'incident, formulaires et validation](../cours/03-journal-validation.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Pendant l'incident, tout ce qui se passe et se décide doit être écrit, daté, signé : la bascule vers le DC2, l'appel de PilatPay, la découverte du compte d'assistance détourné. Ce journal servira au rapport final à l'ANSSI, aux clients, aux auditeurs de DORA. Il n'a de valeur que si **personne ne peut le réécrire**.
> **Pour le métier :** recevoir une saisie sans lui faire confiance (validation, protection CSRF) et garantir qu'une donnée ne changera plus, à deux niveaux.
>
> **À la fin, vous saurez :** afficher et enregistrer des données avec un formulaire Blade · valider une saisie avec une Form Request, messages en français · rendre une donnée immuable et corriger par ajout
>
> ⚖️ **Le droit derrière la séance :** un écrit électronique **prouve** à deux conditions : on sait qui l'a écrit, et il est conservé intègre (code civil, art. 1366). L'immuabilité (RG4) et la signature (séance 6) sont ces deux conditions. Et le rapport final à l'ANSSI se rédige à partir de ce journal → [§ 6](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [Journal d'incident, formulaires et validation](../cours/03-journal-validation.md), puis son auto-évaluation | 9 min |
| au début | [la situation](#-la-situation) ; RG4 dans le [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant de modifier | le code existant : `app/Models/Journal.php`, `lang/fr/validation.php`, `app/Regles/RegleNonRespectee.php` | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| 2 · Comprendre | 🟢 | 5 réponses écrites : immuabilité, horodatage, CSRF, preuve | `CARNET.md` | contrôle d'entrée de la séance 4 |
| 3.1 | 🟢 | le moniteur 2 : le journal, le plus récent en haut | `app/Http/Controllers/JournalController.php` 🆕, une vue 🆕, une route | tests `m1` |
| 3.2 | 🟢 | écrire au journal : formulaire, validation, heure de l'exercice | `app/Http/Requests/EnregistrerEntreeRequest.php` 🆕, le contrôleur, la vue, une route | tests `m2` |
| 3.3 | 🟡 | RG4 : entrées immuables, corrections par ajout | `app/Models/Journal.php`, une migration 🆕 (`corrige_id`) | tests `m3` |
| 3.4 | 🟡 | filtrer par catégorie, avec les compteurs | le contrôleur, la vue | tests `m4` |
| 3.5 | 🔴 | chercher un mot : `?q=ardoise` | le contrôleur | bonus `s3_m5` |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé au contrôle d'entrée de la séance 4

**Journal d'incident** · **Form Request** · **Jeton CSRF** · **Immuabilité** · **Correction par ajout** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Hélène Vartan
> **Objet :** le journal d'incident
>
> Bonjour,
> Le deuxième écran, c'est le **journal d'incident**. Chaque fait, décision, action ou notification, avec l'heure et
> son auteur ; le scénario y écrit déjà ses événements. Je veux pouvoir y ajouter une entrée depuis le poste, et
> retrouver vite toutes les décisions.
> Une règle absolue : **on ne réécrit pas le journal**. L'heure est celle de l'exercice, pas celle qu'on tape. Si
> quelqu'un se trompe, il écrit une correction, et l'erreur reste visible, barrée.

C'est la règle **RG4** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh recette
php artisan tinker
>>> App\Models\Journal::orderByDesc('heure')->get(['heure', 'categorie', 'texte'])
```

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 3*.

1. Pourquoi l'heure d'une entrée est-elle fixée par le serveur (`Horloge::maintenant()`) et non saisie dans le formulaire ?
2. Un formulaire sans `@csrf` reçoit une erreur 419. Contre quelle attaque ce jeton protège-t-il ?
3. Supprimer les routes de modification suffit-il à rendre le journal inaltérable ? Qui d'autre pourrait le modifier ?
4. Si on pouvait corriger une entrée en la réécrivant, que perdrait-on au retour d'expérience ?
5. ⚖️ Un écrit électronique a valeur de preuve à deux conditions (code civil, art. 1366 — [§ 6 du droit](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve)). Lesquelles ? Quelle partie de votre code répond à chacune (cette séance, ou la séance 6) ?

## 3 ✏️ Modifier

### Mission 3.1 🟢 — Le journal

**Attendu :** `/moniteur/2` (route `moniteur.journal`) liste les entrées de la plus récente à la plus ancienne :
heure, catégorie (`data-categorie="decision"`), texte, auteur ; une entrée venue du scénario se distingue
(`class="scenario"`).

```bash
php artisan make:controller JournalController
```

```php
Route::get('/moniteur/2', [JournalController::class, 'index'])->name('moniteur.journal');
```

✅ `php artisan test --filter=test_m1 --group=seance-03`

### Mission 3.2 🟢 — Écrire au journal

**Attendu :** un formulaire (catégorie, texte, auteur) en haut du moniteur 2, protégé par `@csrf`, envoie
`POST /journal` (route `journal.store`) ; l'entrée prend l'**heure de l'exercice** et la source `centre` ; une saisie
invalide revient avec des messages **en français** ; on revient sur le moniteur 2.

```bash
php artisan make:request EnregistrerEntreeRequest
```

```php
public function rules(): array
{
    return [
        'categorie' => ['required', Rule::in(Journal::CATEGORIES)],
        'texte' => ['required', 'string', 'min:5', 'max:1000'],
        'auteur' => ['required', 'string', 'max:80'],
    ];
}
```

Dans le contrôleur : `public function store(EnregistrerEntreeRequest $request)` ne reçoit que des données valides
(`$request->validated()`) ; ajoutez l'heure et la source vous-même.

✅ `php artisan test --filter=test_m2 --group=seance-03`

### Mission 3.3 🟡 — RG4 : une entrée ne se modifie jamais

**Attendu :**
- aucune route `PUT`, `PATCH` ou `DELETE` sur le journal ;
- le **modèle** refuse lui-même : `update()` et `delete()` lèvent une `RegleNonRespectee` ;
- une correction est une **nouvelle** entrée qui cite l'ancienne (colonne `corrige_id`) ; sur le moniteur, l'entrée
  corrigée est barrée (`class="centre corrige"`) et la correction dit « corrige l'entrée de 09:40 ».

> 💡 **Indice.** Dans le modèle, `protected static function booted(): void { static::updating(fn () => throw new
> RegleNonRespectee('…')); … }`. Deux relations : `corrige()` (`belongsTo`) et `correction()` (`hasOne`).

✅ `php artisan test --filter=test_m3 --group=seance-03`

### Mission 3.4 🟡 — Retrouver une information

**Attendu :** `/moniteur/2?categorie=decision` n'affiche que les décisions ; une tuile par catégorie, même vide,
avec son nombre : `data-categorie-filtre="notification" data-nombre="0"`.

✅ `php artisan test --filter=test_m4 --group=seance-03`

### Mission 3.5 🔴 — Chercher un mot

**Attendu :** `/moniteur/2?q=ardoise` trouve « ARDOISE », sans tenir compte des majuscules.
*(test : `php artisan test --filter=s3_m5`)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-03
php artisan test --group=socle
git add -A
git commit -m "feat: moniteur 2, journal d'incident horodaté et immuable (RG4)"
git push
```

---

## 🧠 À retenir

*Ce sont les cinq points du contrôle d'entrée de la séance 4.*

1. **Journal d'incident** — le journal horodaté et signé de tout ce qui se passe et se décide ; c'est une **preuve** si l'auteur est identifié et l'écrit conservé intègre (code civil, art. 1366).
2. **Form Request** — une classe qui contient les règles de validation ; le contrôleur ne reçoit que des données valides (`$request->validated()`).
3. **Jeton CSRF** — `@csrf` dans chaque formulaire : un autre site ne peut pas faire envoyer le formulaire à votre insu.
4. **Immuabilité** — une donnée qui ne peut plus changer après sa création ; garantie ici deux fois : pas de route, et le modèle refuse.
5. **Correction par ajout** — on ne réécrit pas, on ajoute une entrée qui cite celle qu'elle corrige : l'erreur et sa correction restent visibles.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
