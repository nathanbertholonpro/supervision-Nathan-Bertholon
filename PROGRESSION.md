# 📋 Ma progression — Centre de supervision

> Cochez (`[ ]` → `[x]`) au fur et à mesure, puis **committez** ce fichier.
> Une mission 🟢 ou 🟡 n'est cochée que si ses tests passent ; une ligne 🧠 que si vous savez l'expliquer
> **sans regarder** (c'est le contrôle d'entrée suivant).

**Nom :** · **Dépôt :**

---

## 🚀 Mise en route
- [ ] Dépôt créé depuis le modèle, Codespace ouvert
- [ ] `./lancer.sh` : l'application s'affiche (port 8000), l'horloge avance
- [ ] `php artisan test --group=socle` : 9 tests verts
- [ ] 📖 [Les documents de la supervision](docs/documents-a-produire.md) et les § 1-2 du [droit : NIS 2 et DORA](docs/cadre-juridique.md)
- [ ] 🎬 La [vidéo de présentation](docs/video/README.md)

## 1️⃣ Moniteur 1 : les services et leur GTR
- [ ] 📖 Fiche [Supervision, GTR et MVC Laravel](cours/01-supervision-mvc.md) + auto-évaluation
- [ ] 🟢 1.1 6 réponses écrites : le trajet d'un événement
- [ ] 🟢 1.2 RG2 : les services dans l'ordre de rétablissement
- [ ] 🟡 1.3 RG1 : indisponibilité, GTR, jauge et niveau
- [ ] 🟡 1.4 une tuile par état, avec son nombre de services
- [ ] 🔴 1.5 le filtre par client `?client=CFU`
- [ ] ✅ Groupe seance-01 vert
- [ ] 🧠 Je sais expliquer sans regarder : Supervision · GTR · Règle de gestion isolée · Tri à plusieurs clés · Route → contrôleur → vue

