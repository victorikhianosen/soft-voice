<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\AuthController;
use App\Http\Controllers\Auth\Admin\PasswordResetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->prefix('/admin')->group(function () {
    Route::post('/login', 'login');
    Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'me');
        Route::put('/password/change', 'changePassword');
    });
});

Route::controller(PasswordResetController::class)->middleware('guest')->prefix('/admin')->group(function () {
    Route::post('/password/reset/send-link', 'sendPasswordResetLink')->middleware('throttle:3,1');
    Route::post('/password/reset', 'resetPassword');
});