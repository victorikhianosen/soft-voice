<?php

namespace App\Http\Repositories;

use App\Models\Plan;

class PlanRepository extends AbstractRepository
{
    public function __construct(Plan $plan)
    {
        parent::__construct($plan);
    }

    public function model()
    {
        return app(Plan::class);
    }
}
