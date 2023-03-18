<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Notifications\CustomNotification;

class NotificationController extends Controller
{
    public static function sendAdminRegisteredEmail(Admin $admin, string $password)
    {

        $title = "Administrator Welcome";
        $body = 'Welcome to ' . config('env.app_name') . '.<br>
                An administrator account with role of <b>'.$admin->roles()->first()['name'].'</b> has been created for you, find your login credentials below.<br><br>
                Email: <b>'.$admin['email'].'</b><br>
                Password: <b>'.$password.'</b><br>';
        $action = [
            'text' => 'Login to Dashboard',
            'url'  => (config('env.spa_url') . 'admin/login')
        ];
        try {
            $admin->notify(new CustomNotification($title, $body, $action, ['mail']));
        }catch (\Exception) {
            logger('There was an error sending the email');
        }
    }
}
