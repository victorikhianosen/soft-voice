<?php

namespace App\Http\Controllers;

use App\Http\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Traits\RespondWithHttpStatus;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    use RespondWithHttpStatus;


    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
    ){
    }

    public function index(): JsonResponse
    {
        $data = $this->subscriptionRepository->getAllByUser();
        return $this->success(data: SubscriptionResource::collection($data));
    }
}
