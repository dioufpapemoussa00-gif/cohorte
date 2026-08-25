@extends('layouts.app')

@section('titre', 'Entraide')

@section('contenu')
    <div class="entete-fil">
        <h1>Entraide — {{ auth()->user()->promotion->nom }}</h1>
        <a href="{{ route('questions.create') }}" class="bouton">Poser une question</a>
    </div>

    @forelse ($questions as $question)
        <article class="carte">
            <h3><a href="{{ route('questions.show', $question) }}">{{ $question->titre }}</a></h3>
            <p>{{ Str::limit($question->contenu, 150) }}</p>
            <footer>
                Posée par {{ $question->auteur->name }}
                @if ($question->reponse_retenue_id)
                    — <strong>Résolue</strong>
                @endif
            </footer>
        </article>
    @empty
        <p class="vide">Aucune question pour l'instant.</p>
    @endforelse

    {{ $questions->links() }}
@endsection