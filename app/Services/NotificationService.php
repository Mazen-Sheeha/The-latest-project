<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function fetch()
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications()->latest()->first();
        if ($notification) {
            $data = [
                'id' => $notification->id,
                'title' => $notification->data['title'],
                'phone' => $notification->data['phone'],
                'city' => $notification->data['city'],
                'total' => $notification->data['total'],
                'created_at' => $notification->created_at->diffForHumans()
            ];
            $notification->delete();
            return response()->json([$data]);
        }

        return response()->json([]);
    }
}
