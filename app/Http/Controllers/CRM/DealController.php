<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Deal;
use App\Models\DealStage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class DealController extends Controller
{
    public function export(Request $request)
    {
        $deals = Deal::with(['stage', 'contact', 'client'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->stage_id, function ($query, $stageId) {
                $query->where('deal_stage_id', $stageId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['ID', 'Title', 'Stage', 'Value', 'Probability', 'Status', 'Expected Close', 'Contact', 'Client'];
        $callback = function () use ($deals, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($deals as $d) {
                fputcsv($out, [
                    $d->id,
                    $d->title,
                    optional($d->stage)->name,
                    $d->value,
                    $d->probability,
                    $d->status,
                    optional($d->expected_close_date)?->format('Y-m-d'),
                    $d->contact ? ($d->contact->first_name . ' ' . $d->contact->last_name) : $d->contact_name,
                    $d->client ? $d->client->name : $d->client_name,
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, 'deals_export.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
    public function index(Request $request)
    {
        $deals = Deal::with(['stage', 'contact', 'client'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            })
            ->when($request->stage_id, function ($query, $stageId) {
                $query->where('deal_stage_id', $stageId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Deals/Index', [
            'deals' => $deals,
            'filters' => $request->only(['search', 'stage_id', 'status']),
            'stages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function pipeline()
    {
        $stages = DealStage::orderBy('order_column')->get();
        $deals = Deal::with(['stage', 'contact', 'client'])->where('status', 'open')->get();

        return Inertia::render('CRM/Deals/Pipeline', [
            'stages' => $stages,
            'deals' => $deals,
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Deals/Create', [
            'stages' => DealStage::orderBy('order_column')->get(),
            'contacts' => \App\Models\Contact::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'clients' => \App\Models\Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'client_id' => 'nullable|exists:clients,id',
            'contact_name' => 'nullable|string',
            'client_name' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $deal = Deal::create([
            ...$validated,
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
            'currency' => config('crm.currency', 'GHS'),
            'status' => 'open',
        ]);

        return redirect()->route('crm.deals.pipeline')->with('success', 'Deal created successfully');
    }

    public function updateStage(Request $request, Deal $deal)
    {
        $request->validate([
            'deal_stage_id' => 'required|exists:deal_stages,id',
        ]);

        $deal->update([
            'deal_stage_id' => $request->deal_stage_id,
        ]);

        Log::info('Deal stage updated', ['deal_id' => $deal->id, 'deal_stage_id' => $request->deal_stage_id, 'user_id' => auth()->id()]);
        return back()->with('success', 'Deal stage updated');
    }

    public function show(Deal $deal)
    {
        return Inertia::render('CRM/Deals/Show', [
            'deal' => $deal->load(['stage', 'contact', 'client', 'owner', 'activities' => function($query) {
                $query->orderBy('activity_date', 'desc')->with('owner');
            }, 'engagements' => function($query) {
                $query->orderBy('engagement_date', 'desc')->with('user');
            }]),
            'stages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function edit(Deal $deal)
    {
        return Inertia::render('CRM/Deals/Edit', [
            'deal' => $deal,
            'stages' => DealStage::orderBy('order_column')->get(),
            'contacts' => \App\Models\Contact::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'clients' => \App\Models\Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'client_id' => 'nullable|exists:clients,id',
            'probability' => 'required|integer|min:0|max:100',
            'contact_name' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'expected_close_date' => 'nullable|date',
            'status' => 'required|in:open,won,lost',
            'lost_reason' => 'nullable|string',
        ]);

        $deal->update($validated);

        Log::info('Deal updated', ['deal_id' => $deal->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.deals.index')->with('success', 'Deal updated successfully');
    }

    public function destroy(Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }
        $deal->delete();

        Log::info('Deal deleted', ['deal_id' => $deal->id, 'user_id' => auth()->id()]);
        return redirect()->route('crm.deals.index')->with('success', 'Deal deleted successfully');
    }

    public function convert(Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }

        if ($deal->status === 'won') {
            return back()->with('error', 'Deal is already won and converted.');
        }

        try {
            $project = $deal->convertToProject();
            if ($project === null) {
                return back()->with('error', 'Failed to convert deal to project. Project creation failed.');
            }
            return redirect()->route('crm.projects.show', $project->id)
                ->with('success', 'Deal converted to Project successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert deal: ' . $e->getMessage());
        }
    }

    public function convertToLead(Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $lead = $deal->convertToLead();
            if ($lead === null) {
                return back()->with('error', 'Failed to convert deal to lead. Lead creation failed.');
            }
            return redirect()->route('crm.leads.show', $lead->id)
                ->with('success', 'Deal converted back to Lead successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert deal: ' . $e->getMessage());
        }
    }

    public function convertToTicket(Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $ticket = $deal->convertToTicket();
            if ($ticket === null) {
                return back()->with('error', 'Failed to convert deal to ticket. Ticket creation failed.');
            }
            return redirect()->route('crm.tickets.show', $ticket->id)
                ->with('success', 'Deal successfully converted to ticket.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert deal: ' . $e->getMessage());
        }
    }

    public function convertToEngagement(Deal $deal)
    {
        if ($deal->owner_id !== auth()->id()) {
            abort(403);
        }

        try {
            $engagement = $deal->convertToEngagement();
            if ($engagement === null) {
                return back()->with('error', 'Failed to convert deal to engagement. Engagement creation failed.');
            }
            return redirect()->route('crm.engagements.show', $engagement->id)
                ->with('success', 'Deal successfully converted to engagement.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert deal: ' . $e->getMessage());
        }
    }
}
