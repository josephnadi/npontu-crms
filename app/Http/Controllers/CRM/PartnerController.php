<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $partners = Partner::with('accountManager')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('CRM/Partners/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function show(Partner $partner)
    {
        return Inertia::render('CRM/Partners/Show', [
            'partner' => $partner->load(['accountManager', 'deals']),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Partners/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['user_id'] = auth()->id();

        Partner::create($validated);

        return redirect()->route('crm.partners.index')->with('success', 'Partner created successfully.');
    }

    public function edit(Partner $partner)
    {
        return Inertia::render('CRM/Partners/Edit', [
            'partner' => $partner,
        ]);
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $partner->update($validated);

        return redirect()->route('crm.partners.index')->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->route('crm.partners.index')->with('success', 'Partner deleted successfully.');
    }

    public function convertToLead(Partner $partner)
    {
        try {
            $lead = $partner->convertToLead();
            if ($lead === null) {
                return back()->with('error', 'Failed to convert partner to lead. Lead creation failed.');
            }
            return redirect()->route('crm.leads.show', $lead->id)
                ->with('success', 'Partner successfully converted back to lead.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert partner: ' . $e->getMessage());
        }
    }

    public function convertToTicket(Partner $partner)
    {
        try {
            $ticket = $partner->convertToTicket();
            if ($ticket === null) {
                return back()->with('error', 'Failed to convert partner to ticket. Ticket creation failed.');
            }
            return redirect()->route('crm.tickets.show', $ticket->id)
                ->with('success', 'Ticket successfully created from partner.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }
}
