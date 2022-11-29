<?php

namespace App\Http\Repositories;

use App\Enums\Status;
use App\Models\Estimate;
use Carbon\Carbon;

class EstimateRepository extends AbstractRepository
{
    public function __construct(Estimate $estimate)
    {
        parent::__construct($estimate);
    }

    public function model()
    {
        return app(Estimate::class);
    }
}
