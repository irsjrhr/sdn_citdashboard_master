<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaApprovalMessage extends Model
{
    use HasFactory;
    protected $table = 'sidia_approval_messages';

    protected $fillable = [
        'thread_id',
        'approval_no',
        'sender_role',
        'sender_id',
        'message'
    ];

    /* ================= RELATIONS ================= */

    public function thread()
    {
        return $this->belongsTo(SidiaApprovalThread::class, 'thread_id');
    }

    public function attachments()
    {
        return $this->hasMany(SidiaApprovalMessageAttachment::class, 'message_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /* ================= HELPERS ================= */

    public function isFromCreator()
    {
        return $this->sender_role === 'CREATOR';
    }

    public function isFromApprover()
    {
        return $this->sender_role === 'APPROVER';
    }
}
