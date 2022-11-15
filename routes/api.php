<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::middleware(['auth:sanctum', 'ability:user'])->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'me');
        Route::put('/password/change', 'changePassword');
    });
});

Route::controller(EmailVerificationController::class)->group(function () {
    Route::post('/email/verify', 'verifyEmail');
    Route::middleware(['auth:sanctum', 'ability:user'])->group(function () {
        Route::post('/email/resend-link', 'resendEmailVerificationLink')->middleware('throttle:3,1');
    });
});

Route::controller(PasswordResetController::class)->group(function () {
    Route::post('/password/reset/send-link', 'sendPasswordResetLink')->middleware('throttle:3,1');
    Route::post('/password/reset', 'resetPassword');
});