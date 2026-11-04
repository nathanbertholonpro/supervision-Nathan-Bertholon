# 🚑 Dépannage — classé par ce que vous voyez à l'écran

> Cherchez (Ctrl+F) un morceau du message d'erreur. Toujours commencer par **lire la première ligne** de l'erreur
> Laravel : elle dit presque toujours où regarder (fichier et numéro de ligne).
>
> Une erreur absente de cette page ? Ouvrez une *issue* avec le message complet : elle y sera ajoutée.

---

## Au démarrage du Codespace

### Le port 8000 affiche « Forbidden », une page Apache ou une erreur 500
Le projet n'est pas encore prêt (le script d'installation tourne encore) ou il a échoué. Relancez-le, il ne casse rien :
```bash
bash /workspace/.devcontainer/post-create.sh
```

### `SQLSTATE[HY000] [2002] Connection refused` · `getaddrinfo for mysql failed`
Le serveur MySQL démarre plus lentement que PHP. Attendez 30 secondes et recommencez.
Vérifiez dans `projetLaravel/.env` : `DB_HOST=mysql` (le **nom du conteneur**, pas `127.0.0.1`).

### `No application encryption key has been specified`
```bash
cd projetLaravel && php artisan key:generate
```

### `The stream or file ".../storage/logs/laravel.log" could not be opened: Permission denied`
Apache n'a pas le droit d'écrire dans `storage/`. Depuis `projetLaravel/` :
```bash
chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache
```

### `bash: ./lancer.sh: Permission denied`
```bash
chmod +x lancer.sh
```

### Le Codespace s'est arrêté (inactivité)
Rouvrez-le depuis GitHub (**Code → Codespaces**). Vos fichiers et votre base sont conservés tant que le Codespace
n'est pas supprimé.

---

## L'exercice et son horloge

### L'horloge est bloquée à 13:00 · « l'exercice est fini »
L'exercice dure 4 heures d'exercice, soit 12 minutes réelles. Relancez-le : `./lancer.sh`.

### Je veux travailler sans que l'horloge bouge
`./lancer.sh recette` fige l'exercice à 10:00 (`./lancer.sh recette 11:30` à l'heure choisie). Tout ce qui est prévu
avant cette heure est joué.

### `./lancer.sh` a effacé ce que j'avais saisi
C'est voulu : il repart d'une base **neuve** (migrations + données). Pour avancer l'horloge en gardant la base :
```bash
cd projetLaravel && php artisan exercice:demarrer --figer=11:30 --sans-remise-a-zero
```

### Rien ne change sur l'écran alors que l'heure avance
Les événements du scénario sont joués à chaque page demandée (le middleware `RattraperExercice`). Rechargez la page.
Après la séance 5, vérifiez dans **F12 → Réseau** que `/api/etat` répond 304 puis 200.

---

## Dans le navigateur

### `419 | Page Expired`
Le formulaire n'a pas de jeton CSRF. Ajoutez `@csrf` juste après la balise `<form …>`.

### `Route [moniteur.plan] not defined.`
Le nom de route n'existe pas (faute de frappe, ou route pas encore créée).
```bash
php artisan route:list            # toutes les routes et leurs noms
php artisan route:list -v         # … avec leurs middlewares (séance 8)
```

### `Target class [App\Http\Controllers\…] does not exist.`
Le `use` en haut de `routes/web.php` manque ou le nom de classe est mal écrit (majuscules comprises).

### `Undefined variable $maintenant`
La vue utilise une variable que le contrôleur ne lui a pas transmise : `return view('…', ['maintenant' => $maintenant]);`

### `LogicException: À écrire en séance 1 (mission 1.3).`
Vous appelez une règle dont le corps n'est pas encore écrit : c'est la mission indiquée.

### `Call to a member function format() on null`
Une date est vide (un service jamais coupé, une notification pas envoyée). Utilisez l'opérateur `?->` :
`{{ $s->indisponible_depuis?->format('H:i') ?? '—' }}`

