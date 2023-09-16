<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CreateNewFreeSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'free-subscription:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates new subscriptions for all free plan users with expiring subscription';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $plan = Plan::where('slug', 'basic')->first();
        Subscription::where('plan_id', $plan->id)
            ->where('status', 'active')
            ->whereDate('ends_at', Carbon::now()->toDateString())
            ->get()
            ->each(function ($subscription) use ($plan) {
                $subscription->update([
                    'status' => 'expired',
                ]);

                $subscription->user->subscriptions()->create([
                    'plan_id'   => $plan->id,
                    'starts_at' => now(),
                    'ends_at'   => now()->addMonth(),
                    'status'    => 'active',
                ]);
            });
    }
}
