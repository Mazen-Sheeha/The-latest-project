<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageReview;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageService
{
    public function index(): View
    {
        $pages = Page::latest()->paginate(15);
        return view('pages.index', compact('pages'));
    }

    public function create(): View
    {
        $products = Product::select('id', 'name')->get();
        return view('pages.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStorePage($request);
        $validated['slug'] = Str::slug($validated['name']);

        // default reviews_count
        if (!isset($validated['reviews_count'])) {
            $validated['reviews_count'] = $validated['items_sold_count'] ?? 0;
        }

        $images = [];

        if ($request->hasFile('images')) {
            $order = $request->input('images_order');
            $order = $order ? json_decode($order, true) : [];

            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('pages_assets'), $filename);

                $position = $order[$index] ?? $index;
                $images[$position] = 'pages_assets/' . $filename;
            }


            // Sort by position
            ksort($images);
        }

        $validated['images'] = $images;

        $page = Page::create($validated);

        if ($request->has('reviews')) {
            foreach ($request->reviews as $index => $review) {

                $imagePath = null;

                if (
                    isset($review['reviewer_image']) &&
                    $review['reviewer_image'] instanceof \Illuminate\Http\UploadedFile
                ) {
                    $filename = time() . '_' . $review['reviewer_image']->getClientOriginalName();
                    $review['reviewer_image']->move(public_path('reviews'), $filename);
                    $imagePath = 'reviews/' . $filename;
                }

                $page->reviews()->create([
                    'reviewer_name' => $review['reviewer_name'],
                    'comment' => $review['comment'],
                    'stars' => $review['stars'],
                    'reviewer_image' => $imagePath,
                ]);
            }
        }

        if ($request->filled('upsell_products')) {
            $page->upsellProducts()->sync($request->upsell_products);
        }

        return redirect()
            ->route('pages.index')
            ->with('success', 'تم إنشاء صفحة البيع بنجاح');
    }
    public function show(Page $page): View
    {
        return view('pages.show', compact('page'));
    }

    public function edit(Page $page, Collection $products): View
    {
        $upsellProductIds = $page->upsellProducts->toArray();
        return view('pages.edit', compact('page', 'products', 'upsellProductIds'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $this->validateUpdatePage($request);

        $oldImages = $page->images ?? [];
        $newImages = $request->file('images', []);
        $finalImages = [];

        // If new images are uploaded, delete old images
        if (!empty($newImages)) {
            foreach ($oldImages as $img) {
                $path = public_path($img);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            foreach ($newImages as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('pages_assets'), $filename);
                $finalImages[] = 'pages_assets/' . $filename;
            }
        } else if ($request->filled('images_order')) {
            // Reorder old images if no new images
            $order = json_decode($request->images_order, true) ?? [];
            foreach ($order as $key) {
                if (str_starts_with($key, 'old_')) {
                    $index = (int) str_replace('old_', '', $key);
                    if (isset($oldImages[$index])) {
                        $finalImages[] = $oldImages[$index];
                    }
                } elseif (is_numeric($key)) {
                    $index = (int) $key;
                    if (isset($oldImages[$index])) {
                        $finalImages[] = $oldImages[$index];
                    }
                }
            }
        }

        if (!empty($finalImages)) {
            $validated['images'] = $finalImages;
        }

        $page->update($validated);

        if ($request->has('reviews')) {

            foreach ($request->reviews as $reviewData) {

                // DELETE
                if (!empty($reviewData['_delete']) && !empty($reviewData['id'])) {
                    $review = PageReview::find($reviewData['id']);

                    if ($review) {
                        if ($review->reviewer_image && file_exists(public_path($review->reviewer_image))) {
                            unlink(public_path($review->reviewer_image));
                        }
                        $review->delete();
                    }
                    continue;
                }

                // IMAGE
                $imagePath = null;
                if (
                    isset($reviewData['reviewer_image']) &&
                    $reviewData['reviewer_image'] instanceof \Illuminate\Http\UploadedFile
                ) {

                    $filename = time() . '_' . $reviewData['reviewer_image']->getClientOriginalName();
                    $reviewData['reviewer_image']->move(public_path('reviews'), $filename);
                    $imagePath = 'reviews/' . $filename;
                }

                // UPDATE
                if (!empty($reviewData['id'])) {
                    $review = PageReview::find($reviewData['id']);
                    if ($review) {
                        $review->update([
                            'reviewer_name' => $reviewData['reviewer_name'],
                            'comment' => $reviewData['comment'],
                            'stars' => $reviewData['stars'],
                            'reviewer_image' => $imagePath ?? $review->reviewer_image,
                        ]);
                    }
                }
                // CREATE
                else {
                    $page->reviews()->create([
                        'reviewer_name' => $reviewData['reviewer_name'],
                        'comment' => $reviewData['comment'],
                        'stars' => $reviewData['stars'],
                        'reviewer_image' => $imagePath,
                    ]);
                }
            }

            // Update count
            $page->update([
                'reviews_count' => $page->reviews()->count()
            ]);
        }

        $page->upsellProducts()->sync(
            $request->input('upsell_products', [])
        );

        return redirect()
            ->route('pages.index')
            ->with('success', 'تم تحديث صفحة البيع بنجاح');
    }

    public function destroy(Page $page)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json([
                'success' => false,
                'message' => 'ليس مسموحا لك بهذا'
            ], 403);
        }

        if (!empty($page->images)) {
            foreach ($page->images as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }

        // ================= DELETE REVIEWS + THEIR IMAGES =================
        foreach ($page->reviews as $review) {
            if ($review->reviewer_image) {
                $reviewImagePath = public_path($review->reviewer_image);
                if (file_exists($reviewImagePath)) {
                    unlink($reviewImagePath);
                }
            }
            $review->delete();
        }

        $page->delete();

        return request()->expectsJson()
            ? response()->json(['success' => true, 'message' => 'تم حذف الصفحة بنجاح'])
            : redirect()->route('pages.index')->with('success', 'تم حذف الصفحة بنجاح');
    }

    /**
     * =============================
     * Validation Logic
     * =============================
     */
    protected function validateStorePage(Request $request): array
    {
        $rules = $request->validate([
            // ================= BASIC INFO =================
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:pages,name',
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'theme_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'product_id' => ['required', 'exists:products,id'],

            // ================= SALE =================
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:original_price'],
            'sale_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_ends_at' => ['nullable', 'date', 'after:today'],

            // ================= STATS =================
            'items_sold_count' => ['nullable', 'integer', 'min:0'],
            'reviews_count' => ['nullable', 'integer', 'min:0'],

            // ================= IMAGES =================
            'images_order' => ['nullable', 'string'],

            // ================= REVIEWS =================
            'reviews' => ['nullable', 'array'],
            'reviews.*.reviewer_name' => ['required', 'string', 'max:255'],
            'reviews.*.comment' => ['required', 'string'],
            'reviews.*.stars' => ['required', 'integer', 'min:1', 'max:5'],
            'reviews.*.reviewer_image' => ['nullable', 'image', 'max:2048'],

            // ================= UPSELL PRODUCTS =================
            'upsell_products' => ['nullable', 'array'],
            'upsell_products.*' => ['exists:products,id'],
        ]);

        if ($request->hasFile('images')) {
            $rules['images'] = ['array'];
            $rules['images.*'] = [
                'file',
                'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif',
                'max:5120',
            ];
        }

        return $rules;
    }
    protected function validateUpdatePage(Request $request): array
    {
        $rules = $request->validate([
            // ================= BASIC INFO =================
            'name' => [
                'sometimes',
                'string',
                'max:255',
                'unique:pages,name,' . $request->route('page')->id
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'theme_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'product_id' => ['sometimes', 'exists:products,id'],

            // ================= SALE =================
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:original_price'],
            'sale_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_ends_at' => ['nullable', 'date', 'after:today'],

            // ================= STATS =================
            'items_sold_count' => ['nullable', 'integer', 'min:0'],
            'reviews_count' => ['nullable', 'integer', 'min:0'],

            // ================= IMAGES =================
            'images_order' => ['nullable', 'string'],

            // ================= REVIEWS =================
            'reviews' => ['nullable', 'array'],
            'reviews.*.reviewer_name' => ['required', 'string', 'max:255'],
            'reviews.*.comment' => ['required', 'string'],
            'reviews.*.stars' => ['required', 'integer', 'min:1', 'max:5'],
            'reviews.*.reviewer_image' => ['nullable', 'image', 'max:2048'],

            // ================= UPSELL PRODUCTS =================
            'upsell_products' => ['nullable', 'array'],
            'upsell_products.*' => ['exists:products,id'],
        ]);

        if ($request->hasFile('images')) {
            $rules['images'] = ['array'];
            $rules['images.*'] = [
                'file',
                'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif',
                'max:5120',
            ];
        }

        return $rules;
    }

}
