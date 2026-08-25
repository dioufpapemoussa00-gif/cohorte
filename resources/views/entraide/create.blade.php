@extends('layouts.app')

@section('titre', 'Poser une question')

@section('contenu')
    <h1>Poser une question</h1>

    <form method="POST" action="{{ route('questions.store') }}">
        @csrf

        <label for="titre">Titre de la question</label>
        <input id="titre" type="text" name="titre" value="{{ old('titre') }}" required>

        <label for="contenu">Détails</label>
        <textarea id="contenu" name="contenu" rows="6" required>{{ old('contenu') }}</textarea>

        <button type="submit">Publier la question</button>
    </form>
@endsection