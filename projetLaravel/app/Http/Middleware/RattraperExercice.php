<?php

namespace App\Http\Middleware;

use App\Exercice\Moteur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FOURNI — Avant chaque requête, l'exercice « rattrape » les injects dont l'heure est passée.
 */
class RattraperExercice
{
    public function handle(Request $request, Closure $next): Response
    {
        Moteur::rattraper();

        return $next($request);
    }
}
