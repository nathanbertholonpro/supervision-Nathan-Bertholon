# 🎬 Vidéo de présentation — comprendre le contexte en 8 minutes

Une vidéo à regarder **avant la séance 1** : l'hébergeur et ses clients, la supervision, NIS 2 et DORA, le poste à
quatre écrans, le scénario de l'exercice, ce que vous allez construire et comment travailler.

> **Lien de la vidéo :** *(à venir — l'enseignant le publie ici)*

Les fichiers de ce dossier permettent de la produire avec **NotebookLM** (Google), qui fabrique une vidéo commentée à
partir de documents.

| Fichier | Rôle |
|---|---|
| [source-contexte.md](source-contexte.md) | le contexte raconté sans code : la source principale |
| [script.md](script.md) | le découpage en 10 scènes, avec le texte de la voix et l'image : ce que la vidéo doit suivre |

---

## Produire la vidéo avec NotebookLM

1. **Créer un notebook** sur [notebooklm.google.com](https://notebooklm.google.com), nommé « Centre de supervision — présentation ».
2. **Ajouter les sources**, en important les fichiers (ou en collant leur texte) :
   - `docs/video/source-contexte.md` — **indispensable** ;
   - `docs/video/script.md` — pour que la vidéo suive le découpage ;
   - `docs/cahier-des-charges.md` et `docs/cadre-juridique.md` — facultatifs, pour le vocabulaire et les délais exacts ;
   - les captures de `docs/maquettes/` — facultatif, si votre NotebookLM accepte les images.
3. Dans le panneau **Studio**, choisir **Video Overview** (*Aperçu vidéo*), puis le crayon pour le personnaliser :
   - **Format :** *Explainer* ; **Langue :** français ;
   - **Style visuel :** *Whiteboard* ou *Classic*. Avec *Custom* : « schémas sobres, tons sombres et bleu canard,
     écrans de supervision stylisés, horloges » ;
   - **Sujet à mettre en avant :** coller la consigne ci-dessous.
4. **Générer**, regarder **en entier**, puis vérifier avant de diffuser (voir plus bas).
5. **Télécharger** la vidéo, ou partager le notebook en lecture, et mettre le lien en haut de cette page.

### La consigne à coller

```text
Vidéo de présentation pour des étudiants de BTS SIO SLAM (2e année), en français, ton clair et
concret. Suis le découpage en 10 scènes du document « Script ». Explique : Sevranne Datacenter,
infogéreur-hébergeur, et ses clients dont trois entités financières ; la supervision et la GTR ;
NIS 2 (Sevranne, entité importante, notifie l'ANSSI : 24 h, 72 h, un mois) et DORA (le client
financier classe l'incident et notifie l'ACPR en moins de 24 h) ; l'incident du 9 mars 2027 venu
d'un prestataire ; pourquoi le centre a besoin de 4 écrans (une question par écran) ; l'outil
laissé par un développeur ; les 8 demandes, une par séance ; les repères Lire, Faire, Retenir,
Pourquoi, Droit, Support ; les tests qui passent du rouge au vert. Termine par la première chose
à faire. Ne montre aucun code. L'organisation et les personnes sont fictives : n'invente ni
marque, ni logo, ni personne réelle.
```

### Une version courte (60 secondes)

```text
En 60 secondes, en français, pour des étudiants de BTS SIO : un hébergeur de la Loire voit tomber
sept services, dont la passerelle de paiement d'un client financier. NIS 2 l'oblige à alerter
l'ANSSI en 24 heures ; ses clients bancaires ont 24 heures pour prévenir leur autorité (DORA).
Personne n'a vu les délais passer. L'étudiant reprend l'outil de supervision à 4 écrans et le
livre en 8 séances. Organisation fictive, aucun code.
```

---

## Avant de diffuser : vérifier

NotebookLM peut se tromper ou ajouter des détails. Contrôler au moins :

- [ ] les chiffres : 180 salariés, 140 clients, deux centres de données (DC1, DC2), exercice du mardi 9 mars 2027 de 9 h à 13 h, 7 services tombés à 9 h 18 ;
- [ ] les noms : Sevranne Datacenter, Hélène Vartan, Karim Belaïd, Crédit Furanais, PilatPay, Mutuelle Forézienne d'Assurance, Ardoise Stockage — et **aucune** entreprise, marque ou personne réelle ;
- [ ] NIS 2 : Sevranne est une entité **importante** ; alerte précoce **24 h**, notification **72 h**, rapport final **un mois**, à partir de la **qualification** de l'incident important (9 h 50), pas de la panne ;
- [ ] DORA : c'est le **client financier** qui classe l'incident et notifie l'ACPR (4 h après la classification, au plus tard 24 h) ; Sevranne lui donne les éléments et le prévient dans le délai de son contrat ;
- [ ] la place des écrans : services en haut à gauche, incident en haut à droite, notifications en bas à gauche, tiers et clients en bas à droite ;
- [ ] les huit demandes dans le bon ordre ;
- [ ] la mention que l'organisation et l'incident sont **fictifs**, et que les textes cités sont réels.

*Remarques.* Les styles visuels sont réservés aux comptes de plus de 18 ans : c'est l'enseignant qui génère la vidéo et
la partage. Les options de NotebookLM évoluent ; le principe reste : des sources, un format, une langue, une consigne.
