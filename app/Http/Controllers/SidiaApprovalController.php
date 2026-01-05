<?php

namespace App\Http\Controllers;
use App\Models\SidiaApproval;
use App\Models\Cabang;
use App\Models\SidiaCategory;
use App\Models\User;
use App\Models\SidiaApprovalApprover;
use App\Models\SidiaPpiType;
use App\Notifications\SidiaApprovalAssignedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class SidiaApprovalController extends Controller
{
    public function index()
    {
        return view('sidia.approval.index', [
            'approvals'  => SidiaApproval::latest()->get(),
            'branches'   => Cabang::orderBy('nama_cabang', 'asc')->get(),
            'categories' => SidiaCategory::orderBy('category_name', 'asc')->get(),
            'ppiTypes'   => SidiaPpiType::orderBy('ppi_name')->get(),
            'users'      => User::select('id', 'name')
                            ->whereNotNull('name')
                            ->groupBy('id', 'name')
                            ->orderBy('name')
                            ->get(),
        ]);
    }

    public function create()
    {
        return view('sidia.approval.create', [
            'branches' => Cabang::all(),
            'categories' => SidiaCategory::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang'   => 'required',
            'category_code' => 'required',
            'subject'       => 'required',
            'description'   => 'required',
        ]);

        $approvers = collect(json_decode($request->approvers, true));

        // minimal 1 approver
        if ($approvers->count() === 0) {
            return response()->json([
                'message' => 'Minimal 1 approver harus dipilih'
            ], 422);
        }

        // tidak boleh user yang sama
        if ($approvers->pluck('user_id')->duplicates()->isNotEmpty()) {
            return response()->json([
                'message' => 'Terdapat approver yang duplikat'
            ], 422);
        }

        $isPpi = SidiaCategory::where('category_code', $request->category_code)
                ->whereRaw('LOWER(category_name) LIKE ?', ['%ppi%'])
                ->exists();

        if ($isPpi) {
            $request->validate([
                'ppi_code' => 'required',
                'amount'   => 'required|numeric|min:0',
            ]);
        }

        $approvalNo = 'APP-'.date('Ymd').'-'.strtoupper(Str::random(4));

        SidiaApproval::create([
            'approval_no'  => $approvalNo,
            'kode_cabang'  => $request->kode_cabang,
            'category_code'=> $request->category_code,
            'ppi_code'      => $isPpi ? $request->ppi_code : null,
            'amount'        => $isPpi ? $request->amount : 0,
            'subject'      => $request->subject,
            'description'  => $request->description,
            'created_by'   => auth()->user()->name,
            'modified_by'  => auth()->user()->name,
            'status'       => 1
        ]);

        foreach ($approvers as $index => $appr) {
            SidiaApprovalApprover::create([
                'approval_no'    => $approvalNo,
                'user_id'        => $appr['user_id'],
                'role'           => $appr['role'],
                'approval_order' => $index + 1,
                'status'         => 0
            ]);
        }

        // 🔔 kirim notifikasi ke approver pertama
        $firstApproverId = $approvers->first()['user_id'];
        $firstApprover   = User::find($firstApproverId);

        if ($firstApprover) {
            $firstApprover->notify(
                new SidiaApprovalAssignedNotification(
                    SidiaApproval::where('approval_no', $approvalNo)->first()
                )
            );
        }

        return response()->json([
            'success' => true,
            'approval_no'  => $approvalNo,
            'message' => 'Approval berhasil dibuat'
        ]);
    }

    public function show(SidiaApproval $approval)
    {
        $this->authorize('view', $approval);

        if (auth()->check()) {
            auth()->user()
                ->unreadNotifications
                ->where('data.approval_no', $approval->approval_no)
                ->each(fn ($notif) => $notif->markAsRead());
        }

        $approval->load([
            'approvers.user',
            'threads.messages',
            'creator'
        ]);

        $currentApprover = $approval->approvers()
            ->where('status', 0)
            ->orderBy('approval_order')
            ->first();

        $canApprove = $approval->status === SidiaApproval::STATUS_SUBMIT 
            && $currentApprover
            && $currentApprover->user_id === auth()->id();

        return view('sidia.approval.show', compact('approval', 'canApprove'));
    }
}
