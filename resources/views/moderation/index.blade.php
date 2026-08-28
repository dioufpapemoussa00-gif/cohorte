@extends('layouts.app')

@section('titre', 'File de modération')

@section('contenu')
    <h1>File de modération</h1>

    @forelse ($publications as $publication)
        <article class="carte">
            <p>{{ Str::limit($publication->contenu, 200) }}</p>
            <footer>
                Par {{ $publication->auteur->name }}
                — Statut : {{ $publication->statut }}
                — {{ $publication->signalements_count }} signalement(s)
            </footer>

            <form method="POST" action="{{ route('moderation.update', $publication) }}">
                @csrf
                @method('PUT')
                <button type="submit" name="decision" value="valider">Valider</button>
                <button type="submit" name="decision" value="refuser">Refuser</button>
            </form>
        </article>
    @empty
        <p class="vide">Aucune publication en attente de modération.</p>
    @endforelse

    {{ $publications->links() }}
@endsection