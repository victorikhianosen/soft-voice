<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SettingResource;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\SettingUpdateRequest;
use App\Http\Services\SettingService;

class SettingController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected SettingRepository $settingRepository,
        protected SettingService $settingService
    ){
    }

    public function getSetting(): JsonResponse
    {
        $data = $this->settingRepository->getSetting(request()->user()->id);
        return $this->success(data: new SettingResource($data));
    }

    public function updateSetting(SettingUpdateRequest $request): JsonResponse
    {
        $data = $this->settingService->updateSetting($request);
        return $this->success(data: new SettingResource($data));
    }
}
