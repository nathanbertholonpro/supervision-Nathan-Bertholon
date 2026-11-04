# 6️⃣ Séance 6 — Sécurité et rôles : qui a le droit de notifier qui ?

> **Durée** 2 h · **Tests** `php artisan test --group=seance-06` · **Fiche de cours** [Authentification, autorisation, traçabilité](../cours/06-securite-roles.md)

## 🧭 Feuille de route

> 🎯 **Pourquoi cette séance ?** Aujourd'hui, n'importe qui ouvre le poste, écrit au journal sous le nom de son choix et « envoie » une notification à l'ANSSI. Un centre de supervision qui parle à une autorité et à des banques doit savoir **qui** fait quoi, et n'autoriser chacun qu'à ce que son rôle permet.
> **Pour le métier :** authentifier sans jamais stocker un mot de passe, autoriser finement, tracer chaque action.
>
> **À la fin, vous saurez :** authentifier un utilisateur sans jamais stocker son mot de passe · protéger toute une application, puis ouvrir ce qui doit l'être · autoriser selon le rôle et le portefeuille (Gates) · signer chaque écrit avec la personne connectée
>
> ⚖️ **Le droit derrière la séance :** NIS 2, art. 20 : la **direction** approuve les mesures de cybersécurité et peut être tenue **responsable** — d'où le rapport final que seule la direction envoie. L'**imputabilité** (qui a fait quoi) est aussi une condition de la preuve → [§ 2](../docs/cadre-juridique.md#2-sevranne-au-regard-de-nis-2--une-entité-importante), [§ 6](../docs/cadre-juridique.md#6-le-contrat--gtr-pénalités-preuve).

### 📖 À lire — on lit, on ne produit rien

| Quand | Quoi | Temps |
|---|---|---|
| **avant** la séance | la fiche [Authentification, autorisation, traçabilité](../cours/06-securite-roles.md), puis son auto-évaluation | 10 min |
| au début | [la situation](#-la-situation) ; RG8 et la section 9 du [cahier des charges](../docs/cahier-des-charges.md) | 5 min |
| avant de modifier | le code existant : `app/Models/User.php`, `routes/web.php`, `bootstrap/app.php` | 10 min |

### ✏️ À faire — chaque ligne laisse une trace que l'on peut vérifier

*Chemins depuis `projetLaravel/` · 🆕 fichier à créer · 🟢 guidé · 🟡 semi-guidé · 🔴 facultatif.*

| Mission | | Vous produisez | Où | Preuve |
|---|---|---|---|---|
| 2 · Comprendre | 🟢 | 5 réponses écrites, dont la responsabilité de la direction | `CARNET.md` | contrôle d'entrée de la séance 7 |
| 6.1 | 🟢 | les comptes (rôle, portefeuille), la connexion, le message neutre | une migration 🆕, `ComptesSeeder` 🆕, `ConnexionController` 🆕, une vue 🆕 | tests `m1` |
| 6.2 | 🟢 | toute l'application protégée ; 401 pour l'API ; déconnexion | `routes/web.php`, `bootstrap/app.php`, le gabarit | tests `m2` |
| 6.3 | 🟡 | RG8 : chacun son rôle (Gates), 403 | `app/Providers/AppServiceProvider.php`, les contrôleurs, les vues | tests `m3` |
| 6.4 | 🟡 | signé par la personne connectée | les contrôleurs, la Form Request | tests `m4` |
| 6.5 | 🔴 | freiner la force brute | `ConnexionController` | bonus `s6_m5` |
| **Fin** | ✅ | commit + push · cases cochées · journal au portail | dépôt, portail | onglet *Actions* vert |

### 🧠 À retenir — interrogé au contrôle d'entrée de la séance 7

**Hachage** · **Message neutre** · **Authentification / autorisation** · **Gate** · **Responsabilité de la direction** — définitions en [fin de séance](#-à-retenir).

---

## 📖 La situation

> **De :** Karim Belaïd, RSSI
> **Objet :** n'importe qui peut notifier l'ANSSI !
>
> Bonjour,
> Il faut des comptes, et des rôles :
> - la **direction** (Hélène Vartan) fait tout — et elle seule envoie le **rapport final** à l'ANSSI : c'est elle qui
>   en répond ;
> - le **RSSI** (moi) envoie l'alerte précoce et la notification à l'ANSSI ;
> - les chargés de **relation client** ne notifient que **leurs** clients : Léa Fournel a les clients financiers,
>   Julien Morand les autres ;
> - les **analystes** du NOC et du SOC écrivent au journal ;
> - l'**auditeur** du Crédit Furanais (DORA lui donne un droit d'audit) lit tout et n'écrit rien.
> Et plus de champ « Votre nom » : ce qui est écrit est signé par la personne connectée.

C'est la règle **RG8** du [cahier des charges](../docs/cahier-des-charges.md).

---

## 1 ▶️ Lancer

```bash
./lancer.sh
```

Ouvrez le moniteur 3 dans une fenêtre de navigation privée : tout est accessible. Envoyez une notification en
signant « Hélène Vartan » : le journal vous croit.

## 2 🔍 Comprendre

> ✏️ Répondez dans [`CARNET.md`](../CARNET.md), section *Séance 6*.

1. Pourquoi ne stocke-t-on jamais un mot de passe, même chiffré ? Que stocke-t-on à la place ?
2. Pourquoi le message d'échec doit-il être le même, que le compte existe ou non ?
3. Authentifier, autoriser : quelle différence ? Donnez un exemple de chaque dans le centre de supervision.
4. Pourquoi l'API répond-elle 401 alors qu'une page redirige vers la connexion (302) ?
5. ⚖️ Pourquoi le rapport final à l'ANSSI est-il réservé à la direction ([§ 2 du droit](../docs/cadre-juridique.md#2-sevranne-au-regard-de-nis-2--une-entité-importante)) ?

## 3 ✏️ Modifier

### Mission 6.1 🟢 — Les comptes et la connexion

**Attendu :**
- une migration ajoute à `users` un `role` et un portefeuille `clients` (« CFU,PPAY,MFA ») ;
- `ComptesSeeder` (appelé par `DatabaseSeeder`) crée les sept comptes `@sevranne.test`, mot de passe
  `Supervision2027!` — `hvartan` (directeur), `kbelaid` (rssi), `lfournel` (relation_client, CFU,PPAY,MFA),
  `jmorand` (relation_client, CSL,VALF,SEV), `tguerin` et `irabier` (analyste), `mdelorme` (auditeur) ;
- `/connexion` : formulaire ; `Auth::attempt()` ; même message d'échec dans les deux cas ; nouvelle session.

> 💡 **Indice.** Le modèle `User` hache déjà le mot de passe (`'password' => 'hashed'`). Le rôle n'est pas
> « fillable » : posez-le avec `forceFill([...])->save()`, jamais depuis un formulaire.

✅ `php artisan test --filter=test_m1 --group=seance-06`

### Mission 6.2 🟢 — Tout protéger, puis ouvrir

**Attendu :** toutes les routes dans un groupe `Route::middleware('auth')`, sauf la connexion ; une page renvoie vers
`/connexion`, l'API répond 401 ; le bandeau montre qui est connecté et un bouton « Se déconnecter »
(`POST /deconnexion`, route `deconnexion`).

> 💡 **Indice.** Dans `bootstrap/app.php`, `$middleware->redirectGuestsTo('/connexion')`. Chargez `moniteur.js`
> seulement pour une personne connectée (`@auth`) : sinon la page de connexion se recharge en boucle.

✅ `php artisan test --filter=test_m2 --group=seance-06`

### Mission 6.3 🟡 — RG8 : chacun son rôle

**Attendu :**
- `Gate::define('ecrire', …)` : tout le monde sauf l'auditeur ; la Form Request du journal l'utilise
  (`authorize()`) ;
- `Gate::define('envoyer-notification', fn (User $u, Notification $n) => …)` : règle `nis2_final` → direction ;
  `nis2` → direction ou RSSI ; `contrat` → direction, ou relation client si le client est **dans son portefeuille** ;
- le contrôleur des notifications commence par `Gate::authorize('envoyer-notification', $notification)` (403) ; les vues
  cachent ce qui est interdit (`@can`).

✅ `php artisan test --filter=test_m3 --group=seance-06`

### Mission 6.4 🟡 — Signé par la personne connectée

**Attendu :** l'auteur d'une entrée de journal et d'une notification est **toujours** `$request->user()->name`, même si
la requête envoie un autre nom. Retirez le champ `auteur` des formulaires et de la Form Request.

✅ `php artisan test --filter=test_m4 --group=seance-06`

### Mission 6.5 🔴 — Freiner la force brute

**Attendu :** au-delà de 5 échecs pour une même adresse, « Trop de tentatives… », même avec le bon mot de passe, pendant
une minute (`RateLimiter`).
*(test : `php artisan test --filter=s6_m5`)*

## 4 ✅ Valider

```bash
php artisan test --group=seance-06
php artisan test        # toutes les séances précédentes doivent rester vertes
git add -A
git commit -m "feat: connexion, rôles et portefeuilles (RG8), signature"
git push
```

> À partir d'ici, les tests se connectent avec des comptes de test : si une séance précédente passe au rouge, c'est
> qu'une route ou une vue a besoin d'un utilisateur.

---

## 🧠 À retenir

*Ce sont les cinq points du contrôle d'entrée de la séance 7.*

1. **Hachage** — une empreinte à sens unique (bcrypt, salée) : on vérifie un mot de passe sans pouvoir le retrouver.
2. **Message neutre** — le même échec que le compte existe ou non : on ne révèle pas quels comptes existent.
3. **Authentification / autorisation** — qui êtes-vous (la connexion) / avez-vous le droit (le rôle, le portefeuille).
4. **Gate** — une règle d'autorisation nommée (`Gate::define`) ; `Gate::authorize` répond 403 si elle refuse, `@can` cache ce qui est interdit.
5. **Responsabilité de la direction** — NIS 2, art. 20 : la direction approuve les mesures de cybersécurité, les supervise et peut en répondre ; ce qu'elle signe engage l'entreprise.

## ✏️ Journal de fin de séance (portail)

Ce que j'ai fait · ce qui m'a bloqué · ce que j'ai compris aujourd'hui.
