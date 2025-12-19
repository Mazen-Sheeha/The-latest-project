<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        return $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->orderService->index($request);
    }

    public function show(string $id)
    {
        return $this->orderService->show($id);
    }

    public function create()
    {
        return $this->orderService->create();
    }

    public function store(CreateOrderRequest $request)
    {
        return $this->orderService->store($request);
    }

    public function edit(string $id)
    {
        return $this->orderService->edit($id);
    }

    public function update(UpdateOrderRequest $request, string $id)
    {
        return $this->orderService->update($request, $id);
    }

    public function changeOrderStatus(Request $request, string $id)
    {
        return $this->orderService->changeOrderStatus($request, $id);
    }

    public function changePaymentStatus(Request $request, string $id)
    {
        return $this->orderService->changePaymentStatus($request, $id);
    }

    public function changeTrackingNumber(Request $request, string $id)
    {
        return $this->orderService->changeTrackingNumber($request, $id);
    }

    public function export()
    {
        return $this->orderService->export();
    }

    public function destroy(string $id)
    {
        return $this->orderService->destroy($id);
    }
}
