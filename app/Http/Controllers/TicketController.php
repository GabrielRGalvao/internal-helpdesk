<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(): Response
    {
        $tickets = Ticket::with('attendant')->latest()->get();
        $attendants = Attendant::all();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'attendants' => $attendants
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attendant_id' => 'nullable|exists:attendants,id',
        ]);

        if (empty($validated['attendant_id'])) {
            $lessBusyAttendant = Attendant::withCount(['tickets' => function ($query) {
                $query->where('status', '!=', 'closed'); 
            }])
            ->orderBy('tickets_count', 'asc')
            ->first();

            if ($lessBusyAttendant) {
                $validated['attendant_id'] = $lessBusyAttendant->id;
            }
        }

        $validated['status'] = 'open';

        Ticket::create($validated);

        return redirect()->back();
    }
}