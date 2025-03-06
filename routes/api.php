<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\EntrepriseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

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


Route::get('/entreprises', action: [EntrepriseController::class, 'index']);
Route::post('/entreprises', [EntrepriseController::class, 'store']);
Route::put('/entreprises/{id}', [EntrepriseController::class, 'update']);
Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy']);

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
