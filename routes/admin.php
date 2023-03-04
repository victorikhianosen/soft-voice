<?php

use App\Http\Controllers\Admin\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\AuthController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'me');
        Route::put('/password/change', 'changePassword');
    });
});

Route::controller(PasswordResetController::class)->middleware('guest')->group(function () {
    Route::post('/password/reset/send-link', 'sendPasswordResetLink')->middleware('throttle:3,1');
    Route::post('/password/reset', 'resetPassword');
});

Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
    Route::prefix('/transactions')->group(function () {
        Route::controller(TransactionController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/{transaction}', 'show');
            Route::post('/{transaction}/approve', 'approve');
            Route::post('/{transaction}/decline', 'decline');
            Route::post('/export', 'export');
        });
    });

    Route::prefix('/users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/{user}', 'show');
            Route::put('/{user}/restrict', 'restrict');
            Route::put('/{user}/reinstate', 'reinstate');
            Route::get('/{user}/transactions', 'transactions');
            Route::get('/{user}/subscriptions', 'subscriptions');
            Route::get('/{user}/bank-accounts', 'bankAccounts');
            Route::get('/{user}/customers', 'customers');
            Route::get('/{user}/invoices', 'invoices');
            Route::get('/{user}/estimates', 'estimates');
            Route::get('/{user}/expenses', 'expenses');
            Route::post('/{user}/customers/export', 'exportCustomers');
            Route::get('/{user}/settings', 'settings');
        });
    });

    Route::prefix('/plans')->group(function () {
        Route::controller(PlanController::class)->group(function () {
            Route::get('', 'index');
            Route::get('{plan}', 'show');
        });
    });
});