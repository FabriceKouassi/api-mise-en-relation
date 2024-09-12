<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum', 'timeout')->get('/user', function (Request $request) {
    // return $request->user();
    return response()->json(['message' => 'OK'], 200);
});

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::get('logout/{user}', 'logout')->middleware('auth:sanctum');
    });