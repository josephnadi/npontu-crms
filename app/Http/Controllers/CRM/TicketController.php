<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::with(['client', 'reporter', 'assignee'])
            ->when($request->search, function ($query, $search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('CRM/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    public function show(Ticket $ticket)
    {
        return Inertia::render('CRM/Tickets/Show', [
            'ticket' => $ticket->load(['client', 'reporter', 'assignee']),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Tickets/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'category' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['ticket_number'] = 'TIC-' . rand(1000, 9999);

        Ticket::create($validated);

        return redirect()->route('crm.tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function edit(Ticket $ticket)
    {
        return Inertia::render('CRM/Tickets/Edit', [
            'ticket' => $ticket,
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'category' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return redirect()->route('crm.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('crm.tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function convertToLead(Ticket $ticket)
    {
        try {
            $lead = $ticket->convertToLead();
            if ($lead === null) {
                return back()->with('error', 'Failed to convert ticket to lead. Lead creation failed.');
            }
            return redirect()->route('crm.leads.show', $lead->id)->with('success', 'Ticket converted to Lead successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert ticket: ' . $e->getMessage());
        }
    }

    public function convertToDeal(Ticket $ticket)
    {
        try {
            $deal = $ticket->convertToDeal();
            if ($deal === null) {
                return back()->with('error', 'Failed to convert ticket to deal. Deal creation failed.');
            }
            return redirect()->route('crm.deals.show', $deal->id)->with('success', 'Ticket converted to Deal successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert ticket: ' . $e->getMessage());
        }
    }

    public function convertToContact(Ticket $ticket)
    {
        try {
            $contact = $ticket->convertToContact();
            if ($contact === null) {
                return back()->with('error', 'Failed to convert ticket to contact. Contact creation failed.');
            }
            return redirect()->route('crm.contacts.show', $contact->id)->with('success', 'Ticket converted to Contact successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert ticket: ' . $e->getMessage());
        }
    }
}
