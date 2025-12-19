<?php

namespace App\Services;

use App\Http\Requests\Website\CreateWebsiteRequest;
use App\Http\Requests\Website\UpdateWebsiteRequest;
use App\Models\Website;

class WebsiteService
{
    public function index()
    {
        $websites = Website::orderBy('id', "DESC")->paginate(100);
        return view("websites.index", compact('websites'));
    }

    public function store(CreateWebsiteRequest $request)
    {
        $validated = $request->validated();
        $website = Website::create($validated);
        return response()->json(['success' => true, 'message' => 'تم إضافة دومين بنجاح', 'website' => $website]);
    }

    public function edit(string $id)
    {
        $website = Website::findOrFail($id);
        return view("websites.edit", compact('website'));
    }

    public function update(UpdateWebsiteRequest $request, string $id)
    {
        $validated = $request->validated();
        $website = Website::findOrFail($id);
        $website->domain = $validated['domain'];
        $website->key = $validated['key'];
        $website->save();
        return to_route('websites.index')->with(['success' => "تم تعديل الدومين بنجاح"]);
    }

    public function destroy(string $id)
    {
        $website = Website::findOrFail($id);
        $website->delete();
        return response()->json(['success' => true, 'message' => "تم حذف الدومين بنجاح"]);
    }
}
