<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with('owner')
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->source, function ($query, $source) {
                $query->where('source', $source);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Leads/Index', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'status', 'source']),
            'dealStages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Leads/Create', [
            'users' => User::select('id', 'first_name', 'last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100',
            'status' => 'required|string|in:new,contacted,qualified,converted,lost',
            'score' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
        ]);

        Lead::create($validated);

        return redirect()->route('crm.leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['owner', 'activities' => function($query) {
            $query->orderBy('activity_date', 'desc')->with('owner');
        }]);
        return Inertia::render('CRM/Leads/Show', [
            'lead' => $lead,
            'dealStages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function edit(Lead $lead)
    {
        return Inertia::render('CRM/Leads/Edit', [
            'lead' => $lead,
            'users' => User::select('id', 'first_name', 'last_name')->get(),
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        if ($lead->owner_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100',
            'status' => 'required|string|in:new,contacted,qualified,converted,lost',
            'score' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
        ]);

        $lead->update($validated);

        return redirect()->route('crm.leads.index')->with('success', 'Lead updated successfully.');
    }

    public function convert(Request $request, Lead $lead)
    {
        if ($lead->owner_id !== auth()->id()) {
            abort(403);
        }
        if ($lead->status === 'converted') {
            return redirect()->back()->with('error', 'This lead has already been converted.');
        }

        $request->validate([
            'create_client' => 'boolean',
            'create_deal' => 'boolean',
            'deal_title' => 'required_if:create_deal,true|nullable|string|max:255',
            'deal_value' => 'required_if:create_deal,true|nullable|numeric|min:0',
            'deal_stage_id' => 'required_if:create_deal,true|nullable|exists:deal_stages,id',
        ]);

        try {
            return DB::transaction(function () use ($request, $lead) {
                // 1. Create or Find Client
                $clientId = null;
                if ($request->create_client && $lead->company_name) {
                    // Try to find existing client by name or create new
                    $client = Client::firstOrCreate(
                        ['name' => $lead->company_name],
                        ['owner_id' => auth()->id()]
                    );
                    $clientId = $client->id;
                }

                // 2. Create Contact
                $contact = Contact::create([
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'job_title' => $lead->job_title,
                    'client_id' => $clientId,
                    'owner_id' => auth()->id(),
                    'notes' => $lead->notes,
                    'status' => 'active',
                ]);

                // 3. Create Deal
                if ($request->create_deal) {
                    $stage = DealStage::find($request->deal_stage_id);
                    Deal::create([
                        'title' => $request->deal_title,
                        'value' => $request->deal_value,
                        'currency' => 'GHS',
                        'deal_stage_id' => $request->deal_stage_id,
                        'contact_id' => $contact->id,
                        'client_id' => $clientId,
                        'owner_id' => auth()->id(),
                        'probability' => $stage->probability ?? 0,
                    ]);
                }

                // 4. Migrate Activities
                $lead->activities()->update([
                    'activityable_id' => $contact->id,
                    'activityable_type' => Contact::class,
                ]);

                // 5. Update Lead Status
                $lead->update(['status' => 'converted']);

                Log::info('Lead converted', ['lead_id' => $lead->id, 'user_id' => auth()->id()]);
                return redirect()->route('crm.contacts.index')->with('success', 'Lead converted to contact successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Lead conversion failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }

    public function destroy(Lead $lead)
    {
        if ($lead->owner_id !== auth()->id()) {
            abort(403);
        }
        $lead->delete();
        return redirect()->route('crm.leads.index')->with('success', 'Lead deleted successfully.');
    }
}
