<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Http\Request;
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
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->byStatus($status);
            })
            ->when($request->lead_source_id, function ($query, $sourceId) {
                $query->where('lead_source_id', $sourceId);
            })
            ->latest();

        $leads = $query->paginate(15)->withQueryString();
        $leadSources = LeadSource::where('is_active', true)->get()->toArray();

        return Inertia::render('CRM/Leads/Index', [
            'leads' => [
                'data' => LeadResource::collection($leads->items())->resolve(),
                'current_page' => $leads->currentPage(),
                'first_page_url' => $leads->url(1),
                'from' => $leads->firstItem(),
                'last_page' => $leads->lastPage(),
                'last_page_url' => $leads->url($leads->lastPage()),
                'next_page_url' => $leads->nextPageUrl(),
                'path' => $leads->path(),
                'per_page' => $leads->perPage(),
                'prev_page_url' => $leads->previousPageUrl(),
                'to' => $leads->lastItem(),
                'total' => $leads->total(),
            ],
            'leadSources' => $leadSources,
            'filters' => $request->only(['search', 'status', 'lead_source_id']),
            'dealStages' => \App\Models\DealStage::orderBy('order_column')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leadSources = LeadSource::where('is_active', true)->get()->toArray();
        $users = User::select('id', 'name', 'email')->get()->toArray();
        
        return Inertia::render('CRM/Leads/Create', [
            'leadSources' => $leadSources,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'owner_id' => $request->owner_id ?? auth()->id(),
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $lead->load(['leadSource', 'owner', 'creator', 'updater']);

        return Inertia::render('CRM/Leads/Show', [
            'lead' => (new LeadResource($lead))->resolve(),
            'dealStages' => \App\Models\DealStage::orderBy('order_column')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $leadSources = LeadSource::where('is_active', true)->get()->toArray();
        $users = User::select('id', 'name', 'email')->get()->toArray();
        
        return Inertia::render('CRM/Leads/Edit', [
            'lead' => (new LeadResource($lead->load(['leadSource', 'owner'])))->resolve(),
            'leadSources' => $leadSources,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update([
            ...$request->validated(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Convert lead to contact and optionally create a deal.
     * 
     * This is a KEY FEATURE for the CRM system.
     */
    public function convert(Request $request, Lead $lead)
    {
        // Validate conversion request
        $validated = $request->validate([
            'create_contact' => ['required', 'boolean'],
            'create_deal' => ['required', 'boolean'],
            'deal_title' => ['required_if:create_deal,true', 'string', 'max:255'],
            'deal_value' => ['required_if:create_deal,true', 'numeric', 'min:0'],
            'deal_stage_id' => ['required_if:create_deal,true', 'integer'],
            'expected_close_date' => ['nullable', 'date'],
        ]);

        // Ensure lead can be converted
        if ($lead->status === 'converted') {
            return back()->withErrors(['error' => 'This lead has already been converted.']);
        }

        try {
            DB::beginTransaction();

            $contact = null;
            $deal = null;

            // ====================================================================
            // CONTACT CREATION - Uncomment when Contact model is created
            // ====================================================================
            // if ($validated['create_contact']) {
            //     $contact = Contact::create([
            //         'first_name' => $lead->first_name,
            //         'last_name' => $lead->last_name,
            //         'email' => $lead->email,
            //         'phone' => $lead->phone,
            //         'mobile' => $lead->mobile,
            //         'company_name' => $lead->company_name,
            //         'job_title' => $lead->job_title,
            //         'address_line1' => $lead->address_line1,
            //         'address_line2' => $lead->address_line2,
            //         'city' => $lead->city,
            //         'state' => $lead->state,
            //         'postal_code' => $lead->postal_code,
            //         'country' => $lead->country,
            //         'notes' => $lead->notes,
            //         'tags' => $lead->tags,
            //         'custom_fields' => $lead->custom_fields,
            //         'owner_id' => $lead->owner_id,
            //         'created_by' => auth()->id(),
            //         'updated_by' => auth()->id(),
            //     ]);
            // }

            // ====================================================================
            // DEAL CREATION - Uncomment when Deal model is created
            // ====================================================================
            // if ($validated['create_deal'] && $contact) {
            //     $deal = Deal::create([
            //         'title' => $validated['deal_title'],
            //         'value' => $validated['deal_value'],
            //         'contact_id' => $contact->id,
            //         'stage_id' => $validated['deal_stage_id'],
            //         'expected_close_date' => $validated['expected_close_date'],
            //         'owner_id' => $lead->owner_id,
            //         'created_by' => auth()->id(),
            //         'updated_by' => auth()->id(),
            //     ]);
            // }

            // ====================================================================
            // UPDATE LEAD - Uncomment conversion tracking fields when tables exist
            // ====================================================================
            $lead->update([
                'status' => 'converted',
                // 'converted_to_contact_id' => $contact?->id,
                // 'converted_to_deal_id' => $deal?->id,
                // 'converted_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            // ====================================================================
            // REDIRECT - Update when Contact model exists
            // ====================================================================
            // return redirect()->route('contacts.show', $contact)
            //     ->with('success', 'Lead successfully converted to contact' . ($deal ? ' and deal created' : '') . '.');

            // Temporary redirect until contacts are implemented
            return redirect()->route('leads.show', $lead)
                ->with('success', 'Lead marked as converted. (Full conversion will be enabled when Contact and Deal modules are implemented)');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Failed to convert lead: ' . $e->getMessage()
            ]);
        }
    }
}
