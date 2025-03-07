<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\AuthController;

/*
|---------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour les utilisateurs (admin, recruteur, candidat)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Routes pour les utilisateurs avec un rôle spécifique
Route::middleware('role:admin')->post('/admin/login', [AuthController::class, 'login']);
Route::middleware('role:admin')->post('/admin/register', [AuthController::class, 'register']);
Route::middleware('role:admin')->post('/admin/logout', [AuthController::class, 'logout']);

Route::middleware('role:recruteur')->post('/recruteur/login', [AuthController::class, 'login']);
Route::middleware('role:recruteur')->post('/recruteur/register', [AuthController::class, 'register']);
Route::middleware('role:recruteur')->post('/recruteur/logout', [AuthController::class, 'logout']);

Route::middleware('role:candidat')->post('/candidat/login', [AuthController::class, 'login']);
// Routes pour les utilisateurs (admin, recruteur, candidat)
Route::post('/candidat/register', [AuthController::class, 'register']);
Route::middleware('role:candidat')->post('/candidat/logout', [AuthController::class, 'logout']);

// Routes pour les autres ressources
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/entretiens', [EntretienController::class, 'index']);
Route::post('/entretiens', [EntretienController::class, 'store']);
Route::put('/entretiens/{id}', [EntretienController::class, 'update']);
Route::delete('/entretiens/{id}', [EntretienController::class, 'destroy']);

Route::get('/offres', [OfferController::class, 'index']);
Route::post('/offres', [OfferController::class, 'store']);
Route::put('/offres/{id}', [OfferController::class, 'update']);
Route::delete('/offres/{id}', [OfferController::class, 'destroy']);

Route::get('/entreprises', [EntrepriseController::class, 'index']);
Route::post('/entreprises', [EntrepriseController::class, 'store']);
Route::put('/entreprises/{id}', [EntrepriseController::class, 'update']);
Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);


Route::middleware('role:admin')->post('/admin/login', [AuthController::class, 'login']);
Route::middleware('role:recruteur')->post('/recruteur/login', [AuthController::class, 'login']);
Route::middleware('role:candidat')->post('/candidat/login', [AuthController::class, 'login']);
