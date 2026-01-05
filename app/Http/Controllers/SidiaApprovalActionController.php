<?php

namespace App\Http\Controllers;

use App\Models\SidiaApproval;
use App\Models\SidiaApprovalThread;
use App\Models\SidiaApprovalMessage;
use App\Models\SidiaApprovalMessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidiaApprovalActionController extends Controller
{
    public function inquiry(Request $request, SidiaApproval $approval)
    {
        $this->authorize('inquiry', $approval);

        $request->validate([
            'comment' => 'required|string'
        ]);

        DB::transaction(function () use ($request, $approval) {

            // approver aktif (yang sedang giliran)
            $currentApprover = $approval->approvers()
                ->where('status', 0)
                ->orderBy('approval_order')
                ->firstOrFail();

            // simpan thread inquiry
            $thread = SidiaApprovalThread::create([
                'approval_no' => $approval->approval_no,
                'approver_id' => auth()->id(),
                'type'        => 'inquiry',
                'step_order'  => $currentApprover->approval_order
            ]);

            // simpan message
            SidiaApprovalMessage::create([
                'thread_id'   => $thread->id,
                'approval_no' => $approval->approval_no,
                'sender_role' => 'APPROVER',
                'sender_id'   => auth()->id(),
                'message'     => $request->comment
            ]);

            // set approval status ke INQUIRY
            $approval->update([
                'status' => 5 // INQUIRY
            ]);
        });

        return back()->with('success', 'Inquiry berhasil dikirim ke requestor');
    }

    public function reply(Request $request, SidiaApprovalThread $thread)
    {
        $this->authorize('reply', $thread);

        $request->validate([
            'message'     => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240'
        ]);

        DB::transaction(function () use ($request, $thread) {

            $message = SidiaApprovalMessage::create([
                'thread_id'   => $thread->id,
                'approval_no' => $thread->approval_no,
                'sender_role' => 'CREATOR',
                'sender_id'   => auth()->id(),
                'message'     => $request->message
            ]);

            // attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    SidiaApprovalMessageAttachment::create([
                        'message_id' => $message->id,
                        'file_name'  => $file->getClientOriginalName(),
                        'file_path'  => $file->store('approval/chat')
                    ]);
                }
            }

            // balikin status approval ke SUBMITTED
            SidiaApproval::where('approval_no', $thread->approval_no)
                ->update(['status' => 1]);
        });

        return back()->with('success', 'Balasan berhasil dikirim');
    }

    public function approve(Request $request, SidiaApproval $approval)
    {
        $this->authorize('approve', $approval);

        $request->validate([
            'comment' => 'required|string'
        ]);

        DB::transaction(function () use ($request, $approval) {

            $currentApprover = $approval->approvers()
                ->where('status', 0)
                ->orderBy('approval_order')
                ->firstOrFail();

            // ✅ SIMPAN THREAD
            $thread = SidiaApprovalThread::create([
                'approval_no' => $approval->approval_no,
                'approver_id' => auth()->id(),
                'type'        => 'approve',
                'step_order'  => $currentApprover->approval_order,
            ]);

            // ✅ SIMPAN MESSAGE (PAKAI thread_id)
            SidiaApprovalMessage::create([
                'thread_id'   => $thread->id,   // 🔥 INI YANG HILANG
                'approval_no' => $approval->approval_no,
                'sender_role' => 'APPROVER',
                'sender_id'   => auth()->id(),
                'message'     => $request->comment
            ]);

            $currentApprover->update([
                'status' => 1,
                'approved_at' => now()
            ]);

            $nextApprover = $approval->approvers()
                ->where('status', 0)
                ->orderBy('approval_order')
                ->first();

            $approval->update([
                'status' => $nextApprover ? 1 : 3
            ]);
        });

        return back()->with('success', 'Approval berhasil diproses');
    }

    public function reject(Request $request, SidiaApproval $approval)
    {
        $this->authorize('reject', $approval);

        $request->validate([
            'comment' => 'required|string'
        ]);

        DB::transaction(function () use ($request, $approval) {

            $currentApprover = $approval->approvers()
                ->where('status', 0)
                ->orderBy('approval_order')
                ->firstOrFail();

            $thread = SidiaApprovalThread::create([
                'approval_no' => $approval->approval_no,
                'approver_id' => auth()->id(),
                'type'        => 'reject',
                'step_order'  => $currentApprover->approval_order
            ]);

            SidiaApprovalMessage::create([
                'thread_id'   => $thread->id,
                'approval_no' => $approval->approval_no,
                'sender_role' => 'APPROVER',
                'sender_id'   => auth()->id(),
                'message'     => $request->comment
            ]);

            $currentApprover->update([
                'status'      => 2, // rejected
                'approved_at' => now()
            ]);

            $approval->update([
                'status' => 4 // REJECTED
            ]);
        });

        return back()->with('success', 'Approval ditolak');
    }
}
