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
        $setting = $this->model()->where('user_id', $userId)->first();
        if (!$setting) return $this->create(['user_id' => $userId])->fresh();
        return $setting;
    }

    public function updateSetting(int $userId, array $data)
    {
        $setting = $this->model()->where('user_id', $userId)->first();
        if (!$setting) $setting = $this->create(['user_id' => $userId])->fresh();
        return $setting->update($data);
    }
}
