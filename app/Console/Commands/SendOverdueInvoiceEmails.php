<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use App\Http\Services\NotificationService\CustomerNotificationService;
use App\Http\Services\NotificationService\UserNotificationService;

class SendOverdueInvoiceEmails extends Command
{
    protected $signature = 'invoices:send-overdue-emails';
    protected $description = 'Send overdue invoice emails to customers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(CustomerNotificationService $customerNotificationService, UserNotificationService $userNotificationService)
    {
        $overdueInvoices = Invoice::where('due_date', '<', now())
            ->whereNull('overdue_email_sent')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $customerNotificationService->sendInvoiceOverdueToCustomer($invoice);

            $userNotificationService->sendInvoiceOverdueNotification($invoice);

            $invoice->update(['overdue_email_sent' => now()]);
        }

        $this->info('Overdue invoice emails sent successfully.');
    }
}
