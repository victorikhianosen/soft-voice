<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Services\CouponService;
use App\Http\Resources\CouponResource;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Repositories\CouponRepository;
use App\Http\Requests\CouponRequest;

use function GuzzleHttp\Promise\all;

class CouponController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected CouponService $couponService,
        protected CouponRepository $couponRepository
    ) {
    }

    public function index()
    {
        $data = $this->couponRepository->getAll('*');
        return $this->success(data: CouponResource::collection($data));
    }

    public function show(Coupon $coupon)
    {
        return $this->success(data: new CouponResource($coupon));
    }

    public function store(CouponRequest $request)
    {
        $coupon = $this->couponService->store($request->all());
        return $this->success("Coupon created", $coupon);
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon = $this->couponService->update($coupon, $request->all());
        return $this->success("Coupon updated", $coupon);
    }

    public function delete(Coupon $coupon)
    {
        $coupon = $this->couponRepository->destroy($coupon);
        return $this->success("Coupon deleted");
    }
}
