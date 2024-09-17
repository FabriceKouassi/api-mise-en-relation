<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
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

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::get('loginFail', 'loginFail')->name('loginFail');
        Route::post('register', 'register');
        Route::get('logout/{user}', 'logout')->middleware('auth:sanctum');
    });

Route::middleware('auth:sanctum', 'timeout')
    ->group(function () {
        
        Route::prefix('users')->group(function () {
            Route::controller(AuthController::class)->group(function () {
                Route::post('/logout', 'logout');
            });
            Route::controller(UserController::class)->group(function () {
                Route::get('/', 'all');
                Route::post('/update/{slug}', 'update');
            });
        });

    });