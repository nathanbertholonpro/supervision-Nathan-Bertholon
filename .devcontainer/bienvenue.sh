#!/usr/bin/env bash
# Rappel affiché à chaque ouverture du Codespace.
cat <<'TXT'

  📡  Centre de supervision — Sevranne Datacenter
     Démarrer l'exercice          ./lancer.sh               (ou Ctrl+Maj+B)
     Mode recette, figé à 10 h    ./lancer.sh recette        (ou ./lancer.sh recette 12:00)
     Les adresses, sans relancer  ./lancer.sh adresses
     Tests d'une séance           php artisan test --group=seance-01
     Base de données              onglet PORTS → 8080 (phpMyAdmin)
     Un souci ?                   DEPANNAGE.md

TXT