### `403 | Accès interdit` (à partir de la séance 6)
Votre rôle ne permet pas l'action : un auditeur n'écrit rien, la relation client ne notifie que les clients de son
portefeuille, seule la direction envoie le rapport final. Comptes de l'exercice (mot de passe `Supervision2027!`) :
`hvartan@sevranne.test` (direction), `kbelaid@sevranne.test` (RSSI), `lfournel@sevranne.test` (relation client)…

### La page de connexion se recharge sans arrêt
Le script des moniteurs tourne sur la page de connexion, reçoit 401 et recharge la page. Chargez `moniteur.js`
seulement dans un bloc `@auth … @endauth` du gabarit.

### `Add [clients] to fillable property to allow mass assignment`
Le champ n'est pas dans `$fillable` du modèle. Ajoutez-le — **sauf** s'il ne doit jamais venir d'un formulaire
(comme `role` ou `clients` : utilisez `forceFill`).

---

## Dans la base de données

### `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'composants'` (après la séance 2)
La colonne texte a disparu : la fabrique `ServiceFactory`, le seeder `ReferentielSeeder` ou le `$fillable` de `Service`
l'utilisent encore. Retirez-la (missions 2.2 et 2.3).

### `SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'service_composant'`
Votre migration a échoué à mi-chemin (séance 2). Sous MySQL, ce qui a été fait avant l'erreur n'est pas annulé.
Supprimez la table dans phpMyAdmin, corrigez la migration, relancez `php artisan migrate`.

### `SQLSTATE[23000]: … 1062 Duplicate entry '3-5' for key 'PRIMARY'`
Vous insérez deux fois la même dépendance (un code noté deux fois dans le texte). `array_unique()` avant d'insérer.

### `RegleNonRespectee: Une entrée du journal ne se modifie pas` (à partir de la séance 3)
C'est la règle RG4 qui fonctionne : on ne modifie pas une entrée, on ajoute une correction.

### Je veux repartir d'une base propre
```bash
./lancer.sh       # ⚠️ efface toutes les données et relance l'exercice
```

---

## Dans les tests

### `Class "App\Models\EtatService" not found` · `Route [moniteur.journal] not defined.`
La mission correspondante n'est pas encore faite : le test **décrit** ce que vous devez construire. C'est normal au
début de chaque séance.

### `Failed asserting that '…' contains "data-service=\"PPAY-PSP\""`
Le test cherche un attribut précis dans votre HTML : relisez l'**Attendu** de la mission, il donne la balise exacte.

### Un test du groupe `socle` est devenu rouge
Vous avez cassé quelque chose qui marchait : c'est une **régression**. Regardez votre dernière modification
(`git diff`) et cherchez ce qui a changé.

### Après la séance 6, des tests des séances précédentes passent en 403
Les tests se connectent avec des comptes de test (la direction, surtout). Vérifiez vos portes (`Gate::define`) : la
direction peut écrire au journal et envoyer toutes les notifications.

### `401` en appelant `/api/supervision/webhook` (à partir de la séance 8)
C'est votre correction qui fonctionne : la requête n'est pas signée, ou pas avec le bon secret. Pour essayer à la main :
```bash
CORPS='{"service":"PPAY-PSP","etat":"normal"}'
SIG=$(printf '%s' "$CORPS" | openssl dgst -sha256 -hmac "$SUPERVISION_WEBHOOK_SECRET" | sed 's/^.* //')
curl -X POST http://localhost:8000/api/supervision/webhook -H 'Content-Type: application/json' -H "X-Signature: sha256=$SIG" -d "$CORPS"
```

---

## Git et GitHub

### `! [rejected] main -> main (fetch first)`
Le dépôt GitHub contient des commits que vous n'avez pas (modification faite sur le site) :
```bash
git pull --rebase && git push
```

### La croix rouge dans l'onglet Actions
Cliquez dessus, puis sur l'étape en rouge : le message est le même que dans votre terminal.
