@extends('layouts.app')

@section('titre', 'Rejoindre une promotion')

@section('contenu')
    <h1>Rejoindre votre promotion</h1>

    <p>Saisissez le code d'invitation qui vous a été communiqué.</p>

    <form method="POST" action="{{ route('promotion.adherer') }}">
        @csrf

        <label for="code_invitation">Code d'invitation</label>
        <input id="code_invitation" type="text" name="code_invitation"
               value="{{ old('code_invitation') }}" required
               placeholder="ex. DWA2026">

        <button type="submit">Rejoindre</button>
    </form>
@endsection