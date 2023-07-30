<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AnalyticsService;
use App\Http\Traits\RespondWithHttpStatus;

class AnalyticsController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected AnalyticsService $analyticsService
    )
    {
    }

    public function getAllAnalytics()
    {
        return $this->success(data: $this->analyticsService->generateAnalytics());
    }
}
