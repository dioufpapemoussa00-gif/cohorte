<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Reponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReponseController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Publication $question): RedirectResponse
    {
        $this->authorize('view', $question);

        $donnees = $request->validate([
            'contenu' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Reponse::create([
            'publication_id' => $question->id,
            'user_id' => $request->user()->id,
            'contenu' => $donnees['contenu'],
        ]);

        return back()->with('succes', 'Votre réponse a été ajoutée.');
    }
}