<?php

namespace App\Services;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\IncreaseProductStockRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use File;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->from && $request->to) {
            $from = $request->from;
            $to = $request->to;

            $query->with(['orders' => function ($q) use ($from, $to) {
                $q->whereBetween("orders.created_at", [$from, $to]);
            }]);

            $query->withCount(['orders' => function ($q) use ($from, $to) {
                $q->whereBetween("orders.created_at", [$from, $to]);
            }]);
        } else {
            $query->with('orders')->withCount('orders');
        }
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('code', 'LIKE', '%' . $request->search . '%');
            });
        }
        $products = $query
            ->orderBy('active', "DESC")
            ->orderBy('id', "DESC")
            ->paginate(40)
            ->withQueryString();

        return view('products.index', compact('products'));
    }


    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function create()
    {
        $companies = ShippingCompany::all();
        if ($companies->count() == 0) {
            if (Gate::allows('access-shipping-companies')) {
                return to_route('shipping_companies.index')->withErrors(['message' => 'لابد من إضافة شركة شحن واحدة على الأقل']);
            } else {
                return to_route('products.index')->withErrors(['message' => 'لابد من إضافة شركة شحن واحدة على الأقل']);
            }
        }
        return view('products.create', compact('companies'));
    }

    public function store(CreateProductRequest $request)
    {
        $validated = $request->validated();
        $file = $request->file('image');
        try {
            DB::beginTransaction();
            $validated['image'] = $file->store("public");
            Product::create($validated);
            DB::commit();
            return to_route("products.index")->with(['success' => "تم إضافة المنتوج بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => "حدث خطأ أثناء إضافة المنتوج"]);
        }
    }

    public function edit(string $id)
    {
        $companies = ShippingCompany::all();
        if ($companies->count() == 0) return to_route('shipping_companies.index')->withErrors(['message' => 'لابد من إضافة شركة شحن واحدة على الأقل']);
        $product = Product::findOrFail($id);
        return view('products.edit', compact('companies', 'product'));
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $validated = $request->validated();
        $file = $request->file('image');
        $product = Product::findOrFail($id);
        try {
            DB::beginTransaction();
            if (isset($validated['image'])) {
                $validated['image'] = $file->store('public');
                if (File::exists($product->image)) {
                    File::delete($product->image);
                }
            }
            $product->update($validated);
            DB::commit();
            return to_route("products.index")->with(['success' => 'تم تعديل المنتوج بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route("products.index")->withErrors(['message' => "حدث خطأأثثناء تعديل المنتوج"]);
        }
    }

    public function changeStatus(string $id)
    {
        $product = Product::findOrFail($id);
        $product->active = !$product->active;
        $product->save();
        return response()->json(['success' => true, 'message' => "تم تغيير حالة نشاط المنتوج"]);
    }

    public function increaseStock(IncreaseProductStockRequest $request, string $id)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($id);
        $product->update(['stock' => $product->stock + $validated['quantity']]);
        return response()->json(['success' => true, 'message' => "تم إضافة مخزون بنجاح"]);
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        };
        $product = Product::findOrFail($id);
        if ($product->hasOrders())  return response()->json(['success' => false, 'message' => "هذا المنتوج تابع لطلبات"]);
        try {
            DB::beginTransaction();
            if (File::exists($product->image)) {
                File::delete($product->image);
            }
            $product->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => "تم حذف المنتوج بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => "حدث خطأ أثناء حذف المنتوج"]);
        }
    }
}
