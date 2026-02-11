<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communications = Communication::with(['communicable', 'owner'])
            ->when($request->search, function ($query, $search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('from_identifier', 'like', "%{$search}%")
                    ->orWhere('to_identifier', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->direction, function ($query, $direction) {
                $query->where('direction', $direction);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('CRM/Communications/Index', [
            'communications' => $communications,
            'filters' => $request->only(['search', 'type', 'direction']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Communication $communication)
    {
        $communication->load(['communicable', 'owner']);
        return Inertia::render('CRM/Communications/Show', [
            'communication' => $communication,
        ]);
    }
}
