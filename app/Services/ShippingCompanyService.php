<?php

namespace App\Services;

use App\Http\Requests\ShippingCompany\CreateShippingCompanyRequest;
use App\Http\Requests\ShippingCompany\UpdateShippingCompanyRequest;
use App\Models\ShippingCompany;
use Illuminate\Support\Facades\Gate;

class ShippingCompanyService
{
    public function index()
    {
        $shipping_companies = ShippingCompany::withCount('products')->paginate(100);
        return view("shipping_companies.index", compact('shipping_companies'));
    }

    public function create()
    {
        return view("shipping_companies.create");
    }

    public function store(CreateShippingCompanyRequest $request)
    {
        $validated = $request->validated();
        $company = ShippingCompany::create($validated);
        return response()->json(['success' => true, 'message' => "تم إضافة شركة الشحن بنجاح", 'company' => $company]);
    }

    public function edit(string $id)
    {
        $company = ShippingCompany::findOrFail($id);
        return view('shipping_companies.edit', compact('company'));
    }

    public function update(UpdateShippingCompanyRequest $request, string $id)
    {
        $validated = $request->validated();
        $company = ShippingCompany::findOrFail($id);
        $company->update($validated);
        return to_route("shipping_companies.index")->with(['success' => "تم تعديل بيانات الشركة بنجاخ"]);
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        };
        $company = ShippingCompany::findOrFail($id);
        if ($company->products->count() > 0) return response()->json(['success' => false, 'message' => "هناك منتوجات تابعة لهذه الشركة"]);
        $company->delete();
        return response()->json(['success' => true, 'message' => "تم حذف الشركة بنجاح"]);
    }
}