## 2️⃣ Les dépendances et l'historique
- [ ] 📖 Fiche [Association porteuse et reprise de données](cours/02-association-reprise.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 2.1 l'association porteuse `service_composant` (avec son `mode`) et les relations
- [ ] 🟢 2.2 la **reprise** : les dépendances texte deviennent des lignes, sans rien perdre
- [ ] 🟡 2.3 la fiche d'un service : composants, mode, prestataire, état
- [ ] 🟡 2.4 l'historique des états
- [ ] 🔴 2.5 « si ce composant tombe » : `/composants/SAN-A/impact`
- [ ] ✅ Groupe seance-02 vert
- [ ] 🧠 Je sais expliquer sans regarder : Association porteuse · Migration de reprise · Base en service · Historique · `withPivot`

## 3️⃣ Moniteur 2 : le journal d'incident
- [ ] 📖 Fiche [Journal d'incident, formulaires et validation](cours/03-journal-validation.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 3.1 le moniteur 2 : le journal, le plus récent en haut
- [ ] 🟢 3.2 écrire au journal : formulaire, validation, heure de l'exercice
- [ ] 🟡 3.3 RG4 : entrées immuables, corrections par ajout
- [ ] 🟡 3.4 filtrer par catégorie, avec les compteurs
- [ ] 🔴 3.5 chercher un mot : `?q=ardoise`
- [ ] ✅ Groupe seance-03 vert
- [ ] 🧠 Je sais expliquer sans regarder : Journal d'incident · Form Request · Jeton CSRF · Immuabilité · Correction par ajout

## 4️⃣ Incident important (NIS 2) et moniteur 3 : les notifications
- [ ] 📖 Fiche [Incident important, notifications, transaction](cours/04-nis2-notifications-transaction.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 4.1 RG5 : les critères NIS 2 ; RG6 : échéance, prérequis, statut ; les critères au moniteur 2
- [ ] 🟢 4.2 le moniteur 3 et le bouton « Envoyée »
- [ ] 🟡 4.3 les refus : 409 avec un message, 404
- [ ] 🟡 4.4 tout ou rien : transaction et verrou
- [ ] 🔴 4.5 les notifications en JSON : `GET /api/notifications`
- [ ] ✅ Groupe seance-04 vert
- [ ] 🧠 Je sais expliquer sans regarder : Incident important (NIS 2) · Alerte précoce et notification · Code 409 · Transaction · Verrou (`lockForUpdate`)

## 5️⃣ Les quatre moniteurs synchronisés
- [ ] ✍️ Interrogation écrite n° 1 (séances 1 à 4)
- [ ] 📖 Fiche [API, ETag et communication entre fenêtres](cours/05-api-etag-fenetres.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 5.1 l'API d'état : heure, version, ETag, 304
- [ ] 🟢 5.2 des moniteurs qui écoutent au lieu de se recharger
- [ ] 🟡 5.3 l'API du poste : l'adresse de chaque moniteur
- [ ] 🟡 5.4 RG7 : je clique sur le 1, le 3 suit
- [ ] 🔴 5.5 le lanceur qui place les quatre fenêtres : `/poste`
- [ ] ✅ Groupe seance-05 vert
- [ ] 🧠 Je sais expliquer sans regarder : API JSON · ETag et 304 · Rafraîchissement par zones · BroadcastChannel · Poste

## 6️⃣ Sécurité et rôles : qui a le droit de notifier qui ?
- [ ] 📖 Fiche [Authentification, autorisation, traçabilité](cours/06-securite-roles.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 6.1 les comptes (rôle, portefeuille), la connexion, le message neutre
- [ ] 🟢 6.2 toute l'application protégée ; 401 pour l'API ; déconnexion
- [ ] 🟡 6.3 RG8 : chacun son rôle (Gates), 403
- [ ] 🟡 6.4 signé par la personne connectée
- [ ] 🔴 6.5 freiner la force brute
- [ ] ✅ Groupe seance-06 vert et toutes les séances précédentes vertes
- [ ] 🧠 Je sais expliquer sans regarder : Hachage · Message neutre · Authentification / autorisation · Gate · Responsabilité de la direction

## 7️⃣ Moniteur 4 : tiers, clients financiers (DORA) et registre
- [ ] 📖 Fiche [DORA : classification, registre et indicateurs](cours/07-dora-registre-indicateurs.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟢 7.1 le moniteur 4 : composants, état, prestataire
- [ ] 🟡 7.2 RG9 : l'historique, l'évaluation DORA de chaque client financier
- [ ] 🟡 7.3 RG10 : le registre des contrats, la concentration
- [ ] 🟢 7.4 les indicateurs
- [ ] 🔴 7.5 le fil des alertes, la plus grave d'abord
- [ ] 📂 Alerte précoce à l'ANSSI (`CARNET.md`)
- [ ] 📂 Information de PilatPay (`CARNET.md`)
- [ ] ✅ Groupe seance-07 vert
- [ ] 🧠 Je sais expliquer sans regarder : Incident majeur (DORA) · Fonction critique ou importante · Registre d'informations · Concentration · Indicateur

## 8️⃣ L'exercice : audit, tests, retour d'expérience
- [ ] 📖 ⚖️ [Le droit : NIS 2 et DORA](docs/cadre-juridique.md) relu en entier, auto-évaluation faite
- [ ] ✍️ Interrogation écrite n° 2 (séances 5 à 7)
- [ ] 📖 Fiche [Audit, signature HMAC et retour d'expérience](cours/08-audit-signature-retex.md) + auto-évaluation
- [ ] ✏️ *2 · Comprendre* : réponses dans `CARNET.md`
- [ ] 🟡 8.1 la faille : trouvée, prouvée, corrigée, expliquée
- [ ] 🟢 8.2 au moins deux tests à vous, vérifiés en cassant le code
- [ ] 🟢 8.3 le style du code sans écart
- [ ] 🟡 8.4 sonde de santé, pages d'erreur, retour d'expérience, recette
- [ ] 🟢 8.5 la fiche de réalisation E5
- [ ] 📂 Projet de rapport final à l'ANSSI (`CARNET.md`)
- [ ] 📂 Rapport d'exercice, une page (`CARNET.md`)
- [ ] ✅ Groupe seance-08 vert, toutes les lignes vertes dans **Actions**
- [ ] 🧠 Je sais expliquer sans regarder : Contrôle d'accès et intégrité · Signature HMAC · Test qui protège · Sonde de santé · Exercice et RETEX

---
📖 lire · ✏️ faire · 🧠 retenir · ⚖️ le droit · 📂 support · 🟢 guidé · 🟡 semi-guidé · 🔴 autonome · ✅ tests · ✍️ interrogation
