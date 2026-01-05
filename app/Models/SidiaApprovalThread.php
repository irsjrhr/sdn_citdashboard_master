<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaApprovalThread extends Model
{
    use HasFactory;
    protected $table = 'sidia_approval_threads';

    protected $fillable = [
        'approval_no',
        'approver_id',
        'status'
    ];

    /* ================= RELATIONS ================= */

    public function messages()
    {
        return $this->hasMany(SidiaApprovalMessage::class, 'thread_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function approval()
    {
        return $this->belongsTo(SidiaApproval::class, 'approval_no');
    }

    /* ================= HELPERS ================= */

    public function isOpen()
    {
        return $this->status == 1;
    }

    public function close()
    {
        $this->update(['status' => 2]);
    }
}
