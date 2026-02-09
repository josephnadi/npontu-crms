<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Deal;
use App\Models\DealStage;
use Inertia\Inertia;

class DealController extends Controller
{
    public function index()
    {
        return Inertia::render('CRM/Deals/Index', [
            'deals' => Deal::with('stage')->latest()->get(),
        ]);
    }

    public function pipeline()
    {
        $stages = DealStage::orderBy('order_column')->get();
        $deals = Deal::with('stage')->get();

        return Inertia::render('CRM/Deals/Pipeline', [
            'stages' => $stages,
            'deals' => $deals,
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Deals/Create', [
            'stages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|numeric',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'contact_name' => 'nullable|string',
            'client_name' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
        ]);

        $deal = Deal::create([
            ...$validated,
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
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

        return back()->with('success', 'Deal stage updated');
    }

    public function show(Deal $deal)
    {
        return Inertia::render('CRM/Deals/Show', [
            'deal' => $deal->load(['stage', 'activities' => function($query) {
                $query->orderBy('activity_date', 'desc');
            }]),
        ]);
    }

    public function edit(Deal $deal)
    {
        return Inertia::render('CRM/Deals/Edit', [
            'deal' => $deal,
            'stages' => DealStage::orderBy('order_column')->get(),
        ]);
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'probability' => 'required|integer|min:0|max:100',
            'contact_name' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'expected_close_date' => 'nullable|date',
            'status' => 'required|in:open,won,lost',
        ]);

        $deal->update($validated);

        return redirect()->route('crm.deals.index')->with('success', 'Deal updated successfully');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()->route('crm.deals.index')->with('success', 'Deal deleted successfully');
    }
}
