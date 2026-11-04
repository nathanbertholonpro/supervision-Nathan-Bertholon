# Authentification, autorisation, traçabilité

> **Séance 6** · Lecture 10 min · ▶️ [Faire la séance](../seances/seance-06.md)

## L'idée

- **Authentifier**, c'est savoir **qui** est là. On ne stocke jamais un mot de passe, même chiffré : on stocke une
  **empreinte** (bcrypt, avec un sel). À la connexion, on recalcule l'empreinte et on compare.
- **Autoriser**, c'est savoir si cette personne **a le droit**. On ferme tout par défaut (groupe `auth`), puis on ouvre
  explicitement ce qui doit l'être (la page de connexion). Les droits fins suivent le **rôle** et le **portefeuille**.
- **Tracer**, c'est pouvoir dire qui a fait quoi, quand : l'auteur vient de la **session**, jamais d'un champ du formulaire.

| Rôle | Journal | Notifier l'ANSSI | Rapport final | Notifier un client |
|---|---|---|---|---|
| direction | écrit | oui | **oui** | tous |
| RSSI | écrit | oui | non | non |
| relation client | écrit | non | non | **son portefeuille** |
| analyste | écrit | non | non | non |
| auditeur | lit | non | non | non |

> ⚖️ Pourquoi le rapport final est-il réservé à la direction ? NIS 2, art. 20 : la direction approuve les mesures de
> cybersécurité et peut en être tenue **responsable** ; ce qu'elle signe engage l'entreprise. Et la signature rend
> chaque écrit **imputable**, condition de sa valeur de preuve → [le droit, § 2](../docs/cadre-juridique.md#2-sevranne-au-regard-de-nis-2--une-entité-importante).

## Le code à reconnaître

```php
if (! Auth::attempt($identifiants)) {
    throw ValidationException::withMessages(['email' => 'Identifiant ou mot de passe incorrect.']);
}
$request->session()->regenerate();
```
```php
Gate::define('envoyer-notification', function (User $u, Notification $n) {
    return match ($n->regle) {
        'nis2_final' => $u->role === 'directeur',
        'nis2' => in_array($u->role, ['directeur', 'rssi'], true),
        default => $u->role === 'directeur'
            || ($u->role === 'relation_client' && in_array($n->client, explode(',', (string) $u->clients), true)),
    };
});
```
```php
Gate::authorize('envoyer-notification', $notification);      // 403 si refus
Notifications::envoyer($notification, $request->user()->name, Horloge::maintenant());
```

## Les pièges

- **Deux messages d'échec différents** : on apprend quels comptes existent.
- **`role` dans `$fillable`** : un formulaire pourrait faire de n'importe qui un directeur.
- **Cacher le bouton sans vérifier côté serveur** (`@can` sans `Gate::authorize`) : une requête écrite à la main passe.
- **Charger `moniteur.js` sur la page de connexion** : l'API répond 401, la page se recharge en boucle.

## ✅ Auto-évaluation

1. Que stocke la colonne `password` ?
2. Authentification ou autorisation : « Léa ne peut pas notifier la Clinique Saint-Lys » ?
3. Une page renvoie vers `/connexion`, l'API répond 401 : pourquoi cette différence ?
4. Léa Fournel envoie une notification en écrivant « Hélène Vartan » dans la requête. Qui signe ?
5. Pourquoi le rapport final à l'ANSSI est-il réservé à la direction ?

<details><summary>Réponses</summary>

1. Une empreinte bcrypt, salée : on vérifie une saisie sans pouvoir retrouver le mot de passe.
2. Autorisation : elle est authentifiée, mais ce client n'est pas dans son portefeuille (403).
3. Un humain peut remplir un formulaire ; un programme non : il lui faut un code d'erreur.
4. Léa Fournel : l'auteur vient de la session, jamais de la requête.
5. NIS 2, art. 20 : la direction approuve les mesures, les supervise, et peut en être tenue responsable.
</details>
