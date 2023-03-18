<?php

namespace App\Http\Services;

use App\Http\Repositories\CouponRepository;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;

class CouponService
{
    public function __construct(protected CouponRepository $couponRepository)
    {
    }

    public function store(array $data): CouponResource
    {
        $coupon = $this->couponRepository->create($data);
        return new CouponResource($coupon->fresh());
    }

    public function update(Coupon $coupon, array $data): CouponResource
    {
        if ($data['total'] < $coupon->used) {
            abort(400, "Total cannot be less than used");
        }
        $this->couponRepository->update($coupon, $data);
        return new CouponResource($coupon->fresh());
    }
}
