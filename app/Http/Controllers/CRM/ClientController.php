<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::withCount('contacts')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Clients/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['owner_id'] = auth()->id();

        Client::create($validated);

        return redirect()->route('crm.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['contacts', 'owner', 'deals.stage', 'activities' => function($query) {
            $query->orderBy('activity_date', 'desc')->with('owner');
        }, 'engagements' => function($query) {
            $query->orderBy('engagement_date', 'desc')->with('user');
        }]);
        
        return Inertia::render('CRM/Clients/Show', [
            'client' => $client,
        ]);
    }

    public function edit(Client $client)
    {
        return Inertia::render('CRM/Clients/Edit', [
            'client' => $client
        ]);
    }

    public function update(Request $request, Client $client)
    {
        if ($client->owner_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        Log::info('Client updated', ['client_id' => $client->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        if ($client->owner_id !== auth()->id()) {
            abort(403);
        }
        $client->delete();

        Log::info('Client deleted', ['client_id' => $client->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Convert client back to lead
     */
    public function convert(Client $client)
    {
        if ($client->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $lead = $client->convertToLead();
            return redirect()->route('crm.leads.show', $lead->id)
                ->with('success', 'Client successfully converted back to lead.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert client: ' . $e->getMessage());
        }
    }

    public function convertToPartner(Client $client)
    {
        if ($client->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $partner = $client->convertToPartner();
            return redirect()->route('crm.partners.index')
                ->with('success', 'Client successfully converted to partner.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert client: ' . $e->getMessage());
        }
    }

    public function convertToTicket(Client $client)
    {
        if ($client->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $ticket = $client->convertToTicket();
            return redirect()->route('crm.tickets.show', $ticket->id)
                ->with('success', 'Client successfully converted to ticket.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert client: ' . $e->getMessage());
        }
    }
}
