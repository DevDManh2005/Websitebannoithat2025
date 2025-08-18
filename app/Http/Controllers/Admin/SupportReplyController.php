<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreReplyRequest;
use App\Models\SupportReply;
use App\Models\SupportTicket;

class SupportReplyController extends Controller
{
    /**
     * POST /admin/support-tickets/{support_ticket}/replies
     * Route name: admin.support_tickets.replies.store
     */
    public function store(StoreReplyRequest $form, SupportTicket $support_ticket)
    {
        $validated = $form->validated();

        // Nội dung text
        $message = isset($validated['message']) ? trim((string) $validated['message']) : null;
        if ($message === '') {
            $message = null;
        }

        // Upload file (dùng request() để tránh cảnh báo IDE & không cần inject Request)
        $path = null;
        $req  = request(); // \Illuminate\Http\Request
        if ($req->hasFile('attachment')) {
            $path = $req->file('attachment')->store('support_attachments', 'public');
        }

        // Lưu reply (admin/staff là user trong hệ thống)
        SupportReply::create([
            'support_ticket_id' => $support_ticket->id,
            'user_id'           => auth()->id(),
            'message'           => $message,
            'attachment'        => $path,
        ]);

        // Cập nhật trạng thái (tuỳ luật)
        if ($support_ticket->status === SupportTicket::STATUS_OPEN) {
            $support_ticket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        } else {
            $support_ticket->touch();
        }

        return redirect()
            ->route('admin.support_tickets.show', ['support_ticket' => $support_ticket->id])
            ->with('success', 'Đã gửi phản hồi.');
    }
}
