<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => '📦 طلب جديد من ' . $this->order->name,
            'order_id' => $this->order->id,
            'phone' => $this->order->phone,
            'city' => $this->order->city,
            'total' => $this->order->total_cost,
            'created_at' => now()->toDateTimeString()
        ];
    }
}
