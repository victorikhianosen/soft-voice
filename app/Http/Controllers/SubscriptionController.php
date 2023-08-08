<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionResource;
use App\Models\Plan;
use App\Traits\RespondWithHttpStatus;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected SubscriptionRepository $subscriptionRepository,
    ){
    }

    public function index(): JsonResponse
    {
        $data = $this->subscriptionRepository->getAllByUser(request()->user()->id);
        return $this->success(data: SubscriptionResource::collection($data));
    }

    public function getActive(): JsonResponse
    {
        $data = $this->subscriptionRepository->getActive(request()->user()->id);
        return $this->success(data: new SubscriptionResource($data));
    }

    public function getPaymentLink(Plan $plan)
    {
        $link = $this->paymentGateway->initializeSubscriptionAndGetPayLink($plan);
        return $this->success(data: compact('link'));
    }
}
