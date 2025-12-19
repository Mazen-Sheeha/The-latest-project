<?php

namespace App\Http\Controllers;

use App\Services\HomeService;

class HomeController extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        return $this->homeService = $homeService;
    }

    public function home()
    {
        return $this->homeService->home();
    }
}
