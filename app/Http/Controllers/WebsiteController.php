<?php

namespace App\Http\Controllers;

use App\Http\Requests\Website\CreateWebsiteRequest;
use App\Http\Requests\Website\UpdateWebsiteRequest;
use App\Services\WebsiteService;

class WebsiteController extends Controller
{
    protected $websiteservice;

    public function __construct(WebsiteService $websiteService)
    {
        return $this->websiteservice = $websiteService;
    }

    public function index()
    {
        return $this->websiteservice->index();
    }

    public function store(CreateWebsiteRequest $request)
    {
        return $this->websiteservice->store($request);
    }

    public function edit(string $id)
    {
        return $this->websiteservice->edit($id);
    }

    public function update(UpdateWebsiteRequest $request, string $id)
    {
        return $this->websiteservice->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->websiteservice->destroy($id);
    }
}
