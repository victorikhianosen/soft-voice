<?php

namespace App\Http\Controllers;

use App\Traits\Paginatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\RespondWithHttpStatus;
use App\Http\Resources\NotificationResource;

class InAppNotificationController extends Controller
{
    use Paginatable, RespondWithHttpStatus;

    public function getNotifications(Request $request)
    {
        $perPage = $request['per_page'] ?? 50;
        $currentPage = $request['current_page'] ?? 1;
        $data = request()->user()
            ->notifications()
            ->offset(($currentPage - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return $this->success(
            data: NotificationResource::collection($data),
            meta: $this->getMeta($request->all(), request()->user()->notifications()->count())
        );
    }

    public function showNotification(string $id)
    {
        DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);
        $notification = request()->user()->notifications()->where('id', $id)->first();
        return $this->success(data: new NotificationResource(collect($notification)));
    }

    public function markAllNotificationsAsRead()
    {
        foreach (request()->user()->unreadNotifications()->get() as $notification) {
            $notification->markAsRead();
        }
        return $this->success("Marked all notifications as read");
    }
}
