<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Http\Services\ReportService;
use App\Http\Traits\RespondWithHttpStatus;

class ReportController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected ReportService $reportService)
    {
    }

    public function getProfitAndLoss(ReportRequest $request)
    {
        $data = $this->reportService->computeProfitAndLoss($request->all());
        return $this->success(data: $data);
    }

    public function getSales(ReportRequest $request)
    {
        $data = $this->reportService->computeSales($request->all());
        return $this->success(data: $data);
    }

    public function getExpenses(ReportRequest $request)
    {
        $data = $this->reportService->computeExpense($request->all());
        return $this->success(data: $data);
    }
}
