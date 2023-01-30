<?php

namespace App\Http\Repositories;

use App\Models\Subscription;

class SubscriptionRepository extends AbstractRepository
{
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
    }

    public function model()
    {
        return app(Subscription::class);
    }

    public function getAllByUser()
    {
        return request()->user()->subscriptions()->get();
    }
}
