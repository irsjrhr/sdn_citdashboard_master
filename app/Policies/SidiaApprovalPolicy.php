<?php

namespace App\Policies;

use App\Models\SidiaApproval;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class SidiaApprovalPolicy
{
    public function view(User $user, SidiaApproval $approval): bool
    {
        Log::info('POLICY CHECK', [
            'user_id' => $user->id,
            'approval_no' => $approval->approval_no,
            'created_by' => $approval->created_by,
            'approvers' => $approval->approvers()->pluck('user_id')->toArray(),
        ]);
        // pembuat approval boleh lihat
        if ($approval->created_by === $user->name) {
            return true;
        }

        // approver boleh lihat
        return $approval->approvers()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function approve(User $user, SidiaApproval $approval): bool
    {
        return $approval->approvers()
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->exists();
    }

    public function reject(User $user, SidiaApproval $approval): bool
    {
        return $this->approve($user, $approval);
    }

    public function inquiry(User $user, SidiaApproval $approval): bool
    {
        return $this->approve($user, $approval);
    }
}
