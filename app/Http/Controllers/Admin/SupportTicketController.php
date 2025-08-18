<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $q = SupportTicket::query()->with('user')->latest();

        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        if ($kw = $request->get('q')) {
            $q->where(function ($x) use ($kw) {
                $x->where('subject', 'like', "%{$kw}%")
                    ->orWhere('message', 'like', "%{$kw}%");
            });
        }

        $tickets = $q->paginate(15)->appends($request->query());

        return view('admins.support_tickets.index', compact('tickets'));
    }


    public function show(SupportTicket $support_ticket)
    {
        $support_ticket->load([
            'user:id,name,email',
            'replies' => fn($q) => $q->with('user:id,name,email')->latest(),
        ]);

        return view('admins.support_tickets.show', ['ticket' => $support_ticket]);
    }

    public function updateStatus(Request $request, SupportTicket $support_ticket)
    {
        $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $support_ticket->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái vé.');
    }
}
