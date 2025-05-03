<?php

use App\Http\Controllers\CvController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 🔐 Auth Routes (Login & Registration)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public route for viewing offer details

// Middleware for authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // User-related routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update-profil', [UserController::class, 'updateMyprofile']);
    Route::get('/me', [UserController::class, 'myProfile']);  // Get authenticated user's profile

    // Offer-related routes
    Route::get('/offres', [OfferController::class, 'index']);
    Route::post('/offres', [OfferController::class, 'store']);
    Route::put('/offres/{id}', [OfferController::class, 'update']);
    Route::delete('/offres/{id}', [OfferController::class, 'destroy']);
    Route::get('/offres/{id}', [OfferController::class, 'show']);

    // CV-related routes
    Route::apiResource('cv', CvController::class);

    // Candidature-related routes
    Route::apiResource('candidatures', CandidatureController::class);
    // Route::get('/mescandidatures', [CandidatureController::class, 'mescandidatures']);
    Route::post('/offres/{id}/postuler', [CandidatureController::class, 'postuler']); // Postuler route

    // Interview-related routes
    Route::get('/entretiens', [EntretienController::class, 'index']);
    Route::post('/entretiens', [EntretienController::class, 'store']);
    Route::put('/entretiens/{id}', [EntretienController::class, 'update']);
    Route::delete('/entretiens/{id}', [EntretienController::class, 'destroy']);

    // Entreprise-related routes
    Route::get('/entreprises', [EntrepriseController::class, 'index']);
    Route::post('/entreprises', [EntrepriseController::class, 'store']);
    Route::put('/entreprises/{id}', [EntrepriseController::class, 'update']);
    Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);

    // Image upload route
    Route::post('/ajouterimage', [ImageController::class, 'uploadImage']);

    // Test route to check token and role (candidat)
    Route::middleware(['role:candidat'])->get('/test-candidat', function () {
        return response()->json(['message' => '✅ Accès autorisé pour le candidat.']);
    });
});

// 📦 Routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cv', CvController::class);
    Route::apiResource('candidatures', CandidatureController::class);
    Route::get('/mescandidatures', [CandidatureController::class, 'mescandidatures']);
});

