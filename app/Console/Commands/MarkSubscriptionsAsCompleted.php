<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Console\Command;

class MarkSubscriptionsAsCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all complete subscriptions as completed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Subscription::where('status', 'active')
            ->whereDate('ends_at', '<', Carbon::now()->toDateString())
            ->get()
            ->each(function ($subscription) {
                $subscription->update([
                    'status' => 'completed',
                ]);
            });
    }
}
