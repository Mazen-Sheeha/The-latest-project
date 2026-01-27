<?php

namespace App\Http\Controllers;

use App\Http\Requests\Domain\CreateDomainRequest;
use App\Http\Requests\Domain\UpdateDomainRequest;
use App\Services\DomainService;

class DomainController extends Controller
{
    protected $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function index()
    {
        return $this->domainService->index();
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(CreateDomainRequest $request)
    {
        return $this->domainService->store($request);
    }

    public function show(string $id)
    {
        return $this->domainService->show($id);
    }

    public function edit(string $id)
    {
        return $this->domainService->edit($id);
    }

    public function update(UpdateDomainRequest $request, string $id)
    {
        return $this->domainService->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->domainService->destroy($id);
    }
}
