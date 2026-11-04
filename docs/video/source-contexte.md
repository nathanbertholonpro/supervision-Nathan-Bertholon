# Le contexte — Centre de supervision NIS 2 · DORA

*Document source pour la vidéo de présentation. L'entreprise, ses clients, les personnes et l'incident sont fictifs ;
les notions (supervision, GTR, NIS 2, DORA, incident important, incident majeur) et les textes cités sont réels.*

## L'entreprise

Sevranne Datacenter est un infogéreur-hébergeur de la Loire. Il possède deux centres de données : le DC1, à
Andrézieux-Bouthéon, et le DC2, son site de secours, à Vénissieux. Il emploie 180 personnes et sert 140 clients. Pour
certains, il héberge simplement leurs serveurs : c'est la colocation. Pour d'autres, il exploite des services
complets : c'est l'infogérance.

Parmi ses clients, trois entités financières : le Crédit Furanais, une banque, dont Sevranne fait tourner la banque en
ligne ; PilatPay, un établissement de paiement, dont la passerelle traite les paiements de 1 200 commerçants en France
et en Belgique ; la Mutuelle Forézienne d'Assurance, dont les dossiers de sinistres sont chez Sevranne. Et d'autres :
la Clinique Saint-Lys, le cabinet comptable Valfurens Conseil, des dizaines de petites entreprises.

## La supervision et la GTR

Chaque contrat promet une GTR, une garantie de temps de rétablissement : trente minutes pour la passerelle de
PilatPay, une heure pour la banque en ligne, huit heures pour l'hébergement des petits sites. Le centre de supervision
surveille les services jour et nuit : quand plusieurs tombent ensemble, il doit savoir par quoi commencer.

## NIS 2 et DORA

Depuis la directive européenne NIS 2, un hébergeur de cette taille est une « entité importante ». Quand un incident
atteint certains critères — un service totalement coupé plus de trente minutes, une action malveillante suspectée, de
grosses pertes —, il est « important » : Sevranne doit envoyer une alerte précoce à l'ANSSI dans les 24 heures, une
notification dans les 72 heures, et un rapport final un mois plus tard. La loi française qui transpose la directive est
en cours d'adoption.

Ses clients financiers, eux, sont soumis au règlement européen DORA, applicable depuis janvier 2025. Pour chaque
incident informatique, c'est à eux de décider s'il est « majeur » et, si oui, de prévenir leur autorité, l'ACPR, au
plus tard 24 heures après en avoir eu connaissance. Pour décider, ils ont besoin des informations de leur hébergeur.
C'est pourquoi chaque contrat oblige Sevranne à les prévenir vite : trente minutes pour PilatPay.

## Ce qui s'est passé le 9 mars

Mardi 9 mars 2027. Pendant la nuit, l'équipe d'assistance de l'éditeur de la baie de stockage, Ardoise Stockage, a
installé à distance un nouveau micrologiciel. À 9 h 18, le cluster de virtualisation du DC1 perd ses disques : sept
services tombent, dont la passerelle de PilatPay et la banque en ligne du Crédit Furanais. À 9 h 50, le RSSI, Karim
Belaïd, qualifie l'incident d'important : les délais NIS 2 démarrent. À 10 h 10, le centre de sécurité découvre que le
compte d'assistance de l'éditeur sert à se connecter depuis une adresse inconnue : l'outil de l'éditeur a été piraté.
C'est une attaque par la chaîne d'approvisionnement. À partir de 10 h 20, les services critiques basculent au DC2 ; la
passerelle de PilatPay revient à 11 h 35, après plus de deux heures de coupure, pour une garantie de trente minutes.

Ce jour-là, personne n'a su dire s'il fallait prévenir l'ANSSI, ni avant quand. PilatPay a appris la panne en appelant
lui-même. Le Crédit Furanais a dû réclamer par écrit les informations pour classer l'incident.

## Le poste à quatre écrans

La directrice des opérations, Hélène Vartan, veut un outil sur le poste du centre de supervision : quatre écrans,
chacun répondant à une question.

1. **Le moniteur 1, les services**, répond à : quels services sont coupés, pour quels clients, et combien de temps
   reste-t-il avant la fin de la garantie ?
2. **Le moniteur 2, l'incident**, répond à : que s'est-il passé, et l'incident est-il important au sens de NIS 2 ? On y
   tient le journal d'incident, que personne ne peut réécrire.
3. **Le moniteur 3, les notifications**, répond à : qui prévenir — l'ANSSI, chaque client — et avant quand ? Ce sont des
   horloges, dans l'ordre, une seule fois chacune.
4. **Le moniteur 4, les tiers et les clients**, répond à : de quels prestataires dépend-on, et quels clients financiers
   sont touchés ? Pour chacun, l'outil dit quels seuils de DORA sont atteints — sans décider à sa place.

## Le point de départ : un outil laissé par un développeur

L'outil existe à moitié : un seul écran, des durées en minutes, des dépendances tapées en texte, et une porte laissée
ouverte « pour ses essais ». En huit séances, les étudiants le reprennent : ordonner les services, réparer la base sans
rien perdre, construire le journal, qualifier l'incident et tenir les horloges, synchroniser les écrans, sécuriser avec
des rôles, aider les clients financiers, et enfin préparer l'exercice — un audit, des tests, une recette, un retour
d'expérience. Ils rédigent aussi les écrits qu'un vrai centre aurait à produire : une alerte précoce à l'ANSSI, une
information pour PilatPay, un projet de rapport final, un rapport d'exercice.

## Comment travailler

Chaque séance part d'une demande, et suit quatre temps : lancer, comprendre, modifier, valider. Des tests automatiques
disent ce qui est attendu : rouges au départ, verts quand c'est fait. Six repères guident : ce qu'on lit, ce qu'on fait,
ce qu'on retient, pourquoi, le droit derrière la séance, et les écrits à produire.
