<?php

namespace App\Http\Repositories;

use App\Models\Setting;

class SettingRepository extends AbstractRepository
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }

    public function model()
    {
        return app(Setting::class);
    }

    public function getSetting(int $userId)
    {
        return $this->model()->where('user_id', $userId)->first();
    }

    public function updateSetting(int $userId, array $data)
    {
        return $this->model()->where('user_id', $userId)->update($data);
    }
}
