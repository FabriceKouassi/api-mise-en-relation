<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;
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
        Route::post('logout', 'logout')->middleware('auth:sanctum');
    });

Route::middleware('auth:sanctum', 'timeout')
    ->group(function () {

        Route::prefix('users')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/all', 'all');
                Route::get('/show/{slug}', 'show');
                Route::post('/update/{slug}', 'update');
                Route::delete('/delete/{slug}', 'delete');
            });
        });
        
        Route::prefix('categories')->group(function () {
            Route::controller(CategorieController::class)->group(function () {
                Route::get('/all', 'all');
                Route::get('/show/{slug}', 'show');
                Route::post('/create', 'create');
                Route::post('/update/{slug}', 'update');
                Route::delete('/delete/{id}', 'delete');
            });
        });
        
        
        Route::prefix('services')->group(function () {
            Route::controller(CategorieController::class)->group(function () {
                Route::get('/all', 'all');
                Route::get('/show/{slug}', 'show');
                Route::post('/create', 'create');
                Route::post('/update/{slug}', 'update');
                Route::delete('/delete/{id}', 'delete');
            });
        });
        

    });