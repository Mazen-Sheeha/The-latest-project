<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        return $this->notificationService = $notificationService;
    }

    public function fetch()
    {
        return $this->notificationService->fetch();
    }
}
