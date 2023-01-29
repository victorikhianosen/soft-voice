<?php

namespace App\Http\Controllers;

use App\Http\Services\ReportService;
use App\Http\Traits\RespondWithHttpStatus;

class ReportController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected ReportService $reportService)
    {
    }

    public function getProfitAndLoss()
    {
        $data = $this->reportService->computeProfitAndLoss();
        return $this->success(data: $data);
    }

    public function getSales()
    {
        $data = $this->reportService->computeSales();
        return $this->success(data: $data);
    }

    public function getExpenses()
    {
        $data = $this->reportService->computeExpense();
        return $this->success(data: $data);
    }
}
