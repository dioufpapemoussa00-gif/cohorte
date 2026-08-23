@extends('layouts.app')

@section('titre', 'Bienvenue sur Cohorte')

@section('contenu')
    <h1>Cohorte</h1>
    <p>Le réseau social de votre promotion.</p>

    @guest
        <p>
            <a href="{{ route('login') }}">Se connecter</a>
            —
            <a href="{{ route('register') }}">Créer un compte</a>
        </p>
    @endguest
@endsection