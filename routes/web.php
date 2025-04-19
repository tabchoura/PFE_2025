<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('unauthorized', function () {
    return response()->json(['message' => 'Unauthorized'])->setStatusCode(401);
})->name('login');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('vue');

