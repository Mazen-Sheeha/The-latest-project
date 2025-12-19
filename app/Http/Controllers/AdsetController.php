<?php

namespace App\Http\Controllers;

use App\Http\Requests\Adset\CreateAdsetRequest;
use App\Http\Requests\Adset\UpdateAdsetRequest;
use App\Services\AdsetService;
use Illuminate\Http\Request;

class AdsetController extends Controller
{
    protected $adsetService;

    public function __construct(AdsetService $adsetService)
    {
        return $this->adsetService = $adsetService;
    }

    public function index()
    {
        return $this->adsetService->index();
    }

    public function statisticsIndex(Request $request)
    {
        return $this->adsetService->statisticsIndex($request);
    }

    public function store(CreateAdsetRequest $request)
    {
        return $this->adsetService->store($request);
    }

    public function update(UpdateAdsetRequest $request, string $id)
    {
        return $this->adsetService->update($request, $id);
    }

    public function changeActive(string $id)
    {
        return $this->adsetService->changeActive($id);
    }

    public function destroy(string $id)
    {
        return $this->adsetService->destroy($id);
    }
}
