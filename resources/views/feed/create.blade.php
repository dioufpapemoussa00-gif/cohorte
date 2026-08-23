@extends('layouts.app')

@section('titre', 'Nouvelle publication')

@section('contenu')
    <h1>Nouvelle publication</h1>

    <form method="POST" action="{{ route('publications.store') }}">
        @csrf

        <label for="titre">Titre (facultatif)</label>
        <input id="titre" type="text" name="titre" value="{{ old('titre') }}">

        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="6" required>{{ old('contenu') }}</textarea>

        <button type="submit">Publier</button>
    </form>
@endsection