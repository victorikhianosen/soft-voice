<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SettingResource;
use App\Traits\RespondWithHttpStatus;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\SettingInvoiceUpdateRequest;
use App\Http\Requests\SettingNotificationsUpdateRequest;
use App\Http\Requests\SettingPreferencesUpdateRequest;
use App\Http\Requests\SettingTaxUpdateRequest;
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

    public function getSetting()
    {
        $data = $this->settingRepository->getSetting(request()->user()->id);
        return $this->success(data: new SettingResource($data));
    }

    public function updatePreferences(SettingPreferencesUpdateRequest $request): JsonResponse
    {
        $data = $this->settingService->updateSetting($request);
        return $this->success(data: new SettingResource($data));
    }

    public function updateNotifications(SettingNotificationsUpdateRequest $request): JsonResponse
    {
        $data = $this->settingService->updateSetting($request);
        return $this->success(data: new SettingResource($data));
    }

    public function updateInvoice(SettingInvoiceUpdateRequest $request): JsonResponse
    {
        $data = $this->settingService->updateSetting($request);
        return $this->success(data: new SettingResource($data));
    }

    public function updateTax(SettingTaxUpdateRequest $request): JsonResponse
    {
        $data = $this->settingService->updateSetting($request);
        return $this->success(data: new SettingResource($data));
    }
}