@extends('layouts.supervision', ['titre' => 'Moniteur 1 · Services et disponibilité'])

@push('tete')
    <meta http-equiv="refresh" content="5">
@endpush

@section('contenu')
    <h1>Services et disponibilité</h1>

    <table class="tableau">
        <thead>
            <tr><th>Code</th><th>Client</th><th>Service</th><th>Criticité</th><th>État</th><th>GTR (min)</th><th>Composants</th></tr>
        </thead>
        <tbody>
            @foreach ($services as $s)
                <tr>
                    <td class="mono">{{ $s->code }}</td>
                    <td>{{ $s->client }}</td>
                    <td>{{ $s->nom }}</td>
                    <td>{{ $s->criticite }}</td>
                    <td>{{ $s->etat }}</td>
                    <td>{{ $s->gtr_minutes }}</td>
                    <td class="mono">{{ $s->composants }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
