# Script — vidéo de présentation « Centre de supervision » (environ 8 minutes)

*À donner comme source à NotebookLM, avec la [source de contexte](source-contexte.md). Peut aussi servir à enregistrer
la vidéo soi-même (diaporama + voix). Le texte « Voix » se lit à voix haute ; « À l'écran » décrit l'image.*

---

## Scène 1 — L'accroche (0:00 – 0:35)

**À l'écran.** Une salle de supervision la nuit ; un écran passe au rouge : « PPAY-PSP — indisponible — 09:18 ».

**Voix.** Mardi, 9 h 18. Dans un centre de données de la Loire, sept services s'arrêtent en même temps. Parmi eux, la
passerelle qui encaisse les paiements de 1 200 commerçants. Au même moment, deux horloges se mettent à tourner : celle de
la loi, et celle des contrats. Et ce jour-là, personne ne les regarde.

## Scène 2 — L'entreprise (0:35 – 1:15)

**À l'écran.** Une carte : DC1 à Andrézieux-Bouthéon, DC2 à Vénissieux ; autour, les logos fictifs des clients.

**Voix.** Sevranne Datacenter héberge les serveurs de ses clients et exploite leurs services : 180 salariés, 140 clients.
Une banque, le Crédit Furanais. Un établissement de paiement, PilatPay, en France et en Belgique. Une mutuelle. Une
clinique. Un cabinet comptable. Et pour chacun, un contrat : une garantie de temps de rétablissement, la GTR. Trente
minutes pour la passerelle de PilatPay.

## Scène 3 — Deux textes européens (1:15 – 2:20)

**À l'écran.** Deux colonnes : « NIS 2 — Sevranne → ANSSI : 24 h · 72 h · 1 mois » et « DORA — banque → ACPR : 24 h au plus ».

**Voix.** Depuis la directive NIS 2, un hébergeur de cette taille est une entité importante. Quand un incident est
important — un service coupé plus de trente minutes, une attaque suspectée —, il doit alerter l'ANSSI dans les 24
heures, lui notifier l'incident dans les 72 heures, et lui remettre un rapport final un mois plus tard. Ses clients
financiers, eux, relèvent du règlement DORA : c'est à eux de décider si l'incident est majeur, et de prévenir l'ACPR, au
plus tard 24 heures après l'avoir appris. Pour décider, il leur faut nos informations. Plus nous tardons, moins ils ont
de temps.

## Scène 4 — Ce qui s'est passé (2:20 – 3:05)

**À l'écran.** Une frise : 9 h 18 panne · 9 h 50 qualification · 10 h 10 compte d'assistance détourné · 11 h 35 PilatPay revient.

**Voix.** La nuit précédente, l'éditeur de la baie de stockage avait installé une mise à jour, à distance. À 9 h 18,
tout tombe. À 10 h 10, on découvre que son outil d'assistance a été piraté : l'attaque est venue d'un fournisseur. La
passerelle de PilatPay revient à 11 h 35 : plus de deux heures, pour trente minutes garanties. PilatPay a appris la panne
en appelant lui-même. Personne n'a su dire s'il fallait prévenir l'ANSSI, ni avant quand.

## Scène 5 — Le poste à quatre écrans (3:05 – 4:05)

**À l'écran.** Quatre moniteurs en carré ; chaque écran s'allume avec sa question.

**Voix.** Hélène Vartan, la directrice des opérations, veut un poste à quatre écrans. En haut à gauche, les services :
ce qui est coupé, pour qui, et combien de temps avant la fin de la garantie. En haut à droite, l'incident : le journal,
que personne ne peut réécrire, et les critères de NIS 2. En bas à gauche, les horloges : l'ANSSI, chaque client, avant
quand. En bas à droite, les prestataires et les clients financiers : pour chacun, les seuils de DORA atteints. Quatre
écrans, parce qu'en plein incident, il faut tout voir en même temps.

## Scène 6 — L'outil et vos huit séances (4:05 – 5:20)

**À l'écran.** Un seul écran allumé, une liste dans le désordre. Puis une frise de huit cases qui se remplissent.

**Voix.** Cet outil existe à moitié. Un développeur l'a commencé, puis il est parti : un écran, des durées en minutes, des
dépendances tapées en texte, et une porte laissée ouverte. Vous reprenez son travail. Huit séances, huit demandes.
Ordonner les services. Réparer la base sans rien perdre. Construire le journal. Qualifier l'incident et tenir les
horloges, sans jamais notifier deux fois. Synchroniser les écrans. Sécuriser, avec des rôles. Aider les clients
financiers. Et enfin, l'exercice : un audit, vos tests, la recette, le retour d'expérience.

## Scène 7 — Ce que vous écrirez (5:20 – 6:00)

**À l'écran.** Quatre feuilles se posent : « Alerte précoce — ANSSI », « Information — PilatPay », « Rapport final », « Rapport d'exercice ».

**Voix.** L'application calcule les délais ; elle n'écrit pas les messages. Vous rédigerez ce qu'un vrai centre aurait à
envoyer : l'alerte précoce à l'ANSSI, l'information de PilatPay — sans décider à sa place —, le projet de rapport final,
avec la cause profonde, et le rapport d'exercice pour la direction.

## Scène 8 — Comment travailler (6:00 – 6:55)

**À l'écran.** Un guide de séance ; six icônes : Lire, Faire, Retenir, Pourquoi, Droit, Support. Puis des tests qui passent du rouge au vert.

**Voix.** Chaque séance commence par sa feuille de route : ce qu'on lit, ce qu'on fait et où, ce qu'il faut retenir,
pourquoi, et le droit qui est derrière. Des tests automatiques décrivent ce qui est attendu : rouges au départ, verts
quand c'est fait. Votre carnet garde vos réponses ; vos commits, votre progression.

## Scène 9 — Ce que vous y gagnez (6:55 – 7:30)

**À l'écran.** Les quatre écrans complets. Puis une page « Retour d'expérience », et une fiche « E5 ».

**Voix.** À la fin, vous aurez livré un outil qu'un centre de supervision peut utiliser : fiable, sécurisé, testé. Vous
saurez faire évoluer un code et une base qui ne sont pas les vôtres, traduire un texte réglementaire en règles exactes,
et expliquer ce qu'imposent NIS 2 et DORA quand un incident survient.

## Scène 10 — Pour commencer (7:30 – 7:50)

**À l'écran.** Le dépôt GitHub ; le bouton « Use this template » ; puis le fichier « séance 1 ».

**Voix.** Pour commencer : créez votre dépôt à partir du modèle, ouvrez votre Codespace, démarrez l'exercice. Puis
ouvrez la séance 1 et lisez sa feuille de route. Mardi, 9 h : le centre de supervision compte sur vous.
