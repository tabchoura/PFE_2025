<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// → Liste des offres publique (plus de 401)
Route::get('/offres', [OfferController::class, 'index']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // ─── Utilisateur ──────────────────────────────────────────────────────
    Route::get('/user',          fn(Request $req) => $req->user());
    Route::post('/logout',       [AuthController::class, 'logout']);
    Route::put('/update-profil', [UserController::class, 'updateMyprofile']);
    Route::get('/me',            [UserController::class, 'myProfile']);

    // ─── Offres (CRUD privé) ───────────────────────────────────────────────
    Route::post(  '/offres',      [OfferController::class, 'store']);
    Route::get(   '/offres/{id}', [OfferController::class, 'show']);
    Route::put(   '/offres/{id}', [OfferController::class, 'update']);
    Route::delete('/offres/{id}', [OfferController::class, 'destroy']);

    // ─── Postuler à une offre ─────────────────────────────────────────────
    Route::post('/offres/{id}/postuler', [CandidatureController::class, 'postuler']); // Postuler route

    // ─── Candidatures ──────────────────────────────────────────────────────
    Route::put('/candidatures/{id}/accept', [CandidatureController::class, 'accept'])
         ->name('candidatures.accept');
    Route::put('/candidatures/{id}/refuse', [CandidatureController::class, 'refuse'])
         ->name('candidatures.refuse');

    Route::apiResource('candidatures', CandidatureController::class);
    Route::get('/mescandidatures', [CandidatureController::class, 'mescandidatures']);

    // ─── CV ─────────────────────────────────────────────────────────────────
    Route::apiResource('cv', CvController::class);

    // ─── Entretiens ─────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
     Route::apiResource('entretiens', EntretienController::class);
 });
 
    // ─── Entreprises ────────────────────────────────────────────────────────
    Route::get(   '/entreprises',      [EntrepriseController::class, 'index']);
    Route::post(  '/entreprises',      [EntrepriseController::class, 'store']);
    Route::put(   '/entreprises/{id}', [EntrepriseController::class, 'update']);
    Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);

    // ─── Images ─────────────────────────────────────────────────────────────
    Route::post('/ajouterimage', [ImageController::class, 'uploadImage']);

    // ─── Test accès candidat ────────────────────────────────────────────────
    Route::middleware('role:candidat')
         ->get('/test-candidat', fn() => response()->json(['message' => '✅ Accès autorisé pour le candidat.']));
});
