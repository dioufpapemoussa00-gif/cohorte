<?php

use App\Http\Controllers\Entraide\QuestionController;
use App\Http\Controllers\Entraide\ReponseController;
use App\Http\Controllers\Entraide\ReponseRetenueController;
use App\Http\Controllers\Feed\PublicationController;
use App\Http\Controllers\Profil\ProfilController;
use App\Http\Controllers\Promotion\AdhesionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('accueil'))->name('accueil');

Route::middleware('auth')->group(function () {
    Route::get('/rejoindre', [AdhesionController::class, 'create'])->name('promotion.rejoindre');
    Route::post('/rejoindre', [AdhesionController::class, 'store'])->name('promotion.adherer');

    Route::middleware('promotion')->group(function () {
        Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');

        Route::resource('publications', PublicationController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::resource('questions', QuestionController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::post('questions/{question}/reponses', [ReponseController::class, 'store'])
            ->name('reponses.store');

        Route::post('questions/{question}/reponse-retenue', [ReponseRetenueController::class, 'store'])
            ->name('reponse-retenue.store');
    });
});