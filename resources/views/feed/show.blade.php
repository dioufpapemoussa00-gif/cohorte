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

    @if ($publication->user_id !== auth()->id())
        <form method="POST" action="{{ route('signalements.store', $publication) }}">
            @csrf
            <select name="motif" required>
                <option value="">-- Motif du signalement --</option>
                <option value="insulte">Insulte</option>
                <option value="hors_sujet">Hors sujet</option>
                <option value="publicite">Publicité</option>
                <option value="autre">Autre</option>
            </select>
            <button type="submit">Signaler</button>
        </form>
    @endif

    <a href="{{ route('publications.index') }}">Retour au fil</a>
@endsection