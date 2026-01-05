<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaApprovalApprover extends Model
{
    use HasFactory;
    protected $table = 'sidia_approval_approvers';

    protected $fillable = [
        'approval_no',
        'user_id',
        'role',
        'approval_order',
        'status',
        'approved_at',
        'note'
    ];

    /* ================= RELATIONS ================= */

    public function approval()
    {
        return $this->belongsTo(SidiaApproval::class, 'approval_no');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ================= SCOPES ================= */

    public function scopeCurrent($query, $approvalNo, $userId)
    {
        return $query
            ->where('approval_no', $approvalNo)
            ->where('user_id', $userId)
            ->where('status', 0);
    }
}
