<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreReplyRequest;
use App\Http\Requests\Support\StoreTicketRequest;
use App\Models\SupportReply;
use App\Models\SupportTicket;

class SupportTicketController extends Controller
{
    public function index()
    {
        $u = auth()->user();
        $role = optional($u->role)->name;
        $isStaff = in_array($role, ['admin','staff'], true);

        $tickets = SupportTicket::query()
            ->when(!$isStaff, fn($q) => $q->where('user_id', (int)$u->id))
            ->latest()
            ->paginate(12);

        return view('frontend.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.support.create');
    }

    public function store(StoreTicketRequest $form)
    {
        $validated = $form->validated();

        $ticket = SupportTicket::create([
            'user_id' => (int)auth()->id(),
            'subject' => $validated['subject'],
            'message' => trim((string)$validated['message']),
            'status'  => SupportTicket::STATUS_OPEN,
        ]);

        // Nếu có file đính kèm khi tạo vé -> lưu như 1 reply
        $file = request()->file('attachment');
        if ($file) {
            $path = $file->store('support_attachments', 'public');

            SupportReply::create([
                'support_ticket_id' => $ticket->id,
                'user_id'           => (int)auth()->id(),
                'message'           => null,
                'attachment'        => $path,
            ]);
        }

        return redirect()->route('support.show', $ticket)->with('success', 'Đã tạo yêu cầu hỗ trợ.');
    }

    public function show(SupportTicket $ticket)
    {
        $u = auth()->user();
        $isOwner = (int)$ticket->user_id === (int)$u->id;
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin','staff'], true);

        abort_unless($isOwner || $isStaff, 403);

        $ticket->load([
            'replies' => fn($q) => $q->with('user:id,name,email')->latest(),
        ]);

        return view('frontend.support.show', compact('ticket'));
    }

    /**
     * POST /ho-tro/{ticket}/rep  (name: support.reply)
     */
    public function reply(StoreReplyRequest $form, SupportTicket $ticket)
    {
        $u = auth()->user();
        $isOwner = (int)$ticket->user_id === (int)$u->id;
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin','staff'], true);

        abort_unless($isOwner || $isStaff, 403);

        $validated = $form->validated();
        $message   = isset($validated['message']) ? trim((string)$validated['message']) : null;
        if ($message === '') $message = null;

        $file = request()->file('attachment');
        $path = $file ? $file->store('support_attachments', 'public') : null;

        SupportReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => (int)auth()->id(),
            'message'           => $message,
            'attachment'        => $path,
        ]);

        // Nếu vé đã resolved/closed mà có tin nhắn mới -> mở lại
        if (in_array($ticket->status, [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED], true)) {
            $ticket->update(['status' => SupportTicket::STATUS_OPEN]);
        } else {
            $ticket->touch();
        }

        return back()->with('success', 'Đã gửi phản hồi.');
    }
}
