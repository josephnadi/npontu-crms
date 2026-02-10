<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['client', 'project'])
            ->where('owner_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Invoices/Create', [
            'clients' => Client::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
            'projects' => Project::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
            'nextInvoiceNumber' => 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });
            
            $tax_total = $subtotal * 0.1; // Example 10% tax
            $total_amount = $subtotal + $tax_total;

            $invoice = Invoice::create([
                'invoice_number' => $validated['invoice_number'],
                'client_id' => $validated['client_id'],
                'project_id' => $validated['project_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'subtotal' => $subtotal,
                'tax_total' => $tax_total,
                'total_amount' => $total_amount,
                'owner_id' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('crm.invoices.index')->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $this->authorizeOwner($invoice);
        $invoice->load(['client', 'project', 'items']);

        return Inertia::render('CRM/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorizeOwner($invoice);
        $invoice->load('items');

        return Inertia::render('CRM/Invoices/Edit', [
            'invoice' => $invoice,
            'clients' => Client::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
            'projects' => Project::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeOwner($invoice);

        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });
            
            $tax_total = $subtotal * 0.1;
            $total_amount = $subtotal + $tax_total;

            $invoice->update([
                'invoice_number' => $validated['invoice_number'],
                'client_id' => $validated['client_id'],
                'project_id' => $validated['project_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'subtotal' => $subtotal,
                'tax_total' => $tax_total,
                'total_amount' => $total_amount,
            ]);

            $invoice->items()->delete();
            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('crm.invoices.index')->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorizeOwner($invoice);
        $invoice->delete();

        return redirect()->route('crm.invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    protected function authorizeOwner(Invoice $invoice)
    {
        if ($invoice->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
