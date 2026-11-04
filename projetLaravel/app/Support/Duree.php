<?php

namespace App\Support;

/**
 * FOURNI — Écrire une durée en minutes comme on la lit : 45 min, 1 h, 2 h 04, 3 j.
 */
class Duree
{
    public static function format(?int $minutes): string
    {
        if ($minutes === null) {
            return '—';
        }
        $signe = $minutes < 0 ? '−' : '';
        $m = abs($minutes);

        if ($m >= 1440 && $m % 1440 === 0) {
            return $signe.($m / 1440).' j';
        }
        if ($m < 60) {
            return $signe.$m.' min';
        }
        $h = intdiv($m, 60);
        $reste = $m % 60;

        return $signe.$h.' h'.($reste ? ' '.sprintf('%02d', $reste) : '');
    }
}
