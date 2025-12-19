<?php

namespace App\Services;

use App\Http\Requests\Adset\CreateAdsetRequest;
use App\Http\Requests\Adset\UpdateAdsetRequest;
use App\Models\Adset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdsetService
{
    public function index()
    {
        $adsets = Adset::orderBy('active', "DESC")->orderBy('id', 'DESC')->paginate(100)->withQueryString();
        return view('adsets.index', compact('adsets'));
    }

    public function statisticsIndex(Request $request)
    {
        $query = Adset::query()->withStatistics($request->from, $request->to);

        if ($request->active && $request->active !== 'all') {
            $query->where("active", $request->active === 'active');
        }

        $adsets = $query->orderBy('active', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(100)
            ->withQueryString();

        return view("adsets.statisticsIndex", compact('adsets'));
    }


    public function store(CreateAdsetRequest $request)
    {
        $validated = $request->validated();
        $adset = Adset::create($validated);
        $adset->active = true;
        return response()->json(['success' => true, 'message' => 'تم إنشاء مجموعة حملات إعلانية بنجاح', 'adset' => $adset]);
    }

    public function update(UpdateAdsetRequest $request, string $id)
    {
        $validated = $request->validated();
        $adset = Adset::findOrFail($id);
        $adset->update($validated);
        return response()->json(['success' => true, 'message' => "تم تعديل اسم المجموعة بنجاح"]);
    }

    public function changeActive(string $id)
    {
        $adset = Adset::findOrFail($id);
        $adset->active = !$adset->active;
        $adset->save();
        return response()->json(['success' => true, 'message' => 'تم تعديل حالة مجموعة الحملات الإعلانية إلى ' . ($adset->active ? "نشط" : "غير نشط"), 'active' => $adset->active]);
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        };
        $adset = Adset::findOrFail($id);
        $adset->delete();
        return response()->json(['success' => true, 'message' => "تم حذف المجموعة $adset->name بنجاح"]);
    }
}
