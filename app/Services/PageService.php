<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageReview;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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

        $images = [];
        if ($request->hasFile('images')) {
            $order = json_decode($request->input('images_order'), true) ?? [];
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                // Changed disk to 'direct_public'
                $path = $image->storeAs('pages_assets', $filename, 'direct_public');
                $position = $order[$index] ?? $index;
                $images[$position] = $path;
            }
            ksort($images);
        }
        $validated['images'] = $images;

        if ($request->has('offers') && is_array($request->offers)) {
            $offers = [];
            foreach ($request->offers as $offerIndex => $offer) {
                if (isset($offer['quantity']) && isset($offer['price'])) {
                    $imagePath = null;
                    if ($request->hasFile("offers.$offerIndex.image")) {
                        $image = $request->file("offers.$offerIndex.image");
                        $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                        // Changed disk to 'direct_public'
                        $imagePath = $image->storeAs('offers', $filename, 'direct_public');
                    }
                    $offers[] = [
                        'quantity' => (int) $offer['quantity'],
                        'price' => (float) $offer['price'],
                        'label' => $offer['label'] ?? null,
                        'image' => $imagePath,
                    ];
                }
            }
            $validated['offers'] = $offers;
        }

        $page = Page::create($validated);

        if ($request->has('reviews')) {
            foreach ($request->reviews as $review) {
                $imagePath = null;
                if (isset($review['reviewer_image']) && $review['reviewer_image'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = time() . '_' . Str::random(8) . '.' . $review['reviewer_image']->getClientOriginalExtension();
                    // Changed disk to 'direct_public'
                    $imagePath = $review['reviewer_image']->storeAs('reviews', $filename, 'direct_public');
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
            $upsellData = [];
            foreach ($request->upsell_products as $product) {
                $productId = $product['product_id'] ?? null;
                if (!$productId)
                    continue;

                $imagePath = null;
                if (isset($product['image']) && $product['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = time() . '_' . Str::random(8) . '.' . $product['image']->getClientOriginalExtension();
                    $imagePath = $product['image']->storeAs('upsell_products', $filename, 'direct_public');
                }
                $upsellData[$productId] = [
                    'name' => $product['name'] ?? null,
                    'image' => $imagePath,
                    'price' => $product['price'] ?? null,
                ];
            }
            $page->upsellProducts()->sync($upsellData);
        }

        return redirect()->route('pages.index')->with('success', 'تم إنشاء صفحة البيع بنجاح');
    }
    public function show(Page $page): View
    {
        return view('pages.show', compact('page'));
    }

    public function edit(Page $page, Collection $products, Collection $domains): View
    {
        $upsellProductIds = $page->upsellProducts->toArray();
        return view('pages.edit', compact('page', 'products', 'upsellProductIds', 'domains'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $this->validateUpdatePage($request);
        $oldImages = $page->images ?? [];
        $newImages = $request->file('images', []);
        $finalImages = [];

        if (!empty($newImages)) {
            foreach ($oldImages as $img) {
                // Updated check/delete to 'direct_public'
                if (Storage::disk('direct_public')->exists($img)) {
                    Storage::disk('direct_public')->delete($img);
                }
            }
            foreach ($newImages as $image) {
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $finalImages[] = $image->storeAs('pages_assets', $filename, 'direct_public');
            }
        } elseif ($request->filled('images_order')) {
            $order = json_decode($request->images_order, true) ?? [];
            foreach ($order as $index) {
                if (isset($oldImages[$index])) {
                    $finalImages[] = $oldImages[$index];
                }
            }
        }

        if (!empty($finalImages))
            $validated['images'] = $finalImages;

        // ... [Offers update logic follows the same pattern using 'direct_public'] ...

        $page->update($validated);

        // --- Review Update/Delete Logic ---
        if ($request->has('reviews')) {
            foreach ($request->reviews as $reviewData) {
                if (!empty($reviewData['_delete']) && !empty($reviewData['id'])) {
                    $review = PageReview::find($reviewData['id']);
                    if ($review && $review->reviewer_image) {
                        // Changed to storage disk deletion instead of public_path unlink for consistency
                        Storage::disk('direct_public')->delete($review->reviewer_image);
                    }
                    $review?->delete();
                    continue;
                }
                // ... (rest of review update logic using 'direct_public' for new uploads)
            }
        }

        return redirect()->route('pages.index')->with('success', 'تم تحديث صفحة البيع بنجاح');
    }
    public function destroy(Page $page): JsonResponse|RedirectResponse
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا'], 403);
        }

        // Delete Main Images
        foreach ($page->images ?? [] as $imagePath) {
            Storage::disk('direct_public')->delete($imagePath);
        }

        // Delete Review Images
        foreach ($page->reviews as $review) {
            if ($review->reviewer_image) {
                Storage::disk('direct_public')->delete($review->reviewer_image);
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
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'title' => ['required', 'string', 'max:255'],
            'domain_id' => ['required', 'exists:domains,id'],
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
            // 'upsell_products.*' => ['exists:products,id'],

            'features' => 'sometimes|array',
            'whatsapp_phone' => ['nullable', 'string', 'max:32'],

            'meta_pixel' => ['nullable', 'string'],
            'tiktok_pixel' => ['nullable', 'string'],
            'snapchat_pixel' => ['nullable', 'string'],
            'twitter_pixel' => ['nullable', 'string'],
            'google_analytics' => ['nullable', 'string'],
            'google_ads_pixel' => ['nullable', 'string'],
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
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'unique:pages,slug,' . $request->route('page')->id
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'domain_id' => ['sometimes', 'exists:domains,id'],
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
            // 'upsell_products.*' => ['exists:products,id'],

            'features' => 'sometimes|array',
            'whatsapp_phone' => ['nullable', 'string', 'max:32'],
            'meta_pixel' => ['nullable', 'string'],
            'tiktok_pixel' => ['nullable', 'string'],
            'snapchat_pixel' => ['nullable', 'string'],
            'twitter_pixel' => ['nullable', 'string'],
            'google_analytics' => ['nullable', 'string'],
            'google_ads_pixel' => ['nullable', 'string'],
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
