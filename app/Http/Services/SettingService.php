<?php

namespace App\Http\Services;

use App\Http\Requests\SettingUpdateRequest;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\SettingPreferencesUpdateRequest;
use App\Traits\UploadsFile;

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
        $logo = $request->file('business_logo');

        $data = $request->all();
        if ($file) {
            $data['signature'] = $this->saveFIle($file, 'signatures');
        }
        if ($logo) {
            $data['business_logo'] = $this->saveFIle($logo, 'logos');
        }

        $this->settingRepository->updateSetting(request()->user()->id, $data);
        return request()->user()->setting;
    }
}
