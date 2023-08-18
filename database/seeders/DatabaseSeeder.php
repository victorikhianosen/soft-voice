<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Http\Services\NotificationService;
use App\Http\Services\NotificationService\UserNotificationService;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Wallet;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\EstimateItem;
use App\Models\Subscription;
use App\Models\InvoicePayment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        protected UserNotificationService $userNotificationService
    )
    {
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(5)->create()->each(function ($user) {
            Wallet::factory()->create(['user_id' => $user->id]);
            Subscription::factory()->create(['user_id' => $user->id]);
            Setting::factory()->create(['user_id' => $user->id]);
        });

        Customer::factory(200)->create();
        Coupon::factory(50)->create();
        Estimate::factory(200)->create();
        EstimateItem::factory(1000)->create();
        Expense::factory(500)->create();
        Invoice::factory(200)->create()->each(function ($invoice) {
            $this->userNotificationService->sendInvoiceViewedNotification($invoice, ['database']);
        });
        InvoiceItem::factory(1000)->create();
        InvoicePayment::factory(1000)->create();
        Message::factory(20)->create();
        Transaction::factory(2000)->create();
    }
}
