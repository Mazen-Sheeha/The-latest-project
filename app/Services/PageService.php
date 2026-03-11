<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageReview;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageService
{
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Page::query()
            ->with(['product', 'domain', 'pixels']);

        // Search by name or title
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Filter by domain
        if ($domainId = $request->get('domain_id')) {
            $query->where('domain_id', $domainId);
        }

        // Filter by product
        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        // Filter by pixel
        if ($pixelId = $request->get('pixel_id')) {
            $query->whereHas('pixels', fn($q) => $q->where('pixels.id', $pixelId));
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }

        return $query->latest()->paginate(20)->withQueryString();
    }

    public function create(): View
    {
        $products = Product::select('id', 'name')->get();
        return view('pages.create', compact('products'));
    }

    public function store(array $validatedData): RedirectResponse
    {
        $images = [];
        if (isset($validatedData['images'])) {
            $order = json_decode($validatedData['images_order'], true) ?? [];
            foreach ($validatedData['images'] as $index => $image) {
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('pages_assets', $filename, 'direct_public');
                $position = $order[$index] ?? $index;
                $images[$position] = $path;
            }
            ksort($images);
        }
        $validatedData['images'] = $images;

        if (isset($validatedData['offers']) && is_array($validatedData['offers'])) {
            $offers = [];
            foreach ($validatedData['offers'] as $offer) {
                if (isset($offer['quantity']) && isset($offer['price'])) {
                    $imagePath = null;

                    if (isset($offer['image']) && $offer['image'] instanceof UploadedFile) {
                        $filename = time() . '_' . Str::random(8) . '.' . $offer['image']->getClientOriginalExtension();
                        $imagePath = $offer['image']->storeAs('offers', $filename, 'direct_public');
                    }

                    $offers[] = [
                        'quantity' => (int) $offer['quantity'],
                        'price' => (float) $offer['price'],
                        'label' => $offer['label'] ?? null,
                        'image' => $imagePath,
                        'selected' => !empty($offer['selected']),
                    ];
                }
            }
            $validatedData['offers'] = $offers;
        }

        $activeFeatures = $validatedData['features_active'] ?? [];
        $featureLabels = $validatedData['features_labels'] ?? [];

        $features = [];
        foreach ($activeFeatures as $value) {
            $features[$value] = $featureLabels[$value] ?? $value;
        }

        $validatedData['features'] = $features;
        unset($validatedData['features_active'], $validatedData['features_labels']);

        $page = Page::create($validatedData);

        if (isset($validatedData['reviews']) && is_array($validatedData['reviews'])) {
            foreach ($validatedData['reviews'] as $review) {
                $imagePath = null;
                if (isset($review['reviewer_image']) && $review['reviewer_image'] instanceof UploadedFile) {
                    $filename = time() . '_' . Str::random(8) . '.' . $review['reviewer_image']->getClientOriginalExtension();
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

        if (isset($validatedData['upsell_products']) && is_array($validatedData['upsell_products'])) {
            $upsellData = [];
            foreach ($validatedData['upsell_products'] as $product) {
                $productId = $product['product_id'] ?? null;
                if (!$productId)
                    continue;

                $imagePath = null;
                if (isset($product['image']) && $product['image'] instanceof UploadedFile) {
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

        if (isset($validatedData['pixels']) && is_array($validatedData['pixels'])) {
            $page->pixels()->sync($validatedData['pixels']);
        }

        return redirect()->route('pages.index')->with('success', 'تم إنشاء صفحة البيع بنجاح');
    }
    public function show(Page $page): View
    {
        return view('pages.show', compact('page'));
    }

    public function edit(Page $page, Collection $products, Collection $domains, Collection $pixels): View
    {
        $upsellProductIds = $page->upsellProducts->toArray();
        return view('pages.edit', compact('page', 'products', 'upsellProductIds', 'domains', 'pixels'));
    }

    public function update(array $validatedData, Page $page): RedirectResponse
    {
        Log::info('Updating page with data: ', $validatedData);
        // ================= IMAGES =================
        $oldImages = $page->images ?? [];
        $newImages = $validatedData['images'] ?? [];
        $finalImages = [];

        if (!empty($newImages)) {
            // Delete old images and upload new ones
            foreach ($oldImages as $img) {
                if (Storage::disk('direct_public')->exists($img)) {
                    Storage::disk('direct_public')->delete($img);
                }
            }
            foreach ($newImages as $image) {
                $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $finalImages[] = $image->storeAs('pages_assets', $filename, 'direct_public');
            }
        } elseif (isset($validatedData['images_order'])) {
            // Reorder existing images
            $order = json_decode($validatedData['images_order'], true) ?? [];
            foreach ($order as $index) {
                if (isset($oldImages[$index])) {
                    $finalImages[] = $oldImages[$index];
                }
            }
        } else {
            // Keep existing images untouched
            $finalImages = $oldImages;
        }

        $activeFeatures = $validatedData['features_active'] ?? [];
        $featureLabels = $validatedData['features_labels'] ?? [];

        $features = [];
        foreach ($activeFeatures as $value) {
            $features[$value] = $featureLabels[$value] ?? $value;
        }

        $validatedData['features'] = $features;
        unset($validatedData['features_active'], $validatedData['features_labels']);

        $validatedData['images'] = $finalImages;

        // ================= OFFERS =================
        if (isset($validatedData['offers']) && is_array($validatedData['offers'])) {
            $offers = [];
            $oldOffers = $page->offers ?? [];

            foreach ($validatedData['offers'] as $index => $offer) {
                if (!isset($offer['quantity']) || !isset($offer['price'])) {
                    continue;
                }

                $imagePath = $oldOffers[$index]['image'] ?? null;

                // New image uploaded — delete old one and store new
                if (isset($offer['image']) && $offer['image'] instanceof UploadedFile) {
                    if ($imagePath && Storage::disk('direct_public')->exists($imagePath)) {
                        Storage::disk('direct_public')->delete($imagePath);
                    }
                    $filename = time() . '_' . Str::random(8) . '.' . $offer['image']->getClientOriginalExtension();
                    $imagePath = $offer['image']->storeAs('offers', $filename, 'direct_public');
                }

                // Image was deleted via the delete button (hidden input cleared)
                if (isset($offer['existing_image']) && empty($offer['existing_image']) && $imagePath) {
                    if (Storage::disk('direct_public')->exists($imagePath)) {
                        Storage::disk('direct_public')->delete($imagePath);
                    }
                    $imagePath = null;
                }

                $offers[] = [
                    'quantity' => (int) $offer['quantity'],
                    'price' => (float) $offer['price'],
                    'label' => $offer['label'] ?? null,
                    'image' => $imagePath,
                    'selected' => !empty($offer['selected']),
                ];
            }

            // Delete images of removed offers
            foreach ($oldOffers as $oldIndex => $oldOffer) {
                $stillExists = collect($validatedData['offers'])->has($oldIndex);
                if (!$stillExists && !empty($oldOffer['image'])) {
                    if (Storage::disk('direct_public')->exists($oldOffer['image'])) {
                        Storage::disk('direct_public')->delete($oldOffer['image']);
                    }
                }
            }

            $validatedData['offers'] = $offers;
        } else {
            // All offers removed — delete their images
            foreach ($page->offers ?? [] as $oldOffer) {
                if (!empty($oldOffer['image']) && Storage::disk('direct_public')->exists($oldOffer['image'])) {
                    Storage::disk('direct_public')->delete($oldOffer['image']);
                }
            }
            $validatedData['offers'] = [];
        }

        // ================= REVIEWS =================
        if (isset($validatedData['reviews']) && is_array($validatedData['reviews'])) {
            foreach ($validatedData['reviews'] as $reviewData) {

                // Delete marked reviews
                if (!empty($reviewData['_delete']) && !empty($reviewData['id'])) {
                    $review = PageReview::find($reviewData['id']);
                    if ($review) {
                        if ($review->reviewer_image && Storage::disk('direct_public')->exists($review->reviewer_image)) {
                            Storage::disk('direct_public')->delete($review->reviewer_image);
                        }
                        $review->delete();
                    }
                    continue;
                }

                $imagePath = null;
                if (isset($reviewData['reviewer_image']) && $reviewData['reviewer_image'] instanceof UploadedFile) {
                    $filename = time() . '_' . Str::random(8) . '.' . $reviewData['reviewer_image']->getClientOriginalExtension();
                    $imagePath = $reviewData['reviewer_image']->storeAs('reviews', $filename, 'direct_public');
                }

                // Update existing review
                if (!empty($reviewData['id'])) {
                    $review = PageReview::find($reviewData['id']);
                    if ($review) {
                        // Delete old image if new one uploaded
                        if ($imagePath && $review->reviewer_image && Storage::disk('direct_public')->exists($review->reviewer_image)) {
                            Storage::disk('direct_public')->delete($review->reviewer_image);
                        }
                        $review->update([
                            'reviewer_name' => $reviewData['reviewer_name'],
                            'comment' => $reviewData['comment'],
                            'stars' => $reviewData['stars'],
                            'reviewer_image' => $imagePath ?? $review->reviewer_image,
                        ]);
                    }
                    continue;
                }

                // Create new review
                $page->reviews()->create([
                    'reviewer_name' => $reviewData['reviewer_name'],
                    'comment' => $reviewData['comment'],
                    'stars' => $reviewData['stars'],
                    'reviewer_image' => $imagePath,
                ]);
            }
        }

        // ================= UPSELL PRODUCTS =================
        if (isset($validatedData['upsell_products']) && is_array($validatedData['upsell_products'])) {
            Log::info('Processing upsell products: ', $validatedData['upsell_products']);
            $upsellData = [];

            foreach ($validatedData['upsell_products'] as $product) {
                $productId = (int) ($product['product_id'] ?? null);
                if (!$productId)
                    continue;

                // Get existing pivot image if any
                $existingPivot = $page->upsellProducts->firstWhere('id', $productId);
                $imagePath = $existingPivot?->pivot->image ?? null;

                if (isset($product['image']) && $product['image'] instanceof UploadedFile) {
                    // Delete old pivot image
                    if ($imagePath && Storage::disk('direct_public')->exists($imagePath)) {
                        Storage::disk('direct_public')->delete($imagePath);
                    }
                    $filename = time() . '_' . Str::random(12) . '.' . $product['image']->getClientOriginalExtension();
                    $imagePath = $product['image']->storeAs('upsell_products', $filename, 'direct_public');
                }

                $upsellData[$productId] = [
                    'name' => $product['name'] ?? null,
                    'image' => $imagePath,
                    'price' => $product['price'] ?? null,
                ];
            }

            // Delete images of removed upsell products
            foreach ($page->upsellProducts as $existingProduct) {
                if (!isset($upsellData[$existingProduct->id]) && $existingProduct->pivot->image) {
                    if (Storage::disk('direct_public')->exists($existingProduct->pivot->image)) {
                        Storage::disk('direct_public')->delete($existingProduct->pivot->image);
                    }
                }
            }

            $page->upsellProducts()->sync($upsellData);
        } else {
            // All upsell products removed — delete their images
            foreach ($page->upsellProducts as $existingProduct) {
                if ($existingProduct->pivot->image && Storage::disk('direct_public')->exists($existingProduct->pivot->image)) {
                    Storage::disk('direct_public')->delete($existingProduct->pivot->image);
                }
            }
            $page->upsellProducts()->detach();
        }

        // ================= PIXELS =================
        if (isset($validatedData['pixels']) && is_array($validatedData['pixels'])) {
            $page->pixels()->sync($validatedData['pixels']);
        } else {
            $page->pixels()->detach();
        }

        // ================= SAVE PAGE =================
        // Remove relation keys before update to avoid mass assignment issues
        $pageData = collect($validatedData)->except([
            'reviews',
            'upsell_products',
            'pixels',
            'images_order',
        ])->toArray();

        $page->update($pageData);

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

    public function duplicate(Page $page): Page
    {
        $newPage = $page->replicate();
        $newPage->name = $page->name . ' (نسخة)';
        $newPage->slug = $this->uniqueSlug($page->slug);
        $newPage->is_active = false;
        $newPage->save();

        if ($page->pixels->isNotEmpty()) {
            $newPage->pixels()->sync($page->pixels->pluck('id')->toArray());
        }

        foreach ($page->reviews as $review) {
            $newReview = $review->replicate();
            $newReview->page_id = $newPage->id;
            $newReview->save();
        }

        if ($page->upsellProducts->isNotEmpty()) {
            $upsellSync = [];
            foreach ($page->upsellProducts as $product) {
                $upsellSync[$product->id] = [
                    'name' => $product->pivot->name,
                    'price' => $product->pivot->price,
                    'image' => $product->pivot->image,
                ];
            }
            $newPage->upsellProducts()->sync($upsellSync);
        }

        return $newPage;
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug . '-copy';
        $count = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-copy-' . $count;
            $count++;
        }

        return $slug;
    }
}
