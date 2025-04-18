<?php
use App\Http\Controllers\AuhtentificationController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\AuthController;

// 🔐 Récupérer l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 🔐 Auth commun
Route::post('/login', [AuhtentificationController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 🔐 Auth par rôle (optionnel si besoin d’une route séparée par rôl


 
Route::middleware('auth:sanctum')->group(function () 
{
    Route::get('/me', [UserController::class, 'myProfile']); 
        Route::post('/logout', [AuthController::class, 'logout']); 
       // Route::get('/me', [UserController::class, 'me']);  
});
// 📦 Users
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// 📅 Entretiens
Route::get('/entretiens', [EntretienController::class, 'index']);
Route::post('/entretiens', [EntretienController::class, 'store']);
Route::put('/entretiens/{id}', [EntretienController::class, 'update']);
Route::delete('/entretiens/{id}', [EntretienController::class, 'destroy']);

// 📄 Offres
Route::get('/offres', [OfferController::class, 'index']);
Route::post('/offres', [OfferController::class, 'store']);
Route::put('/offres/{id}', [OfferController::class, 'update']);
Route::delete('/offres/{id}', [OfferController::class, 'destroy']);

// 🏢 Entreprises
Route::get('/entreprises', [EntrepriseController::class, 'index']);
Route::post('/entreprises', [EntrepriseController::class, 'store']);
Route::put('/entreprises/{id}', [EntrepriseController::class, 'update']);
Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);

// ✅ Route de test pour vérification du token et du rôle
Route::middleware(['auth:sanctum', 'role:candidat'])->get('/test-candidat', function () {
    return response()->json(['message' => '✅ Accès autorisé pour le candidat.']);
});
