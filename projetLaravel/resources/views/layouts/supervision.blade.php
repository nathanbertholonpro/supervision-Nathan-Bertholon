<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('tete')
    <title>{{ isset($titre) ? $titre.' · ' : '' }}Centre de supervision · Sevranne Datacenter</title>
    <link rel="stylesheet" href="{{ asset('css/supervision.css') }}">
</head>
<body class="{{ $classeCorps ?? '' }}">
    <header class="bandeau">
        <a class="marque" href="{{ route('accueil') }}">
            <span class="logo">SD</span>
            <span><strong>Centre de supervision</strong><small>Sevranne Datacenter</small></span>
        </a>
        <nav class="moniteurs">
            @foreach ([[1, 'moniteur.services', 'Services'], [2, 'moniteur.journal', 'Incident'], [3, 'moniteur.notifications', 'Notifications'], [4, 'moniteur.tiers', 'Tiers et clients']] as [$n, $route, $libelle])
                @if (Route::has($route))
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'actif' : '' }}">{{ $n }} · {{ $libelle }}</a>
                @else
                    <span class="a-venir" title="À construire">{{ $n }} · {{ $libelle }}</span>
                @endif
            @endforeach
        </nav>
        <div class="horloge" data-horloge>
            <span class="exercice">{{ config('exercice.nom') }}</span>
            <strong class="mono">{{ \App\Exercice\Horloge::maintenant()->translatedFormat('D d/m H:i') }}</strong>
            <span class="mono t">{{ \App\Exercice\Horloge::libelleT() }}</span>
        </div>
        @yield('compte')
    </header>

    <main id="contenu">
        @yield('contenu')
    </main>

    @stack('scripts')
</body>
</html>
