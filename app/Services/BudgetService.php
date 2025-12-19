<?php

namespace App\Services;

use App\Http\Requests\Budget\CreateBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BudgetService
{
    public function index(Request $request)
    {
        if (!$request->campaign) return to_route("adsets.index");
        $budgets = Budget::with('campaign')->where("campaign_id", $request->campaign)->orderBy('date', "DESC")->orderBy('id', "DESC")->paginate(100);
        return view('budgets.index', compact('budgets'));
    }

    public function store(CreateBudgetRequest $request)
    {
        $validated = $request->validated();
        $budget = Budget::create($validated);
        return response()->json(['success' => true, 'message' => "تم إضافة ميزانية بنجاح", "budget" => $budget]);
    }

    public function edit(string $id)
    {
        $budget = Budget::findOrFail($id);
        return view('budgets.edit', compact("budget"));;
    }

    public function update(UpdateBudgetRequest $request, string $id)
    {
        $validated = $request->validated();
        $budget = Budget::findOrFail($id);
        $budget->budget = $validated['budget'];
        $budget->date = $validated['date'];
        $budget->save();
        return to_route('budgets.index', ['campaign' => $budget->campaign_id])->with(['success' => 'تم تعديل الميزانية بنجاح']);
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        };
        $budget = Budget::findOrFail($id);
        $budget->delete();
        return response()->json(['success' => true, 'message' => "تم حذف الميزانية بنجاح"]);
    }
}
