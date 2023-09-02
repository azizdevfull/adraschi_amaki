<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications;

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function markRead($id)
    {
        $notification = Auth::user()->unreadNotifications->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        } else {
            return response()->json([
                'message' => __('notification.already_read'),
            ]);
        }

        return response()->json([
            'message' => __('notification.read_success'),
        ]);
    }

    public function readNotifications()
    {
        $read_notifications = Auth::user()->readNotifications;

        return response()->json([
            'notifications' => $read_notifications
        ]);
    }
    public function unReadNotifications()
    {
        $unread_notifications = Auth::user()->unReadNotifications;

        return response()->json([
            'notifications' => $unread_notifications
        ]);
    }
}
