<?php

namespace App\Http\Repositories;

use App\Models\Coupon;

class CouponRepository extends AbstractRepository
{
    public function __construct(Coupon $coupon)
    {
        parent::__construct($coupon);
    }

    public function model()
    {
        return app(Coupon::class);
    }
}
