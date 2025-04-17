<?php
use Illuminate\Support\Facades\Route;
Route::get('unauthorized', function () {
    return response()->json(['message' => 'Unauthorized'])->setStatusCode(401);
})->name('login');
Route::get('/{any}', function () {
    return view('app'); // Charge Vue.js
})->where('any', '.*');
