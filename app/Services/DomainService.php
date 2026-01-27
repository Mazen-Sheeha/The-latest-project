<?php

namespace App\Services;

use App\Http\Requests\Domain\CreateDomainRequest;
use App\Http\Requests\Domain\UpdateDomainRequest;
use App\Models\Domain;

class DomainService
{
    public function index()
    {
        $domains = Domain::orderBy('id', 'DESC')->paginate(100);
        return view('domains.index', compact('domains'));
    }

    public function store(CreateDomainRequest $request)
    {
        $validated = $request->validated();

        if ($validated['setup_type'] === 'dns_record') {
            $validated['dns_record'] = json_encode($this->generateDNSRecord());
            $validated['status'] = 'pending';
        }

        Domain::create($validated);

        return to_route('domains.index')
            ->with(['success' => 'تم إضافة الدومين بنجاح']);
    }

    public function show(string $id)
    {
        $domain = Domain::findOrFail($id);
        return view('domains.show', compact('domain'));
    }

    public function edit(string $id)
    {
        $domain = Domain::findOrFail($id);
        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, string $id)
    {
        $validated = $request->validated();
        $domain = Domain::findOrFail($id);

        if ($validated['setup_type'] === 'dns_record') {
            $validated['dns_record'] = json_encode($this->generateDNSRecord());
            $validated['status'] = 'pending';
        }

        $domain->update($validated);

        return to_route('domains.index')
            ->with(['success' => 'تم تعديل الدومين بنجاح']);
    }

    public function destroy(string $id)
    {
        $domain = Domain::findOrFail($id);
        $domain->delete();

        return to_route('domains.index')
            ->with(['success' => 'تم حذف الدومين بنجاح']);
    }

    /**
     * Generate Hostinger-friendly DNS instructions
     */
    private function generateDNSRecord(): array
    {
        return [
            'provider' => 'hostinger',
            'records' => [
                [
                    'type' => 'CNAME',
                    'host' => '@',
                    'value' => config('app.platform_domain', 'trendocp.com'),
                    'ttl' => 'Auto',
                ],
                [
                    'type' => 'CNAME',
                    'host' => '*',
                    'value' => config('app.platform_domain', 'trendocp.com'),
                    'ttl' => 'Auto',
                ],
            ],
        ];
    }
}
