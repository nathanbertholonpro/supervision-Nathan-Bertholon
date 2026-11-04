# Incident important, notifications, transaction

> **Séance 4** · Lecture 10 min · ▶️ [Faire la séance](../seances/seance-04.md)

## L'idée

**NIS 2** impose à Sevranne, entité importante, de notifier à l'ANSSI tout **incident important**. « Important » ne se
devine pas : le règlement d'exécution (UE) 2024/2690 donne des critères — il suffit d'en atteindre un. Une fois
l'incident qualifié, trois **horloges** démarrent ; et chaque contrat client en ajoute une, qui part de la détection.

```mermaid
timeline
    title Mardi 9 mars 2027
    09:18 : détection · horloges des contrats (PilatPay + 30 min)
    09:48 : 30 min d'indisponibilité totale · critère atteint
    09:50 : qualification · horloges NIS 2
    mer. 09:50 : alerte précoce (24 h)
    ven. 09:50 : notification (72 h)
    + 1 mois : rapport final (après la notification)
```

Une notification se fait **dans l'ordre** (pas de notification avant l'alerte précoce) et **une seule fois**. Quand
la règle refuse, elle lève une **exception métier** ; le contrôleur répond **409** (*Conflict* : la demande est bien
formée mais contredit l'état). Et l'envoi + l'entrée au journal se font **ensemble ou pas du tout** : une
**transaction**, avec un **verrou** pour que deux clics simultanés ne fassent qu'un envoi.

> ⚖️ Les critères et les délais : [le droit, § 3](../docs/cadre-juridique.md#3-quand-un-incident-est-il--important--et-que-faut-il-notifier--nis-2).

## Le code à reconnaître

```php
'nis2' => $exercice->important_depuis?->addMinutes($notification->delai_minutes),   // null tant que non qualifié
'nis2_final' => $notification->dependDe?->envoyee_a?->addMonth(),
```
```php
return DB::transaction(function () use ($notification, $auteur, $heure) {
    $n = Notification::whereKey($notification->id)->lockForUpdate()->firstOrFail();
    if ($n->estEnvoyee()) {
        throw new RegleNonRespectee("La notification {$n->code} est déjà envoyée.");
    }
    // prérequis, update, Journal::create(...)
});
```

## Les pièges

- **Compter les 24 h depuis la détection** : elles partent de la **qualification** d'incident important.
- **`>= 30` au lieu de `> 30`** : « plus de 30 minutes » ; à 30 minutes pile, le critère n'est pas atteint.
- **Oublier que la colocation n'attend pas** : un service de centre de données totalement indisponible compte dès la coupure (art. 8).
- **Dire « bloquée » à une notification échue** : le retard prime.
- **Relire sans `lockForUpdate()`**, ou une transaction qui n'englobe pas l'écriture au journal.

## ✅ Auto-évaluation

1. Citez trois critères d'incident important retenus pour Sevranne.
2. Qualification à 09:50 : échéances de l'alerte précoce et de la notification ?
3. Notification envoyée le jeudi 11 mars à 15:00 : échéance du rapport final ?
4. Contrat de 1 h, détection à 09:18 : à partir de quand le statut est-il « proche » ?
5. Pourquoi 409 et pas 400 quand l'alerte précoce n'est pas partie ?

<details><summary>Réponses</summary>

1. Service géré totalement indisponible plus de 30 min (ou service de colocation coupé) ; disponibilité limitée plus d'une heure ; action malveillante suspectée ; pertes directes au-delà de 500 000 € ou 5 % du CA.
2. Mercredi 10 mars 09:50 ; vendredi 12 mars 09:50.
3. Le dimanche 11 avril à 15:00 (un mois après).
4. Quand il reste un quart du délai ou moins : à 10:03 (échéance 10:18).
5. La demande est bien formée ; c'est l'état (prérequis non envoyé) qui l'empêche : *Conflict*.
</details>
