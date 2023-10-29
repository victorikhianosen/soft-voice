<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
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
        $freePlan = Plan::where('slug', 'basic')->first();

        $usersWithoutActiveSubscription = User::whereDoesntHave('subscriptions', function ($query) {
            $query->where('status', 'active')->where('ends_at', '>=', now());
        })->get();

        foreach ($usersWithoutActiveSubscription as $user) {
            $user->subscriptions()->create([
                'plan_id'   => $freePlan->id,
                'starts_at' => now(),
                'ends_at'   => now()->addMonth(),
                'status'    => 'active',
            ]);

            $this->info("Assigned free subscription to User ID: {$user->id}");
        }

        $this->info('Free subscriptions have been assigned to eligible users.');
    }
}
