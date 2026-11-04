@extends('layouts.supervision', ['titre' => 'Accueil'])

@section('contenu')
    <h1>Poste du centre de supervision</h1>
    <p class="sous-titre">Quatre moniteurs pour superviser les services et tenir les délais de notification (NIS 2, DORA) pendant l'exercice du mardi 9 mars 2027.</p>

    <div class="grille-moniteurs">
        @php
            $moniteurs = [
                [1, 'moniteur.services', 'Services et disponibilité', 'Quels services sont coupés, pour quels clients, et combien de temps reste-t-il avant la fin de la GTR ?'],
                [2, 'moniteur.journal', 'Incident et qualification', 'Que s\'est-il passé, et l\'incident est-il « important » au sens de NIS 2 ?'],
                [3, 'moniteur.notifications', 'Horloges de notification', 'Qui prévenir — ANSSI, clients — et avant quand ?'],
                [4, 'moniteur.tiers', 'Tiers, clients et registre', 'De quels prestataires dépend-on, et quels clients financiers sont touchés (DORA) ?'],
            ];
        @endphp
        @foreach ($moniteurs as [$n, $route, $nom, $question])
            @if (Route::has($route))
                <a class="tuile-moniteur" href="{{ route($route) }}">
            @else
                <div class="tuile-moniteur a-venir">
            @endif
                    <span class="numero">{{ $n }}</span>
                    <strong>{{ $nom }}</strong>
                    <span>{{ $question }}</span>
                    @unless (Route::has($route)) <em>À construire</em> @endunless
            @if (Route::has($route)) </a> @else </div> @endif
        @endforeach
    </div>

    <p class="pied">{{ $nbServices }} services supervisés · {{ $nbIndisponibles }} indisponible(s) · {{ $nbEntrees }} entrée(s) au journal d'incident</p>
@endsection
