<?php

namespace App\Http\Controllers;

use App\Services\EasyOrderService;
use Illuminate\Http\Request;

class EasyOrderController extends Controller
{
    protected $easyOrderService;

    public function __construct(EasyOrderService $easyOrderService)
    {
        return $this->easyOrderService = $easyOrderService;
    }

    public function webhook(Request $request)
    {
        return $this->easyOrderService->webhook($request);
    }
}
