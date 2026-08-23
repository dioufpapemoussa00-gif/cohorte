@extends('layouts.app')

@section('titre', $publication->titre ?? 'Publication')

@section('contenu')
    <article>
        @if ($publication->titre)
            <h1>{{ $publication->titre }}</h1>
        @endif

        <p>{{ $publication->contenu }}</p>
        <footer>Publié par {{ $publication->auteur->name }}</footer>
    </article>

    @if ($publication->statut !== 'publie' && $publication->user_id === auth()->id())
        <p class="alerte">
            Cette publication n'est pas visible dans le fil (statut : {{ $publication->statut }}).
        </p>
    @endif

    <a href="{{ route('publications.index') }}">Retour au fil</a>
@endsection