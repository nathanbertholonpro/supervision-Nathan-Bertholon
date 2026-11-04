# 2️⃣ Séance 2 — Les dépendances et l'historique

> **Durée** 2 h · **Tests** `php artisan test --group=seance-02` · **Fiche de cours** [Association porteuse et reprise de données](../cours/02-association-reprise.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Quand la baie de stockage SAN-A tombe, quels services s'arrêtent, lesquels se dégradent seulement ? Le prestataire a noté les dépendances dans une colonne texte (« VIRT-1;SAN-A ») : impossible de répondre. Et pour le retour d'expérience comme pour les clients financiers, il faudra savoir **combien de temps** chaque service a été coupé : on ne doit plus écraser l'état précédent.
> **Pour le métier :** faire évoluer une base **qui contient déjà des données** : c'est la situation la plus courante en entreprise, bien plus qu'une base neuve.
>
> **À la fin, vous saurez :** passer du MCD aux tables : l'association porteuse · écrire une migration qui reprend les données existantes · historiser une information au lieu de l'écraser
>
> ⚖️ **Le droit derrière la séance :** NIS 2 impose la **sécurité de la chaîne d'approvisionnement** (art. 21.2 d) : savoir de quels composants — et donc de quels prestataires — dépend chaque service est le point de départ. DORA demande la même chose aux clients financiers (registre, concentration) → [§ 2](../docs/cadre-juridique.md#2-sevranne-au-regard-de-nis-2--une-entité-importante), [§ 6](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [Association porteuse et reprise de données](../cours/02-association-reprise.md), puis son auto-évaluation | 10 min |
| au début | [la situation](#-la-situation) ; RG3 et les sections 4 et 5 du [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant de modifier | le code existant : les migrations `database/migrations/2027_01_10_*`, `app/Models/Service.php`, `app/Models/Composant.php`, `database/seeders/ReferentielSeeder.php` | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| 2 · Comprendre | 🟢 | 4 réponses écrites, dont le MCD | `CARNET.md` | contrôle d'entrée de la séance 3 |
| 2.1 | 🟢 | l'association porteuse `service_composant` (avec son `mode`) et les relations | une migration 🆕, `app/Models/Service.php`, `app/Models/Composant.php` | tests `m1` |
| 2.2 | 🟢 | la **reprise** : les dépendances texte deviennent des lignes, sans rien perdre | la même migration | test `m2` (reprise) |
| 2.3 | 🟡 | la fiche d'un service : composants, mode, prestataire, état | `ReferentielSeeder`, une route, `ServicesController::show`, une vue 🆕 | tests `m3` |
| 2.4 | 🟡 | l'historique des états | une migration 🆕, un modèle `EtatService` 🆕, `changerEtat()` | tests `m4` |
| 2.5 | 🔴 | « si ce composant tombe » : `/composants/SAN-A/impact` | un contrôleur 🆕, une vue 🆕 | bonus `s2_m5` |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé au contrôle d'entrée de la séance 3

**Association porteuse** · **Migration de reprise** · **Base en service** · **Historique** · **`withPivot`** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Hélène Vartan
> **Objet :** qui dépend de quoi ?
>
> Bonjour,
> À 9 h 18, quand la baie SAN-A est tombée, personne n'a su dire tout de suite quels clients étaient touchés. Je veux,
> pour chaque service, la liste de ses composants : est-ce que le service **s'arrête** sans lui (bloquant) ou est-ce
> qu'il **se dégrade** seulement (redondant) ? Et qui le maintient — nous, ou un prestataire ?
> Attention : la base est **en service**. Les dépendances déjà notées par le prestataire ne doivent pas être perdues.
> Enfin, je veux l'**historique** de chaque service : à quelle heure il est tombé, à quelle heure il est revenu.

C'est la règle **RG3** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh recette
php artisan tinker
>>> App\Models\Service::pluck('composants', 'code')
```

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 2*.

1. Dessinez le MCD : SERVICE — DÉPENDRE (mode) — COMPOSANT. Quelles sont les cardinalités ? Où va la propriété `mode` ?
2. Pourquoi une colonne texte « VIRT-1;SAN-A » ne permet-elle pas de répondre à « quels services dépendent de SAN-A ? » de façon fiable ?
3. Dans quel ordre une migration doit-elle travailler sur une base en service : ajouter, transférer, supprimer ? Que se passe-t-il si on supprime d'abord ?
4. Si l'état d'un service est simplement écrasé à chaque changement, que ne peut-on plus savoir au retour d'expérience ?

## 3 ✏️ Modifier

### Mission 2.1 🟢 — L'association porteuse `service_composant`

**Attendu :** une table `service_composant` (`service_id`, `composant_id`, `mode`), clé primaire sur le couple ; dans
les modèles, `Service::composants()` et `Composant::services()`, en `belongsToMany`, avec `->withPivot('mode')`.

```bash
php artisan make:migration create_service_composant_table
```

```php
Schema::create('service_composant', function (Blueprint $table) {
    $table->foreignId('service_id')->constrained()->cascadeOnDelete();
    $table->foreignId('composant_id')->constrained()->cascadeOnDelete();
    $table->string('mode', 12)->default('bloquant');   // bloquant ou redondant (RG3)
    $table->primary(['service_id', 'composant_id']);
});
```

```php
// app/Models/Service.php
public function composants(): BelongsToMany
{
    return $this->belongsToMany(Composant::class, 'service_composant')->withPivot('mode');
}
```

✅ `php artisan test --filter=test_m1 --group=seance-02`

### Mission 2.2 🟢 — La reprise des données

**Attendu :** **dans la même migration**, après la création de la table : pour chaque service, chaque code noté dans
`composants` devient une ligne (`mode` = `bloquant`, par prudence) ; les espaces sont ignorées, un code inconnu aussi ;
**puis** la colonne `services.composants` est supprimée.

> 💡 **Indice.** Travaillez avec `DB::table(...)`, pas avec les modèles : la migration doit encore fonctionner quand les
> modèles auront changé. `DB::table('composants')->pluck('id', 'code')` donne un tableau code → id ;
> `array_map('trim', explode(';', $texte))`.

Puis lancez **`php artisan migrate`** (et non `migrate:fresh`) : votre base garde ses données. Vérifiez dans
phpMyAdmin que la table `service_composant` est remplie.

Le socle passe au rouge : la fabrique (`ServiceFactory`), le `ReferentielSeeder` et le `$fillable` de `Service` parlent
encore de la colonne `composants`. Retirez-la de la fabrique et du `$fillable` ; le seeder est la mission 2.3.

✅ `php artisan test --filter=test_m2 --group=seance-02`

### Mission 2.3 🟡 — La fiche d'un service

**Attendu :**
- `ReferentielSeeder` crée les dépendances avec leur **vrai** mode, lu dans `referentiel.json` (`dependances`) ;
- `/services/PPAY-PSP` (route `services.show`) montre le service, puis chaque composant :
  `<tr data-composant="SAN-A" data-mode="bloquant" data-etat="hors_service">`, son prestataire (`tiers`, ou
  « Sevranne ») ;
- sur le moniteur 1, le code de chaque service mène à sa fiche.

> 💡 **Indice.** Pour que Laravel cherche un service **par son code** dans l'URL, ajoutez au modèle
> `public function getRouteKeyName(): string { return 'code'; }` (c'est déjà fait dans `Notification`).

✅ `php artisan test --filter=test_m3 --group=seance-02`

### Mission 2.4 🟡 — L'historique des états

**Attendu :** une table `etats_service` (`service_id`, `etat`, `debut`) ; à chaque **vrai** changement d'état,
`changerEtat()` ajoute une ligne ; `Service::historique()` les rend du plus ancien au plus récent. La fiche les affiche.

✅ `php artisan test --filter=test_m4 --group=seance-02`

### Mission 2.5 🔴 — Si ce composant tombe…

**Attendu :** `/composants/SAN-A/impact` (route `composants.impact`) liste les services qui en dépendent :
`data-impact="indisponible"` s'il est bloquant, `"degrade"` s'il est redondant.
*(test : `php artisan test --filter=s2_m5`)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-02
php artisan test --group=socle && php artisan test --group=seance-01
git add -A
git commit -m "feat: dépendances en association porteuse (reprise des données), historique, fiche service"
git push
```

---

## 🧠 À retenir

*Ce sont les cinq points du contrôle d'entrée de la séance 3.*

1. **Association porteuse** — une association qui porte une donnée (ici le `mode`) : au niveau physique, une table dont la clé est le couple des deux identifiants.
2. **Migration de reprise** — une migration qui transforme des données existantes : ajouter la structure, transférer les données, supprimer l'ancien — dans cet ordre.
3. **Base en service** — une base qui contient des données réelles : on la fait évoluer avec `migrate`, jamais avec `migrate:fresh`, qui efface tout.
4. **Historique** — on ajoute une ligne par changement au lieu d'écraser : on peut dire ce qui s'est passé, et combien de temps.
5. **`withPivot`** — rend accessible la donnée portée par l'association : `$composant->pivot->mode`.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
