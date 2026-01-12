<?php

namespace App\Services;

use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Models\Adset;
use App\Models\Campaign;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CampaignService
{
    public function index(Request $request)
    {
        if (!$request->adset)
            return to_route('adsets.index');
        $campaigns = Campaign::where('adset_id', $request->adset)->withSum('budgets', 'budget')->orderBy('active', "DESC")->orderBy('id', "DESC")->paginate(100);
        return view('campaigns.index', compact('campaigns'));
    }

    public function statisticsIndex(Request $request)
    {
        $query = Campaign::query()
            ->withStatistics($request->from, $request->to)
            ->filterByRequest($request);

        $campaigns = $query->orderBy('active', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(100)
            ->withQueryString();

        return view("campaigns.statisticsIndex", compact('campaigns'));
    }

    public function store(CreateCampaignRequest $request)
    {
        $validated = $request->validated();
        $page = Page::findOrFail($validated['page_id']);

        $baseUrl = url('/buy/' . $page->slug);
        $utmUrl = $baseUrl
            . '?utm_source=' . urlencode($validated['source'])
            . '&utm_campaign=' . urlencode($validated['campaign']);

        $campaign = Campaign::create([
            'campaign' => $validated['campaign'],
            'source' => $validated['source'],
            'page_id' => $page->id,
            'url' => $utmUrl,
            'adset_id' => $validated['adset_id'],
            'active' => true,
        ]);
        return response()->json(['success' => true, 'message' => "تم إضافة الحملة بنجاح", 'campaign' => $campaign]);
    }

    public function edit(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        return view("campaigns.edit", compact('campaign'));
    }

    public function update(UpdateCampaignRequest $request, string $id)
    {
        $validated = $request->validated();
        $campaign = Campaign::findOrFail($id);
        $campaign->campaign = $validated['campaign'];
        $campaign->source = $validated['source'];
        $campaign->url = explode('?', $campaign->url)[0] . "?utm_source=" . $validated['source'] . "&utm_campaign=" . $validated['campaign'];
        $campaign->active = true;
        $campaign->save();
        return to_route('campaigns.index', ['adset' => $campaign->adset_id])->with(['success' => "تم تعديل الحملة $campaign->campaign بنجاح", 'campaign' => $campaign]);
    }


    public function changeActive(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->active = !$campaign->active;
        $campaign->save();
        return response()->json(['success' => true, 'message' => 'تم تعديل حالة الحملة الإعلانية إلى ' . ($campaign->active ? "نشط" : "غير نشط"), 'active' => $campaign->active]);
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        }
        ;
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();
        return response()->json(['success' => true, 'message' => "تم حذف الحملة بنجاح"]);
    }
}
