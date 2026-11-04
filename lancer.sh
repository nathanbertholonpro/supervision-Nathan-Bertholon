#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
#  📡  Démarrer l'exercice de supervision (Sevranne Datacenter)
#
#    ./lancer.sh                 base remise à neuf, exercice relancé à 09:00 (20 fois plus vite)
#    ./lancer.sh recette         tout est joué jusqu'à 10:00, puis l'horloge s'arrête (séance 8)
#    ./lancer.sh recette 12:00   … figé à l'heure choisie (09:00 à 13:00)
#    ./lancer.sh adresses        affiche seulement les adresses à ouvrir
#
#  L'application est servie en permanence par Apache (port 8000) : rien d'autre à lancer.
#  Attention : « ./lancer.sh » et « recette » repartent d'une base NEUVE (migrations + données).
#  Dans VS Code : Ctrl+Maj+B fait la même chose que ./lancer.sh
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail
cd "$(dirname "$0")/projetLaravel"
umask 0002

MODE="${1:-normal}"
HEURE="${2:-10:00}"

if [[ -n "${CODESPACE_NAME:-}" ]]; then
  URL="https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-app.github.dev}"
  PMA="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-app.github.dev}"
else
  URL="http://localhost:8000"
  PMA="http://localhost:8080"
fi

case "$MODE" in
  normal)
    php artisan exercice:demarrer
    HORLOGE="l'exercice se déroule de 09:00 à 13:00, 20 fois plus vite" ;;
  recette)
    if ! [[ "$HEURE" =~ ^(09|1[0-2]):[0-5][0-9]$|^13:00$ ]]; then
      echo "⚠️  Heure de recette entre 09:00 et 13:00, par exemple : ./lancer.sh recette 11:30"
      exit 1
    fi
    php artisan exercice:demarrer --figer="$HEURE"
    HORLOGE="figé à ${HEURE/:/ h } (mode recette)" ;;
  adresses)
    HORLOGE="adresses" ;;
  *)
    sed -n '4,8p' "$0" | sed 's/^#//'; exit 1 ;;
esac

# Vues et configuration compilées : on repart à neuf (après un kit de reprise décompressé,
# des fichiers aux dates anciennes feraient servir d'anciennes vues compilées).
[[ "$MODE" == adresses ]] || php artisan optimize:clear -q

# Apache (www-data) doit pouvoir écrire dans storage/ et bootstrap/cache/
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

cat <<TXT

  📡  Centre de supervision — $HORLOGE

     Application     $URL
     Moniteurs       $URL/moniteur/1  …  /moniteur/4   (le 1 existe ; 2, 3, 4 : séances 3, 4, 7)
     Poste 4 écrans  $URL/poste                    (séance 5)
     phpMyAdmin      $PMA
     Comptes         hvartan@sevranne.test (direction), kbelaid@sevranne.test (RSSI)…
                     mot de passe Supervision2027!   (à partir de la séance 6)

     Ctrl+clic sur une adresse pour l'ouvrir.

TXT
