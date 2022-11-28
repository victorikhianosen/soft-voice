<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Estimate;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\EstimateRequest;
use App\Http\Services\EstimateService;
use App\Http\Resources\EstimateResource;
use App\Http\Requests\ListEstimateRequest;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Requests\MarkEstimateAsRequest;
use App\Http\Repositories\EstimateRepository;

class EstimateController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected EstimateService $estimateService,
        protected EstimateRepository $estimateRepository
    )
    {
    }

    public function index(ListEstimateRequest $request): JsonResponse
    {
        $result = $this->estimateService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Estimate $estimate): JsonResponse
    {
        return $this->success(data: new EstimateResource($estimate));
    }

    public function store(EstimateRequest $request): JsonResponse
    {
        $data = $this->estimateService->store($request->all());
        return $this->success("Estimate saved", $data);
    }

    public function update(Estimate $estimate, EstimateRequest $request): JsonResponse
    {
        $data = $this->estimateService->update($estimate, $request->all());
        return $this->success("Estimate updated", $data);
    }

    public function delete(Estimate $estimate): JsonResponse
    {
        $this->estimateService->delete($estimate);
        return $this->success("Estimate deleted");
    }

    public function duplicate(Estimate $estimate): JsonResponse
    {
        $data = $this->estimateService->duplicate($estimate);
        return $this->success("Estimate duplicated", $data);
    }

    public function markAs(Estimate $estimate, MarkEstimateAsRequest $request): JsonResponse
    {
        $data = $this->estimateService->markAs($estimate, Status::from($request->status));
        return $this->success("Estimate status updated", $data);
    }

    public function send(Estimate $estimate): JsonResponse
    {
        $this->estimateService->send($estimate);
        return $this->success("Estimate sent");
    }

    public function export(ExportRequest $request): JsonResponse
    {
        $url = $this->estimateService->export($request['format']);
        return $this->success(
            "Temporary file generated, this file will be deleted in 10 minutes",
            ['url' => $url]
        );
    }
}
