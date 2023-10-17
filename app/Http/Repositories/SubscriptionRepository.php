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

    public function getAllByUser(int $userId)
    {
        return $this->model()->where('user_id', $userId)->latest()->get();
    }

    public function getActive(int $userId)
    {
        return $this->model()->where('user_id', $userId)
            ->whereNotNull('ends_at')
            ->where('ends_at', '>=', now())
            ->latest()->first();;
    }
}
