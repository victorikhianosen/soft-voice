<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PlanRepository;
use App\Http\Resources\PlanResource;
use App\Http\Traits\RespondWithHttpStatus;

class PlanController extends Controller
{
    use RespondWithHttpStatus;


    public function __construct(
        protected PlanRepository $planRepository
    ){
    }

    public function index()
    {
        $data = $this->planRepository->fetchAll();
        return $this->success(data: PlanResource::collection($data));
    }
}
