<?php

use App\Http\Middleware\RattraperExercice;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Codespaces (et la plupart des hébergeurs) placent un proxy HTTPS devant Apache.
        // On fait confiance à ses en-têtes X-Forwarded-* : les URL générées par
        // route() et url() restent en https:// et les formulaires ne sont pas bloqués.
        $middleware->trustProxies(at: '*');

        // FOURNI : avant chaque page, l'exercice joue les injects dont l'heure est arrivée.
        $middleware->web(append: [RattraperExercice::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
