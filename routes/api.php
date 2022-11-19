<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;

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

Route::controller(CustomerController::class)->middleware(['auth:sanctum', 'ability:user'])->group(function () {
    Route::get('/customers', 'index');
    Route::post('/customers/store', 'store');
    Route::get('/customers/{customer}/show', 'show');
    Route::put('/customers/{customer}', 'update');
    Route::delete('/customers/{customer}', 'delete');
});

Route::controller(InvoiceController::class)->middleware(['auth:sanctum', 'ability:user'])->group(function () {
    Route::get('/invoices', 'index');
    Route::post('/invoices/store', 'store');
    Route::get('/invoices/{invoice}/show', 'show');
    Route::put('/invoices/{invoice}', 'update');
    Route::delete('/invoices/{invoice}', 'delete');
    Route::put('/invoices/{invoice}/mark-as', 'markAs');
    Route::post('/invoices/{invoice}/send', 'send')->middleware('throttle:3,1');
});