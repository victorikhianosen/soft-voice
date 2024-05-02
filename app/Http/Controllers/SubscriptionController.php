<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Services\PaymentService;
use App\Traits\RespondWithHttpStatus;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\SubscriptionLinkRequest;
use App\Http\Repositories\SubscriptionRepository;

class SubscriptionController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected PaymentService $paymentService,
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

    public function getPaymentLink(SubscriptionLinkRequest $request)
    {
        $link = $this->paymentService->generateSubscriptionPayLink($request->gateway, $request->plan_id);
        return $this->success(data: compact('link'));
    }
}
