@extends('layouts.app')

@section('titre', 'Poser une question')

@section('contenu')
    <h1>Poser une question</h1>

    @if (isset($similaires) && count($similaires) > 0)
        <div class="alerte">
            <p>Ces questions similaires existent peut-être déjà :</p>
            <ul>
                @foreach ($similaires as $similaire)
                    <li><a href="{{ route('questions.show', $similaire) }}">{{ $similaire->titre }}</a></li>
                @endforeach
            </ul>
        </div>

        <form method="POST" action="{{ route('questions.store') }}">
            @csrf
            <input type="hidden" name="doublon_verifie" value="1">
            <input type="hidden" name="titre" value="{{ $donnees['titre'] }}">
            <input type="hidden" name="contenu" value="{{ $donnees['contenu'] }}">
            <button type="submit">Publier quand même</button>
        </form>
    @else
        <form method="POST" action="{{ route('questions.store') }}">
            @csrf

            <label for="titre">Titre de la question</label>
            <input id="titre" type="text" name="titre" value="{{ old('titre') }}" required>

            <label for="contenu">Détails</label>
            <textarea id="contenu" name="contenu" rows="6" required>{{ old('contenu') }}</textarea>

            <button type="submit">Publier la question</button>
        </form>
    @endif
@endsection