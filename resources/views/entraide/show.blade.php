@extends('layouts.app')

@section('titre', $question->titre)

@section('contenu')
    <article>
        <h1>{{ $question->titre }}</h1>
        <p>{{ $question->contenu }}</p>
        <footer>Posée par {{ $question->auteur->name }}</footer>
    </article>

    <h2>Réponses ({{ $question->reponses->count() }})</h2>

    @forelse ($question->reponses as $reponse)
        <div class="reponse @if($question->reponse_retenue_id === $reponse->id) reponse--retenue @endif">
            <p>{{ $reponse->contenu }}</p>
            <footer>
                {{ $reponse->auteur->name }}
                @if ($question->reponse_retenue_id === $reponse->id)
                    — <strong>Réponse retenue ✓</strong>
                @endif
            </footer>

            @if ($question->user_id === auth()->id() && ! $question->reponse_retenue_id)
                <form method="POST" action="{{ route('reponse-retenue.store', $question) }}">
                    @csrf
                    <input type="hidden" name="reponse_id" value="{{ $reponse->id }}">
                    <button type="submit">Retenir cette réponse</button>
                </form>
            @endif
        </div>
    @empty
        <p>Aucune réponse pour l'instant.</p>
    @endforelse

    <h3>Répondre</h3>
    <form method="POST" action="{{ route('reponses.store', $question) }}">
        @csrf
        <textarea name="contenu" rows="4" required placeholder="Votre réponse..."></textarea>
        <button type="submit">Envoyer</button>
    </form>

    <a href="{{ route('questions.index') }}">Retour aux questions</a>
@endsection