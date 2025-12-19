<?php

namespace App\Http\Controllers;

use App\Services\StatisticService;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    protected $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        return $this->statisticService = $statisticService;
    }

    public function statistics(Request $request)
    {
        return $this->statisticService->statistics($request);
    }
}
