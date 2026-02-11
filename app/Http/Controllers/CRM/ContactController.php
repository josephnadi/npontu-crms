<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::with('client')
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->client_id, function ($query, $clientId) {
                $query->where('client_id', $clientId);
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'client_id']),
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Contacts/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'required|string|in:active,inactive',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $validated['owner_id'] = auth()->id();

        Contact::create($validated);

        return redirect()->route('crm.contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load(['client', 'owner', 'deals', 'activities' => function($query) {
            $query->orderBy('activity_date', 'desc')->with('owner');
        }, 'communications' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'engagements' => function($query) {
            $query->orderBy('engagement_date', 'desc')->with('user');
        }]);
        return Inertia::render('CRM/Contacts/Show', [
            'contact' => $contact,
        ]);
    }

    public function edit(Contact $contact)
    {
        return Inertia::render('CRM/Contacts/Edit', [
            'contact' => $contact,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        if ($contact->owner_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'required|string|in:active,inactive',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $contact->update($validated);

        Log::info('Contact updated', ['contact_id' => $contact->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        if ($contact->owner_id !== auth()->id()) {
            abort(403);
        }
        $contact->delete();
        Log::info('Contact deleted', ['contact_id' => $contact->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function convert(Contact $contact)
    {
        if ($contact->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $lead = $contact->convertToLead();
            if ($lead === null) {
                return back()->with('error', 'Failed to convert contact to lead. Lead creation failed.');
            }
            return redirect()->route('crm.leads.show', $lead->id)
                ->with('success', 'Contact successfully converted to lead.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert contact: ' . $e->getMessage());
        }
    }

    public function convertToTicket(Contact $contact)
    {
        if ($contact->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $ticket = $contact->convertToTicket();
            if ($ticket === null) {
                return back()->with('error', 'Failed to convert contact to ticket. Ticket creation failed.');
            }
            return redirect()->route('crm.tickets.show', $ticket->id)
                ->with('success', 'Contact successfully converted to ticket.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert contact: ' . $e->getMessage());
        }
    }
}
