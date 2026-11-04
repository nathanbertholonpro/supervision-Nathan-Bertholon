# 📂 Les documents de la supervision — et les supports que vous produisez

> 📖 **À lire avant la séance 1** (6 min). Être prêt pour NIS 2 et DORA, ce n'est pas seulement avoir un outil : c'est
> un **dossier** de procédures, de contrats et de modèles, préparé avant l'incident, suivi pendant, corrigé après.
> Votre application rend une partie de ce dossier vivante sur quatre écrans ; le reste, ce sont des écrits — dont
> quatre que vous rédigerez.

## 1. Le dossier de Sevranne

| | Document | À quoi il sert | Texte | Dans l'application | Séance |
|---|---|---|---|---|---|
| **Avant** | **Cartographie des services et des dépendances** | savoir ce qui tombe quand un composant tombe | NIS 2, art. 21 | moniteur 1, fiche service, impact d'un composant | 1, 2 |
| | **Contrats clients** (GTR, délais de notification, assistance, audit) | ce que Sevranne a promis | DORA, art. 30 ; code civil | GTR (RG1), délais (RG6), registre (RG10) | 1, 4, 7 |
| | **Contrats fournisseurs** et accès des tiers | qui peut toucher à quoi, et comment c'est contrôlé | NIS 2, art. 21.2 d | composants et prestataires | 7 |
| | **Procédure de gestion des incidents** et **de qualification** | qui décide qu'un incident est important, sur quels critères | NIS 2, art. 21.2 b et 23 ; règlement 2024/2690 | critères (RG5) | 4 |
| | **Matrice de notification** | qui notifie qui, dans quel délai, après quoi | NIS 2, art. 23 ; contrats | moniteur 3 (RG6) | 4 |
| | **Modèles de notification** | ne pas écrire à 3 h du matin une alerte à l'ANSSI en partant d'une page blanche | NIS 2, art. 23.4 | **vous** | 7, 8 |
| | **Plan de reprise** (site de secours) | rétablir les services critiques au DC2 | NIS 2, art. 21.2 c | composants `VIRT-2`, `SAUV-DC2` | 2, 7 |
| | **Organisation et rôles** | qui fait quoi pendant l'incident | NIS 2, art. 20 | rôles (RG8) | 6 |
| **Pendant** | **Journal d'incident** | ce qui s'est passé et ce qui a été décidé, daté et signé : une preuve | code civil, art. 1366 | moniteur 2 (RG4) | 3 |
| | **Tableau de bord** de supervision | ce qui est coupé, ce qui est en retard, qui est touché | — | les quatre moniteurs, le poste | 1 à 7 |
| | **Notifications** à l'ANSSI et aux clients | tenir les délais légaux et contractuels | NIS 2, art. 23 ; DORA, art. 30 | échéances au moniteur 3 ; **le texte : vous** | 4, 7 |
| | **Éléments de classification** pour les clients financiers | leur permettre de classer et de notifier l'ACPR à temps | DORA, art. 18-19 ; règlement 2024/1772 | moniteur 4 (RG9) ; **le message : vous** | 7 |
| **Après** | **Rapport final** à l'ANSSI | cause profonde, mesures, effet transfrontière | NIS 2, art. 23.4 d | **vous** (projet) | 8 |
| | **Rapport d'exercice et RETEX** | ce qui a tenu, ce qui a manqué, ce qu'on change | NIS 2, art. 21.2 f (évaluer l'efficacité des mesures) | page RETEX ; **le rapport : vous** | 8 |
| | **Registre** mis à jour | contrats, sous-traitants, concentration | DORA, art. 28-29 | moniteur 4 (RG10) | 7 |

Les obligations derrière ces documents : [⚖️ le droit de la supervision : NIS 2 et DORA](cadre-juridique.md).

## 2. Les quatre supports que vous rédigez

L'application calcule les échéances et établit les critères, mais **ne rédige rien** : en séances 7 et 8, vous
écrivez ce qu'un vrai centre de supervision aurait à envoyer, à partir de ce que montrent vos écrans. Les trames vous
attendent dans [`CARNET.md`](../CARNET.md).

| Séance | Support | Ce qu'il doit contenir | Données à prendre |
|---|---|---|---|
| 7 | 📂 **Alerte précoce à l'ANSSI** | ce qui est connu dans les 24 h (NIS 2, art. 23.4 a) : l'entité, l'heure de la qualification, ce qui se passe, les services touchés ; l'incident est-il dû à une **action malveillante** ? peut-il avoir un **effet transfrontière** ? ; un contact | moniteurs 1 et 2 ; journal à 10:10 ; PilatPay en Belgique |
| 7 | 📂 **Information d'un client financier** (PilatPay) | ce qui s'est passé et depuis quand ; le service touché et sa criticité ; **les éléments de classification DORA** que Sevranne établit (seuils atteints, ce que Sevranne ne sait pas) ; les mesures et le rétablissement prévu ; un contact. Sans jamais dire à sa place si l'incident est majeur | moniteurs 1, 3 et 4 |
| 8 | 📂 **Projet de rapport final à l'ANSSI** | description détaillée, gravité et impact (services, clients, durées), **cause profonde** (la chaîne d'approvisionnement), mesures prises et à prendre, effet transfrontière | page RETEX, journal, moniteur 4 |
| 8 | 📂 **Rapport d'exercice** (une page, pour la direction) | le déroulé en cinq lignes ; les indicateurs (GTR tenues, notifications à l'heure et en retard, clients financiers touchés) ; trois points forts, trois écarts ; trois changements, justifiés — dans l'organisation, les contrats, les procédures | page RETEX, moniteur 4 |

**Critères communs** : exact (chaque heure, chaque chiffre se retrouve à l'écran), daté, signé de votre nom et de
votre rôle, compréhensible par quelqu'un qui n'était pas là, sans jargon inutile, une page au plus. Le bon
destinataire, la bonne obligation : l'ANSSI n'attend pas ce qu'attend un client.

## 3. Ce que vous produisez, séance par séance

| Séance | ✏️ Code (vérifié par les tests) | ✏️ Écrit (`CARNET.md`) | 📂 Support |
|---|---|---|---|
| 1 | moniteur 1 : ordre de rétablissement, GTR | réponses | — |
| 2 | dépendances, reprise des données, historique | réponses | — |
| 3 | moniteur 2 : journal d'incident | réponses, dont la valeur de preuve | — |
| 4 | incident important (NIS 2), moniteur 3 : notifications | réponses, dont les échéances ANSSI | — |
| 5 | quatre moniteurs synchronisés | réponses | — |
| 6 | connexion, rôles, signature | réponses, dont la responsabilité de la direction | — |
| 7 | moniteur 4 : prestataires, aide à la classification DORA, registre, indicateurs | réponses | alerte précoce · information de PilatPay |
| 8 | faille corrigée, sonde, pages d'erreur, vos tests | grille d'audit, faille, recette | projet de rapport final · rapport d'exercice |

Et pour votre dossier : la [fiche de réalisation E5](../e5/FICHE-REALISATION.md).
