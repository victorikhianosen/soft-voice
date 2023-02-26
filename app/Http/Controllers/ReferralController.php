<?php

namespace App\Http\Controllers;

use App\Http\Services\ReferralService;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\RespondWithHttpStatus;

class ReferralController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected ReferralService $referralService
    )
    {
    }

    public function index()
    {
        $data = $this->referralService->getByUserId(request()->user()->id);
        return $this->success(data: $data);
    }
}
