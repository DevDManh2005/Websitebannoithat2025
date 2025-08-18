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
        $tickets = SupportTicket::where('user_id', auth()->id())
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
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => trim((string) $validated['message']),
            'status'  => SupportTicket::STATUS_OPEN,
        ]);

        // Nếu người dùng có đính kèm khi tạo vé → lưu như 1 reply chỉ có file
        $req  = request(); // IDE nhận diện đúng Illuminate\Http\Request
        $file = $req->file('attachment');
        if ($file) {
            $path = $file->store('support_attachments', 'public');

            SupportReply::create([
                'support_ticket_id' => $ticket->id,
                'user_id'           => auth()->id(),
                'message'           => null,
                'attachment'        => $path,
            ]);
        }

        return redirect()->route('support.show', $ticket)->with('success', 'Đã tạo yêu cầu hỗ trợ.');
    }

    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $ticket->load([
            'replies' => fn ($q) => $q->with('user:id,name,email')->latest(),
        ]);

        return view('frontend.support.show', compact('ticket'));
    }

    /**
     * POST /ho-tro/{ticket}/rep  (name: support.reply)
     */
    public function reply(StoreReplyRequest $form, SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $validated = $form->validated();
        $message   = isset($validated['message']) ? trim((string) $validated['message']) : null;
        if ($message === '') {
            $message = null;
        }

        $req  = request(); // dùng Request “chuẩn” cho IDE
        $file = $req->file('attachment');
        $path = null;
        if ($file) {
            $path = $file->store('support_attachments', 'public');
        }

        SupportReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => auth()->id(),
            'message'           => $message,
            'attachment'        => $path,
        ]);

        // Người dùng nhắn thêm: nếu đang resolved/closed => mở lại
        if (in_array($ticket->status, [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED], true)) {
            $ticket->update(['status' => SupportTicket::STATUS_OPEN]);
        } else {
            $ticket->touch();
        }

        return back()->with('success', 'Đã gửi phản hồi.');
    }
}
