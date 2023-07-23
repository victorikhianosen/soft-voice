<?php

namespace App\Http\Services;

use App\Http\Requests\SettingUpdateRequest;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\SettingPreferencesUpdateRequest;
use App\Http\Traits\UploadsFile;

class SettingService
{
    use UploadsFile;

    public function __construct(
        protected SettingRepository $settingRepository
    )
    {
    }

    public function updateSetting($request)
    {
        $file = $request->file('signature');
        $data = $request->all();
        if ($file) {
            $data['signature'] = $this->saveFIle($file, 'signatures');
        }
        $this->settingRepository->updateSetting(request()->user()->id, $data);
        return request()->user()->setting;
    }
}
