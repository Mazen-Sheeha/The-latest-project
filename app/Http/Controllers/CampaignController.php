<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Services\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        return $this->campaignService = $campaignService;
    }

    public function index(Request $request)
    {
        return $this->campaignService->index($request);
    }

    public function statisticsIndex(Request $request)
    {
        return $this->campaignService->statisticsIndex($request);
    }

    public function store(CreateCampaignRequest $request)
    {
        return $this->campaignService->store($request);
    }

    public function edit(string $id)
    {
        return $this->campaignService->edit($id);
    }

    public function update(UpdateCampaignRequest $request, string $id)
    {
        return $this->campaignService->update($request, $id);
    }

    public function changeActive(string $id)
    {
        return $this->campaignService->changeActive($id);
    }

    public function destroy(string $id)
    {
        return $this->campaignService->destroy($id);
    }
}
