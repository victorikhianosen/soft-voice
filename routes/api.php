<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PaymentKeyController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\EmailVerificationController;

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
        Route::post('/profile/update', 'updateProfile');
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
            Route::post('/{invoice}/update', 'update');
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
            Route::post('/{expense}/update', 'update');
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

    Route::prefix('/subscriptions')->group(function () {
        Route::controller(SubscriptionController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/active', 'getActive');
        });
    });

    Route::prefix('/plans')->group(function () {
        Route::controller(PlanController::class)->group(function () {
            Route::get('', 'index');
        });
    });

    Route::prefix('/wallet')->group(function () {
        Route::controller(WalletController::class)->group(function () {
            Route::get('/balance', 'getBalance');
            Route::post('/withdraw', 'withdraw');
            Route::post('/deposit', 'deposit');
        });
    });

    Route::prefix('/transactions')->group(function () {
        Route::controller(TransactionController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/{transaction}', 'show');
        });
    });

    Route::prefix('/settings')->group(function () {
        Route::controller(SettingController::class)->group(function () {
            Route::get('', 'getSetting');
            Route::put('/preferences', 'updatePreferences');
            Route::put('/notifications', 'updateNotifications');
            Route::prefix('/invoice')->group(function () {
                Route::post('/general', 'updateInvoice');
                Route::put('/tax', 'updateTax');
            });
        });
    });

    Route::prefix('/bank-accounts')->group(function () {
        Route::controller(BankAccountController::class)->group(function () {
            Route::get('/get-preferred', 'getPreferred');
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('/{bank_account}', 'show');
            Route::put('/{bank_account}', 'update');
            Route::delete('/{bank_account}', 'delete');
            Route::put('/{bank_account}/set-preferred', 'setPreferred');
        });
    });

    Route::prefix('/payment-keys')->group(function () {
        Route::controller(PaymentKeyController::class)->group(function () {
            Route::get('', 'getKeys');
            Route::put('', 'updateKeys');
        });
    });

    Route::controller(ReferralController::class)->group(function () {
        Route::get('/referred', 'index');
    });

    Route::controller(HomeController::class)->group(function () {
        Route::get('/currencies', 'getCurrencies');
    });
});