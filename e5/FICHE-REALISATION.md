# 🎓 Fiche de réalisation — Centre de supervision NIS 2 · DORA

> Cette fiche prépare la ligne « Centre de supervision » de votre **tableau de synthèse** (épreuve E5, rubrique
> *Réalisation en cours de formation*) et les pages correspondantes de votre portfolio.
> Ne cochez une compétence que si vous pouvez **montrer** ce que vous avez fait.

| | |
|---|---|
| **Intitulé** | Centre de supervision — application de suivi des services, des incidents et des notifications NIS 2 / DORA sur poste à 4 moniteurs (Laravel) |
| **Contexte** | Situation professionnelle en formation, BTS SIO 2ᵉ année, option SLAM — organisation fictive du secteur numérique (infogéreur-hébergeur, clients financiers soumis à DORA) |
| **Période** | du __/__/__ au __/__/__ |
| **Dépôt** | https://github.com/… |
| **Technologies** | PHP 8.4, Laravel 13 (Eloquent, Blade, Form Requests, Gates), MySQL 8.4, JavaScript (fetch, BroadcastChannel), HMAC-SHA256, PHPUnit, Pint, GitHub Actions, Codespaces |

## Ce que j'ai réalisé

*En 5 à 8 lignes, à la première personne : la demande, ce que vous avez livré, une difficulté et comment vous l'avez résolue.*

## Compétences du tableau de synthèse

| Compétence | Ce que je peux montrer | Séances | ☑ |
|---|---|---|---|
| **Gérer le patrimoine informatique** — *mettre en place et vérifier les niveaux d'habilitation associés à un service* | connexion, groupe `auth`, rôles et portefeuilles (Gates), 401 / 302 / 403, mots de passe hachés | 6, 8 | ☐ |
| **Gérer le patrimoine informatique** — *recenser et identifier les ressources numériques* | cartographie des services et de leurs composants, prestataires, registre des contrats, concentration | 2, 7 | ☐ |
| **Répondre aux incidents et aux demandes d'assistance et d'évolution** — *traiter des demandes concernant les applications* | chaque séance part d'une demande ; qualification d'incident (NIS 2) ; la faille du webhook corrigée | 1 à 8 | ☐ |
| **Développer la présence en ligne de l'organisation** — *participer à l'évolution d'un site Web exploitant les données de l'organisation* | quatre moniteurs alimentés par les données de supervision, API d'état, synchronisation | 1, 3, 4, 5, 7 | ☐ |
| **Travailler en mode projet** — *planifier les activités ; évaluer les indicateurs de suivi* | `PROGRESSION.md`, journal de séance, historique Git, tests verts par séance, indicateurs de l'exercice | 1 à 8 | ☐ |
| **Mettre à disposition des utilisateurs un service informatique** — *réaliser les tests d'intégration et d'acceptation ; déployer un service* | tests automatisés et mes propres tests, CI, recette avec la demandeuse, mode production, sonde de santé | 8 | ☐ |
| **Organiser son développement professionnel** — *mettre en œuvre des outils et stratégies de veille* | NIS 2 (directive, règlement 2024/2690, transposition française en cours), DORA (règlements 2024/1772 et 2025/301), OWASP Top 10, documentation Laravel | 1 à 8 | ☐ |

## Et pour le bloc 3 (cybersécurité des services informatiques)

| Compétence | Ce que je peux montrer | Séances |
|---|---|---|
| *Garantir la disponibilité, l'intégrité et la confidentialité des services informatiques et des données de l'organisation face à des cyberattaques* | supervision de la GTR, qualification et notification des incidents (NIS 2), journal immuable et signé, aide à la classification DORA des clients | 1 à 7 |
| *Assurer la cybersécurité d'une solution applicative et de son développement* | l'audit OWASP, la faille A01/A08 trouvée, prouvée, corrigée par une signature HMAC, et verrouillée par un test | 6, 8 |
| *Protéger les données à caractère personnel* | rôle de sous-traitant (RGPD, art. 28 et 33.2) ; droits par rôle et par portefeuille ; aucune donnée métier dans la sonde de santé | 6, 8 |

## Preuves à joindre au portfolio

- [ ] Capture : les quatre moniteurs ouverts sur le poste (ou en mosaïque), pendant l'exercice
- [ ] Capture : l'onglet **Actions** avec les séances au vert
- [ ] Extrait de code commenté : les critères NIS 2 (RG5) **ou** l'envoi d'une notification sous transaction et verrou **ou** l'évaluation DORA (RG9)
- [ ] La faille : comment vous l'avez trouvée (`route:list -v`), prouvée, corrigée (signature HMAC), et le test qui la verrouille
- [ ] La page de retour d'expérience après un exercice joué, et trois propositions
- [ ] Les supports : l'alerte précoce à l'ANSSI, l'information de PilatPay, le projet de rapport final
