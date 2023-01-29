<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\ReportController;

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

Route::middleware(['auth:sanctum', 'ability:user'])->group(function () {
    Route::prefix('/customers')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('/{customer}', 'show');
            Route::get('/{customer}/history', 'getHistory');
            Route::put('/{customer}', 'update');
            Route::delete('/{customer}', 'delete');
        });
    });

    Route::prefix('/invoices')->group(function () {
        Route::controller(InvoiceController::class)->group(function () {
            Route::get('/summary', 'getSummary');
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('/{invoice}', 'show');
            Route::put('/{invoice}', 'update');
            Route::delete('/{invoice}', 'delete');
            Route::put('/{invoice}/duplicate', 'duplicate');
            Route::put('/{invoice}/mark-as', 'markAs');
            Route::post('/{invoice}/send', 'send')->middleware('throttle:3,1');
            Route::post('/{invoice}/send-reminder', 'sendReminder')->middleware('throttle:3,1');
            Route::post('/export', 'export');
        });
    });

    Route::prefix('/estimates')->group(function () {
        Route::controller(EstimateController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('/{estimate}', 'show');
            Route::put('/{estimate}', 'update');
            Route::delete('/{estimate}', 'delete');
            Route::put('/{estimate}/duplicate', 'duplicate');
            Route::put('/{estimate}/mark-as', 'markAs');
            Route::post('/{estimate}/send', 'send')->middleware('throttle:3,1');
            Route::post('/export', 'export');
        });
    });

    Route::prefix('/expenses')->group(function () {
        Route::controller(ExpenseController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('/{expense}', 'show');
            Route::put('/{expense}', 'update');
            Route::delete('/{expense}', 'delete');
            Route::put('/{expense}/duplicate', 'duplicate');
            Route::put('/{expense}/mark-as', 'markAs');
            Route::post('/export', 'export');
        });
    });

    Route::prefix('/payments')->group(function () {
        Route::controller(PaymentController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/{payment}', 'show');
            Route::put('/{payment}', 'update');
            Route::post('', 'store');
            Route::delete('/{payment}', 'delete');
        });
    });

    Route::prefix('/report')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('/profit-and-loss', 'getProfitAndLoss');
            Route::get('/sales', 'getSales');
            Route::get('/expenses', 'getExpenses');
        });
    });
});