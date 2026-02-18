<?php

namespace App\Http\Controllers;

use App\Models\Pixel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PixelController extends Controller
{
    /**
     * Display a listing of the pixels.
     */
    public function index(): View
    {
        $pixels = Pixel::latest()->paginate(15);
        return view('pixels.index', compact('pixels'));
    }

    /**
     * Show the form for creating a new pixel.
     */
    public function create(): View
    {
        $types = Pixel::getTypes();
        return view('pixels.create', compact('types'));
    }

    /**
     * Store a newly created pixel in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:meta,google_ads,google_analytics,tiktok,snapchat,twitter,other',
            'pixel_id' => 'required|string|max:255',
            'code' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Pixel::create($validated);

        return redirect()->route('pixels.index')->with('success', 'تم إنشاء البكسل بنجاح');
    }

    /**
     * Show the form for editing the specified pixel.
     */
    public function edit(Pixel $pixel): View
    {
        $types = Pixel::getTypes();
        return view('pixels.edit', compact('pixel', 'types'));
    }

    /**
     * Update the specified pixel in storage.
     */
    public function update(Request $request, Pixel $pixel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:meta,google_ads,google_analytics,tiktok,snapchat,twitter,other',
            'pixel_id' => 'required|string|max:255',
            'code' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $pixel->update($validated);

        return redirect()->route('pixels.index')->with('success', 'تم تحديث البكسل بنجاح');
    }

    /**
     * Remove the specified pixel from storage.
     */
    public function destroy(Pixel $pixel): JsonResponse
    {
        $pixel->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف البكسل بنجاح'
        ]);
    }

    /**
     * Get all active pixels (API for AJAX).
     */
    public function getActivePixels(): JsonResponse
    {
        $pixels = Pixel::where('is_active', true)->get();
        return response()->json($pixels);
    }
}
