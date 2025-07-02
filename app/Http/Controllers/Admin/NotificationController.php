<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Store a new notification
    public function store($data)
    {

        $user = User::findOrFail($data['user_id']);

        $notification = $user->notifications()->create([
            'text_en'    => $data['text_en'],
            'text_ar'    => $data['text_ar'],
            'request_id' => $data['request_id'],
            'page'       => $data['page']      
        ]);

        return true;
    }

    // Get unread notifications for a user
    public function unreadNotifications($user_id)
    {
        $user = User::with(['notifications' => function ($query) {
            $query->where('is_read', 0);
        }])->findOrFail($user_id);

        return response()->json(['data' => $user->notifications]);
    }

    // Get all notifications for a user
    public function allNotifications($user_id)
    {
        $user = User::with('notifications')->findOrFail($user_id);

        return response()->json(['data' => $user->notifications]);
    }

    // Mark a single notification as read
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => 1]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->notifications()->where('is_read', 0)->update(['is_read' => 1]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
