<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreLeadRequest;
use App\Http\Requests\CRM\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Client;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['leadSource', 'owner'])
            ->where('owner_id', Auth::id());

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lead_source_id) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Calculate stats
        $stats = [
            'total' => Lead::where('owner_id', Auth::id())->count(),
            'new' => Lead::where('owner_id', Auth::id())->where('status', 'new')->count(),
            'qualified' => Lead::where('owner_id', Auth::id())->where('status', 'qualified')->count(),
            'converted' => Lead::where('owner_id', Auth::id())->where('status', 'converted')->count(),
        ];

        return Inertia::render('CRM/Leads/Index', [
            'leads' => $leads,
            'leadSources' => LeadSource::where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['search', 'status', 'lead_source_id']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Leads/Create', [
            'leadSources' => LeadSource::where('is_active', true)->orderBy('name')->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        $validated = $request->validated();
        $validated['owner_id'] = $validated['owner_id'] ?? Auth::id();
        $validated['created_by'] = Auth::id();

        $lead = Lead::create($validated);

        return redirect()->route('crm.leads.index')->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $this->authorizeOwner($lead);
        $lead->load(['leadSource', 'owner', 'creator', 'activities' => function($q) {
            $q->orderBy('activity_date', 'desc')->with('owner');
        }, 'engagements' => function($q) {
            $q->orderBy('engagement_date', 'desc')->with('user');
        }]);

        return Inertia::render('CRM/Leads/Show', [
            'lead' => $lead,
            'dealStages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $this->authorizeOwner($lead);

        return Inertia::render('CRM/Leads/Edit', [
            'lead' => $lead,
            'leadSources' => LeadSource::where('is_active', true)->orderBy('name')->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $this->authorizeOwner($lead);
        
        $validated = $request->validated();
        $validated['updated_by'] = Auth::id();

        $lead->update($validated);

        return redirect()->route('crm.leads.index')->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        $this->authorizeOwner($lead);
        $lead->delete();

        return redirect()->route('crm.leads.index')->with('success', 'Lead deleted successfully.');
    }

    /**
     * Convert lead to client and optionally create a deal.
     */
    public function convert(Request $request, Lead $lead)
    {
        $this->authorizeOwner($lead);

        $validated = $request->validate([
            'create_deal' => 'required|boolean',
            'deal_title' => 'required_if:create_deal,true|nullable|string|max:255',
            'deal_value' => 'required_if:create_deal,true|nullable|numeric|min:0',
            'deal_stage_id' => 'required_if:create_deal,true|nullable|exists:deal_stages,id',
        ]);

        if ($lead->status === 'converted') {
            return back()->with('error', 'Lead is already converted.');
        }

        try {
            $client = $lead->convertToClient($validated);

            return redirect()->route('crm.clients.show', $client->id)
                ->with('success', 'Lead successfully converted to client.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }

    public function convertToPartner(Lead $lead)
    {
        $this->authorizeOwner($lead);

        if ($lead->status === 'converted') {
            return back()->with('error', 'Lead is already converted.');
        }

        try {
            $partner = $lead->convertToPartner();

            return redirect()->route('crm.partners.index')
                ->with('success', 'Lead successfully converted to Partner.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }

    public function convertToTicket(Lead $lead)
    {
        $this->authorizeOwner($lead);

        try {
            $ticket = $lead->convertToTicket();
            return redirect()->route('crm.tickets.show', $ticket->id)
                ->with('success', 'Lead successfully converted to Ticket.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert lead to ticket: ' . $e->getMessage());
        }
    }

    protected function authorizeOwner(Lead $lead)
    {
        if ($lead->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
