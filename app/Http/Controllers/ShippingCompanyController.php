<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingCompany\CreateShippingCompanyRequest;
use App\Http\Requests\ShippingCompany\UpdateShippingCompanyRequest;
use App\Services\ShippingCompanyService;

class ShippingCompanyController extends Controller
{
    protected ShippingCompanyService $shippingCompanyService;

    public function __construct(ShippingCompanyService $shippingCompanyService)
    {
        return $this->shippingCompanyService = $shippingCompanyService;
    }

    public function index()
    {
        return $this->shippingCompanyService->index();
    }

    public function create()
    {
        return $this->shippingCompanyService->create();
    }

    public function store(CreateShippingCompanyRequest $request)
    {
        return $this->shippingCompanyService->store($request);
    }


    public function edit(string $id)
    {
        return $this->shippingCompanyService->edit($id);
    }

    public function update(UpdateShippingCompanyRequest $request, string $id)
    {
        return $this->shippingCompanyService->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->shippingCompanyService->destroy($id);
    }
}
