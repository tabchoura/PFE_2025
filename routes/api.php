<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\CandidatureController;

use App\Http\Controllers\ImageController;
use App\Models\Candidature;
use App\Jobs\EvaluateCvEmbedding;
use App\Services\LocalEmbeddingService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::get('offres/titres', [OfferController::class, 'titles']);
Route::get('/offres', [OfferController::class, 'index']);



Route::get('/embed', function () {
    return response()->json(['status' => 'ok']);
});



Route::middleware('auth:sanctum')->group(function () {

    // ─── Utilisateur ──────────────────────────────────────────────────────
   
    Route::post('/logout',       [AuthController::class, 'logout']);
    Route::put('/update-profil', [UserController::class, 'updateMyprofile']);
    Route::get('/me',            [UserController::class, 'myProfile']);



    // ─── Offres (CRUD privé) ───────────────────────────────────────────────

    Route::post(  '/offres',      [OfferController::class, 'store']);
    Route::get(   '/offres/{id}', [OfferController::class, 'show']);
    Route::put(   '/offres/{id}', [OfferController::class, 'update']);
    Route::delete('/offres/{id}', [OfferController::class, 'destroy']);


      // ─── CV ─────────────────────────────────────────────────────────────────
    Route::apiResource('cv', CvController::class);

      
    // ─── Images ─────────────────────────────────────────────────────────────
    Route::post('/ajouterimage', [ImageController::class, 'uploadImage']);


    // ─── Postuler à une offre ─────────────────────────────────────────────


    Route::post('/offres/{id}/postuler', [CandidatureController::class, 'postuler']); 
Route::post('/offreenregistrer/{offer_id}', [OfferController::class, 'enregistrerOffre']);
Route::get('/mesoffres', [OfferController::class, 'Mesoffres']);
Route::delete('/offreenregistrer/{offer_id}', [OfferController::class, 'deselectionnerOffre']);



    // ─── Candidatures ──────────────────────────────────────────────────────

    // Route::put('/candidatures/{id}/accept', [CandidatureController::class, 'accept'])
    //      ->name('candidatures.accept');
    // Route::put('/candidatures/{id}/refuse', [CandidatureController::class, 'refuse'])
    //      ->name('candidatures.refuse');

    Route::apiResource('candidatures', CandidatureController::class);
    Route::get('/mescandidatures', [CandidatureController::class, 'mescandidatures']);

  

    // ─── Entretiens ─────────────────────────────────────────────────────────
Route::post('/candidatures/{candidatureId}/entretien', [CandidatureController::class, 'envoyerEntretien']);

 
  
 
    // ─── Test accès candidat ────────────────────────────────────────────────
    Route::middleware('role:candidat')
         ->get('/test-candidat', fn() => response()->json(['message' => '✅ Accès autorisé pour le candidat.']));
});
