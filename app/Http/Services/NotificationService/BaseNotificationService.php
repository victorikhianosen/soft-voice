<?php

namespace App\Http\Services\NotificationService;

use Exception;
use App\Models\Admin;
use App\Models\Invoice;
use Twilio\Rest\Client;
use App\Models\Estimate;
use App\Traits\HasRestrictions;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\CustomNotification;
use App\Http\Services\NotificationBuilder\UserNotificationBuilder;
use App\Http\Services\NotificationBuilder\AdminNotificationBuilder;
use App\Http\Services\NotificationBuilder\CustomerNotificationBuilder;

abstract class BaseNotificationService
{
    use HasRestrictions;

    public function __construct(
        protected UserNotificationBuilder $userNotificationBuilder,
        protected AdminNotificationBuilder $adminNotificationBuilder,
        protected CustomerNotificationBuilder $customerNotificationBuilder,
    ) {
    }

    protected function send(
        Model $recipient, array $data, array $channel = ['mail', 'database']
    ) {
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $actions = $data['actions'] ?? [];
        if (in_array('mail', $channel) || in_array('database', $channel)) {
            $channel = array_filter($channel, fn ($c) => in_array($c, $channel));
            try {
                $recipient->notify(new CustomNotification($title, $body, $actions, $channel));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        if (in_array('sms', $channel) && !$this->isRestricted('sms-messaging')) {
            $smsRecipientNumber = $recipient->phone;

            if ($smsRecipientNumber) {
                try {
                    $twilio = new Client(config('env.twilio_sid'), config('env.twilio_token'));

                    $twilio->messages->create($smsRecipientNumber, [
                        "from" => config('env.twilio_phone_number'),
                        "body" => $body,
                    ]);
                } catch (Exception $e) {
                    Log::error('Error sending SMS: ' . $e->getMessage());
                }
            }
        }

        if (in_array('whatsapp', $channel) && !$this->isRestricted('whatsapp-messaging')) {

            if ($recipient->whatsapp_number) {
                $smsRecipientNumber = $recipient->whatsapp_number;
            } else {
                $smsRecipientNumber = $recipient->phone;
            }

            if ($smsRecipientNumber) {
                try {
                    $twilio = new Client(config('env.twilio_sid'), config('env.twilio_token'));
                    $twilio->messages->create("whatsapp:$smsRecipientNumber", [
                        "from" => "whatsapp:" . config('env.twilio_phone_number'),
                        "body" => $body
                    ]);
                } catch (Exception $e) {
                    Log::error('Error sending WhatsApp Message: ' . $e->getMessage());
                }
            }
        }
    }
}
