@extends('layouts.app')

@section('titre', 'Accès refusé')

@section('contenu')
    <h1>403 — Accès refusé</h1>
    <p>Vous n'avez pas la permission d'accéder à cette page.</p>
    <a href="{{ url('/') }}">Retour à l'accueil</a>
@endsection