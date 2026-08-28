<?php

namespace App\Http\Controllers\Moderation;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Signalement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SignalementController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Publication $publication): RedirectResponse
    {
        $this->authorize('signaler', $publication);

        $donnees = $request->validate([
            'motif' => ['required', 'string', 'in:insulte,hors_sujet,publicite,autre'],
        ]);

        $dejaSignale = $publication->signalements()
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($dejaSignale) {
            return back()->with('erreur', 'Vous avez déjà signalé cette publication.');
        }

        Signalement::create([
            'publication_id' => $publication->id,
            'user_id' => $request->user()->id,
            'motif' => $donnees['motif'],
        ]);

        $this->masquerSiSeuilAtteint($publication);

        return back()->with('succes', 'Signalement enregistré. Merci.');
    }

    private function masquerSiSeuilAtteint(Publication $publication): void
    {
        $nombre = $publication->signalements()->count();

        if ($nombre >= config('cohorte.seuil_signalement') && $publication->statut === 'publie') {
            $publication->update([
                'statut' => 'masque',
                'motif_moderation' => "Masquée automatiquement après {$nombre} signalements.",
            ]);
        }
    }
}