@props(['publication'])

<article class="carte">
    <h3><a href="{{ route('publications.show', $publication) }}">{{ $publication->titre ?? 'Publication' }}</a></h3>
    <p>{{ Str::limit($publication->contenu, 200) }}</p>
    <footer>Publié par {{ $publication->auteur->name }}</footer>
</article>