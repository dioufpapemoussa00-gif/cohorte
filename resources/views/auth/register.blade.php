@extends('layouts.app')

@section('titre', 'Créer un compte')

@section('contenu')
    <h1>Rejoindre Cohorte</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Nom complet</label>
        <input id="name" type="text" name="name"
               value="{{ old('name') }}" required autofocus>

        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email"
               value="{{ old('email') }}" required>

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required>

        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <label for="code_invitation">Code d'invitation de votre promotion</label>
        <input id="code_invitation" type="text" name="code_invitation"
               value="{{ old('code_invitation') }}" required
               placeholder="ex. DWA2026">
        @error('code_invitation')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <button type="submit">Créer mon compte</button>
    </form>

    <p>
        <a href="{{ route('login') }}">Déjà inscrit ? Se connecter</a>
    </p>
@endsection