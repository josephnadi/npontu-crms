<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\MarketingAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MarketingAutomationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $automations = MarketingAutomation::where('owner_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/MarketingAutomations/Index', [
            'automations' => $automations,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/MarketingAutomations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email_campaign,drip_sequence,newsletter,sms_alert,whatsapp_broadcast,omnichannel_sequence',
            'status' => 'required|in:draft,active,paused,completed',
            'description' => 'nullable|string',
            'trigger_config' => 'nullable|array',
            'content_config' => 'nullable|array',
        ]);

        $validated['owner_id'] = Auth::id();

        MarketingAutomation::create($validated);

        return redirect()->route('crm.marketing-automations.index')->with('success', 'Automation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingAutomation $marketingAutomation)
    {
        $this->authorizeOwner($marketingAutomation);

        return Inertia::render('CRM/MarketingAutomations/Show', [
            'automation' => $marketingAutomation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingAutomation $marketingAutomation)
    {
        $this->authorizeOwner($marketingAutomation);

        return Inertia::render('CRM/MarketingAutomations/Edit', [
            'automation' => $marketingAutomation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketingAutomation $marketingAutomation)
    {
        $this->authorizeOwner($marketingAutomation);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email_campaign,drip_sequence,newsletter,sms_alert,whatsapp_broadcast,omnichannel_sequence',
            'status' => 'required|in:draft,active,paused,completed',
            'description' => 'nullable|string',
            'trigger_config' => 'nullable|array',
            'content_config' => 'nullable|array',
        ]);

        $marketingAutomation->update($validated);

        return redirect()->route('crm.marketing-automations.index')->with('success', 'Automation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingAutomation $marketingAutomation)
    {
        $this->authorizeOwner($marketingAutomation);
        $marketingAutomation->delete();

        return redirect()->route('crm.marketing-automations.index')->with('success', 'Automation deleted successfully.');
    }

    protected function authorizeOwner(MarketingAutomation $automation)
    {
        if ($automation->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
