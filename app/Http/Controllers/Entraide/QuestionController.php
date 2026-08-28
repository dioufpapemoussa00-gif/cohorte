<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Models\Publication;
use App\Services\ServiceDetectionDoublon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Publication::class);

        $questions = Publication::query()
            ->questions()
            ->visibles()
            ->deLaPromotion($request->user()->promotion_id)
            ->with('auteur')
            ->latest()
            ->paginate(15);

        return view('entraide.index', compact('questions'));
    }

    public function create(): View
    {
        $this->authorize('create', Publication::class);

        return view('entraide.create');
    }

    public function store(StorePublicationRequest $request, ServiceDetectionDoublon $detection): RedirectResponse|View
    {
        $this->authorize('create', Publication::class);

        $donnees = $request->validated();

        if (! $request->boolean('doublon_verifie') && $request->user()->peutAppelerIa()) {
            $similaires = $detection->chercherSimilaires($donnees['titre'] ?? '', $request->user());

            if ($similaires !== []) {
                return view('entraide.create', [
                    'similaires' => $similaires,
                    'donnees' => $donnees,
                ]);
            }
        }

        $question = Publication::create([
            ...$donnees,
            'type' => 'question',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => 'publie',
        ]);

        return redirect()
            ->route('questions.show', $question)
            ->with('succes', 'Votre question est en ligne.');
    }

    public function show(Publication $question): View
    {
        $this->authorize('view', $question);

        $question->load('auteur', 'reponses.auteur', 'reponseRetenue');

        return view('entraide.show', compact('question'));
    }
}