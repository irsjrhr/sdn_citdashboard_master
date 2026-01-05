<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaApprovalMessageAttachment extends Model
{
    use HasFactory;
    protected $table = 'sidia_approval_message_attachments';

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path'
    ];

    public function message()
    {
        return $this->belongsTo(SidiaApprovalMessage::class, 'message_id');
    }
}
