<?php

namespace App\Http\Repositories;

use App\Models\OnlinePayment;

class OnlinePaymentRepository extends AbstractRepository
{
    public function __construct(OnlinePayment $onlinePayment)
    {
        parent::__construct($onlinePayment);
    }

    public function model()
    {
        return app(OnlinePayment::class);
    }
}
