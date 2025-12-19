<?php

namespace App\Http\Controllers;

use App\Http\Requests\Budget\CreateBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Services\BudgetService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        return $this->budgetService = $budgetService;
    }

    public function index(Request $request)
    {
        return $this->budgetService->index($request);
    }

    public function store(CreateBudgetRequest $request)
    {
        return $this->budgetService->store($request);
    }

    public function edit(string $id)
    {
        return $this->budgetService->edit($id);
    }

    public function update(UpdateBudgetRequest $request, string $id)
    {
        return $this->budgetService->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->budgetService->destroy($id);
    }
}
