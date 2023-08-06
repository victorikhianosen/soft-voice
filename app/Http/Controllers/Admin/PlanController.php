<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Http\Repositories\PlanRepository;
use App\Traits\RespondWithHttpStatus;

class PlanController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected PlanRepository $planRepository
    ){
    }

    public function index()
    {
        $data = $this->planRepository->getAll('*');
        return $this->success(data: PlanResource::collection($data));
    }

    public function show(Plan $plan)
    {
        return $this->success(data: new PlanResource($plan));
    }
}
