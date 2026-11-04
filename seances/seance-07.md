# 7️⃣ Séance 7 — Moniteur 4 : tiers, clients financiers (DORA) et registre

> **Durée** 2 h · **Tests** `php artisan test --group=seance-07` · **Fiche de cours** [DORA : classification, registre et indicateurs](../cours/07-dora-registre-indicateurs.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Trois de nos clients sont des entités financières soumises à DORA. Chacune doit décider, vite, si l'incident est **majeur** et, si oui, prévenir l'ACPR en moins de 24 heures. Elles comptent sur nous pour leur donner les éléments. Le moniteur 4 doit aussi montrer d'où vient le problème : un **prestataire**, dont trop de services critiques dépendent.
> **Pour le métier :** traduire des seuils réglementaires en règles exactes, réutiliser les règles déjà écrites (l'historique, la GTR, les notifications) au lieu de les recopier.
>
> **À la fin, vous saurez :** calculer à partir d'un historique · évaluer des seuils et combiner des conditions · produire un registre et des indicateurs
>
> ⚖️ **Le droit derrière la séance :** DORA (règlement 2022/2554) : c'est le **client financier** qui classe l'incident (règlement délégué 2024/1772) et le notifie à l'ACPR — 4 h après la classification, au plus tard 24 h après en avoir eu connaissance. Sevranne, prestataire TIC, lui doit l'assistance et la notification prévues au contrat (art. 30) ; le client tient le **registre** de ses prestataires et surveille la **concentration** (art. 28-29) → [§ 4](../docs/cadre-juridique.md#4-les-clients-financiers-au-regard-de-dora), [§ 5](../docs/cadre-juridique.md#5-et-les-données-personnelles--rgpd).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [DORA : classification, registre et indicateurs](../cours/07-dora-registre-indicateurs.md), puis son auto-évaluation | 10 min |
| au début | [la situation](#-la-situation) ; RG9, RG10 et les sections 1 et 5 du [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant d'écrire les supports | les § 3 et 4 du [⚖️ droit : NIS 2 et DORA](../docs/cadre-juridique.md) | 8 min |
| avant de modifier | le code existant : `app/Models/Contrat.php`, `app/Regles/Historique.php`, `app/Regles/Dora.php`, `app/Regles/Registre.php` (les signatures) | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| 2 · Comprendre | 🟢 | 5 réponses écrites, dont deux évaluations DORA | `CARNET.md` | interrogation n° 2 |
| 7.1 | 🟢 | le moniteur 4 : composants, état, prestataire | `app/Http/Controllers/TiersController.php` 🆕, une vue 🆕, une route | tests `m1` |
| 7.2 | 🟡 | RG9 : l'historique, l'évaluation DORA de chaque client financier | `app/Regles/Historique.php`, `app/Regles/Dora.php`, la vue | tests `m2` |
| 7.3 | 🟡 | RG10 : le registre des contrats, la concentration | `app/Regles/Registre.php`, la vue | tests `m3` |
| 7.4 | 🟢 | les indicateurs | `app/Regles/Indicateurs.php` 🆕, une route, la vue | tests `m4` |
| 7.5 | 🔴 | le fil des alertes, la plus grave d'abord | `Indicateurs`, une route | bonus `s7_m5` |
| 📂 Supports | 🟡 | l'**alerte précoce à l'ANSSI** et l'**information de PilatPay** | `CARNET.md` (trames prêtes) | grille d'évaluation · [critères](../docs/documents-a-produire.md#2-les-quatre-supports-que-vous-rédigez) |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé dans l'interrogation n° 2 (début de la séance 8)

**Incident majeur (DORA)** · **Fonction critique ou importante** · **Registre d'informations** · **Concentration** · **Indicateur** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Léa Fournel, relation client — clients financiers
> **Objet :** nos clients DORA nous appellent
>
> Bonjour,
> À 10 h 40, le Crédit Furanais nous a demandé par écrit « les éléments pour classifier l'incident ». Ce n'est pas à
> nous de dire si l'incident est majeur pour eux — c'est leur décision, et c'est eux qui préviennent l'ACPR. Mais
> nous savons beaucoup de choses : quels services critiques sont coupés, depuis quand, si les deux pays de PilatPay
> sont touchés, si une action malveillante est suspectée, si la presse en parle. Le dernier écran doit le montrer,
> client par client, avec ce que cela donne au regard des seuils du règlement.
> Il doit aussi montrer nos composants et leurs **prestataires**, et le **registre** de nos contrats : nos clients le
> tiennent, ils nous le demandent. Et la direction veut savoir de quel prestataire dépendent trop de services critiques.

Ce sont les règles **RG9** et **RG10** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh recette 12:00
php artisan tinker
>>> App\Models\Service::firstWhere('code', 'CFU-WEB')->historique->map(fn ($h) => [$h->etat, $h->debut->format('H:i')])
>>> App\Models\Contrat::all(['client', 'dora', 'etats_membres', 'delai_notification_minutes'])
```

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 7*.

1. D'après l'historique, combien de temps la banque en ligne du Crédit Furanais a-t-elle été coupée ? Pourquoi ne peut-on pas le savoir avec la seule colonne `indisponible_depuis` ?
2. ⚖️ À 10:00, la passerelle de PilatPay (criticité 1) est coupée depuis 09:18 ; PilatPay opère en France et en Belgique ; aucune malveillance n'est encore suspectée. Quels seuils sont atteints ? L'incident est-il majeur probable ? Et pour le Crédit Furanais (France seulement) ?
3. ⚖️ À 10:00, la Mutuelle Forézienne n'est pas en « majeur probable » ; à 10:10, elle l'est. Qu'est-ce qui a changé, et pourquoi ce seuil suffit-il à lui seul ?
4. ⚖️ Pourquoi l'écran dit-il « majeur probable » et jamais « majeur » ? Qu'est-ce que Sevranne ne peut pas savoir ?
5. Trois services critiques dépendent d'Ardoise Stockage. Quel risque cela représente-t-il pour nos clients, et que leur demande DORA à ce sujet ?

## 3 ✏️ Modifier

### Mission 7.1 🟢 — Le moniteur 4 : composants et prestataires

**Attendu :** `/moniteur/4` (route `moniteur.tiers`) liste les composants : `<tr data-composant="SAN-A"
data-etat="hors_service">`, avec le nom, le prestataire (`tiers`, ou « Sevranne »), l'état et depuis quand.

✅ `php artisan test --filter=test_m1 --group=seance-07`

### Mission 7.2 🟡 — RG9 : aider le client à classifier

**Attendu :**
- `Historique::plusLongueIndisponibilite($service, $maintenant)` : la plus longue indisponibilité **continue** d'après
  l'historique (une indisponibilité en cours compte jusqu'à maintenant) ; `aEteIndisponible()` et `aEteTouche()`
  (indisponible ou dégradé à un moment) ;
- `Dora::evaluer($contrat, $services, $exercice, $maintenant)` renvoie
  `['critique' => bool, 'seuils' => [...]]` : *critique* si un service de criticité 1 du client a été touché ; les
  seuils, dans l'ordre de `Dora::SEUILS` — `clients` (un service a été indisponible), `duree` (plus de 24 h depuis la
  détection), `interruption` (un service de criticité 1 coupé **plus de** 2 h), `geographie` (au moins deux États
  membres, et le client touché), `donnees` (malveillance suspectée, et le client touché), `reputation` (médias, et le
  client touché) ;
- `Dora::majeurProbable($evaluation)` : critique, **et** `donnees`, ou au moins deux **autres** seuils ;
- sur le moniteur 4, pour chaque client **DORA** : `data-client="PPAY" data-majeur="1"`, et chaque seuil atteint
  `data-seuil="geographie"`.

> 💡 **Indice.** Pour l'historique : parcourez les états de `$service->historique` (antérieurs à maintenant) ; pour un
> état `indisponible`, la fin est le `debut` de l'état suivant, ou maintenant.

✅ `php artisan test --filter=test_m2 --group=seance-07`

### Mission 7.3 🟡 — RG10 : registre et concentration

**Attendu :**
- `Registre::sousTraitants($service)` : les prestataires de ses composants, sans doublon, triés ;
- `Registre::concentration($services)` : pour chaque prestataire, les services **de criticité 1** qui en dépendent de
  façon **bloquante** — le prestataire le plus chargé d'abord ;
- le moniteur 4 montre le registre (`<tr data-contrat="PPAY" data-dora="1">` : entité, autorité, services, délai de
  notification « 30 min », sous-traitants, localisation) et la concentration
  (`data-concentration="Ardoise Stockage" data-nombre="3"`).

✅ `php artisan test --filter=test_m3 --group=seance-07`

### Mission 7.4 🟢 — Les indicateurs

**Attendu :** `GET /api/indicateurs` (route `api.indicateurs`) renvoie au moins `services_indisponibles`,
`services_hors_gtr`, `notifications_envoyees`, `notifications_en_retard` ; le moniteur 4 les affiche en tuiles
`data-indicateur="…"`. Les règles existent : ce calcul les **réutilise**.

```php
// app/Regles/Indicateurs.php
public static function calculer(Exercice $exercice, CarbonInterface $maintenant): array
{
    $services = Service::all();
    $notifications = Notification::with('dependDe')->get();

    return [
        'services_indisponibles' => $services->where('etat', 'indisponible')->count(),
        'services_hors_gtr' => $services->filter(fn ($s) => Indisponibilite::niveau($s, $maintenant) === 'hors_gtr')->count(),
        // notifications envoyées, en retard : à vous
    ];
}
```

✅ `php artisan test --filter=test_m4 --group=seance-07`

### Mission 7.5 🔴 — Le fil des alertes

**Attendu :** `GET /api/alertes` renvoie `[{cle, niveau, texte}]` : les services à risque ou hors GTR
(`gtr:PPAY-PSP`), les notifications proches ou en retard (`notification:CLIENT-PPAY`) ; le plus grave d'abord (hors
GTR, en retard), puis les services avant les notifications, puis le code.
*(test : `php artisan test --filter=s7_m5`)*

### 📂 Les supports — l'alerte précoce et l'information de PilatPay 🟡

L'application établit les critères et les seuils ; elle n'écrit pas les messages. Rédigez les deux dans
[`CARNET.md`](../CARNET.md) (section *Séance 7*, trames prêtes), à partir de **vos** écrans en mode recette
(`./lancer.sh recette 10:30`). Critères : [les supports que vous rédigez](../docs/documents-a-produire.md#2-les-quatre-supports-que-vous-rédigez).

- **Alerte précoce à l'ANSSI** (NIS 2, art. 23.4 a) : ce qu'on sait dans les 24 h. Deux questions obligatoires :
  action malveillante ? effet transfrontière ?
- **Information de PilatPay** : les faits, le service, les éléments de classification que nous établissons — et ce que
  nous ne savons pas. Sans jamais décider à sa place que l'incident est majeur.

*Si le temps manque, terminez à la maison : les deux sont lus avant la séance 8.*

## 4 ✅ Valider

```bash
php artisan test --group=seance-07
php artisan test
git add -A
git commit -m "feat: moniteur 4, aide à la classification DORA (RG9), registre et concentration (RG10), indicateurs"
git push
```

**Annonce :** l'interrogation n° 2 (séances 5 à 7) ouvre la séance 8.

---

## 🧠 À retenir

*Ce sont les cinq points de l'interrogation n° 2.*

1. **Incident majeur (DORA)** — classé par l'entité financière (règlement délégué 2024/1772) : services critiques touchés et accès malveillant, ou au moins deux seuils ; notifié à l'autorité 4 h après la classification, au plus tard 24 h après la prise de connaissance.
2. **Fonction critique ou importante** — une fonction dont la perturbation nuirait gravement à l'entité financière ; ici, la criticité 1 d'un service.
3. **Registre d'informations** — la liste que tient l'entité financière de ses contrats avec des prestataires TIC : services, fonctions soutenues, sous-traitants, localisation.
4. **Concentration** — trop de fonctions critiques qui dépendent du même prestataire : une seule défaillance, et tout tombe.
5. **Indicateur** — un chiffre défini sans ambiguïté, calculé en réutilisant les règles de gestion plutôt qu'en les recopiant.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
