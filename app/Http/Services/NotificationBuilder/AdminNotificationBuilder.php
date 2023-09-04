<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Admin;

class AdminNotificationBuilder
{
    public function welcome(Admin $admin, string $password)
    {
        $title = "Administrator Welcome";
        $body = 'Welcome to ' . config('env.app_name') . '.<br>
                An administrator account with role of <b>' . $admin->roles()->first()['name'] . '</b>
				has been created for you, find your login credentials below.<br><br>
                Email: <b>' . $admin['email'] . '</b><br>
                Password: <b>' . $password . '</b><br>';
        $actions = [
            'text' => 'Login to Dashboard',
            'url'  => (config('env.spa_url') . 'admin/login')
        ];
        return compact('title', 'body', 'actions');
    }
}
