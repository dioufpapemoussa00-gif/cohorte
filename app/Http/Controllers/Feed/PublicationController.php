<?php

namespace App\Http\Controllers\Feed;

use App\Enums\VerdictModeration;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Models\Publication;
use App\Services\ServiceModeration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Publication::class);

        $publications = Publication::query()
            ->posts()
            ->visibles()
            ->deLaPromotion($request->user()->promotion_id)
            ->with('auteur')
            ->withCount('signalements')
            ->orderByDesc('epingle_le')
            ->latest()
            ->paginate(15);

        return view('feed.index', compact('publications'));
    }

    public function create(): View
    {
        $this->authorize('create', Publication::class);

        return view('feed.create');
    }

    public function store(StorePublicationRequest $request, ServiceModeration $moderation): RedirectResponse
    {
        $this->authorize('create', Publication::class);

        $verdict = $moderation->evaluer($request->validated()['contenu'], $request->user());

        $publication = Publication::create([
            ...$request->validated(),
            'type' => 'post',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => $verdict->statutPublication(),
            'motif_moderation' => $verdict->value,
        ]);

        $message = match ($verdict) {
            VerdictModeration::Acceptable => 'Votre publication est en ligne.',
            VerdictModeration::Inacceptable => 'Votre publication a été refusée par la modération.',
            default => 'Votre publication est en attente de validation par un délégué.',
        };

        return redirect()
            ->route('publications.index')
            ->with($verdict === VerdictModeration::Inacceptable ? 'erreur' : 'succes', $message);
    }

    public function show(Publication $publication): View
    {
        $this->authorize('view', $publication);

        $publication->load('auteur', 'reponses.auteur');

        return view('feed.show', compact('publication'));
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $this->authorize('delete', $publication);

        $publication->delete();

        return redirect()
            ->route('publications.index')
            ->with('succes', 'Publication supprimée.');
    }
}