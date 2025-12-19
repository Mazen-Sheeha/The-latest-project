<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockedNumber\CreateBlockedNumberRequest;
use App\Http\Requests\BlockedNumber\DeleteBlockedNumberRequest;
use App\Services\BlockedNumberService;

class BlockedNumberController extends Controller
{
    protected $blockedNumberService;

    public function __construct(BlockedNumberService $blockedNumberService)
    {
        return $this->blockedNumberService = $blockedNumberService;
    }

    public function store(CreateBlockedNumberRequest $request)
    {
        return $this->blockedNumberService->store($request);
    }

    public function destroy(DeleteBlockedNumberRequest $request)
    {
        return $this->blockedNumberService->destroy($request);
    }
}
