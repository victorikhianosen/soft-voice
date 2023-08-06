<?php

namespace App\Http\Services;

use Exception;
use App\Models\Admin;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\CustomNotification;
use App\Http\Services\NotificationBuilder\AdminNotificationBuilder;
use App\Http\Services\NotificationBuilder\CustomerNotificationBuilder;
use App\Http\Services\NotificationBuilder\UserNotificationBuilder;
use App\Models\Estimate;

class NotificationService
{
    public function __construct(
        protected UserNotificationBuilder $userNotificationBuilder,
        protected AdminNotificationBuilder $adminNotificationBuilder,
        protected CustomerNotificationBuilder $customerNotificationBuilder,
    ) {
    }

    public function sendAdminWelcomeNotification(Admin $admin, string $password)
    {
        $this->send($admin, $this->adminNotificationBuilder->welcome($admin, $password));
    }

    public function sendInvoiceReminderNotification(Invoice $invoice)
    {
        $this->send($invoice->customer, $this->customerNotificationBuilder->invoiceReminder($invoice));
    }

    public function sendInvoiceToCustomer(Invoice $invoice)
    {
        $this->send($invoice->customer, $this->customerNotificationBuilder->invoiceSent($invoice));
    }

    public function sendEstimateToCustomer(Estimate $estimate)
    {
        $this->send($estimate->customer, $this->customerNotificationBuilder->estimateSent($estimate));
    }

    private function send(Model $recipient, $data, $channel = ['mail', 'database'])
    {
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $action = $data['action'] ?? null;
        if (in_array('mail', $channel) || in_array('database', $channel)) {
            try {
                $recipient->notify(new CustomNotification($title, $body, $action, $channel));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // TODO: Handle SMS

        // TODO: Handle Whatsapp
    }
}
