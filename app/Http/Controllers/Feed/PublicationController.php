<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Models\Publication;
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

    public function store(StorePublicationRequest $request): RedirectResponse
    {
        $this->authorize('create', Publication::class);

        $publication = Publication::create([
            ...$request->validated(),
            'type' => 'post',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => 'publie',
        ]);

        return redirect()
            ->route('publications.show', $publication)
            ->with('succes', 'Votre publication est en ligne.');
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