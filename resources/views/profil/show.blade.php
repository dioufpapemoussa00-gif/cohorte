@extends('layouts.app')

@section('titre', 'Mon profil')

@section('contenu')
    <h1>{{ $utilisateur->name }}</h1>

    <p>Adresse e-mail : {{ $utilisateur->email }}</p>
    <p>Rôle : {{ $utilisateur->role }}</p>

    @if ($utilisateur->promotion)
        <p>Promotion : {{ $utilisateur->promotion->nom }}</p>
    @endif

    <p>Points de réputation : {{ $utilisateur->points }}</p>
@endsection