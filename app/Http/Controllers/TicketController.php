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

        if ($request->input('assignment_mode') === 'auto' || empty($validated['attendant_id'])) {
            $lessBusyAttendant = Attendant::withCount(['tickets' => function ($query) {
                $query->whereNotIn('status', ['resolved', 'closed']); 
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

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'attendant_id' => 'nullable|exists:attendants,id'
        ]);

        $ticket->update($validated);

        return redirect()->back();
    }
}