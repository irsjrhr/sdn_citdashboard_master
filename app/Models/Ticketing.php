<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticketing extends Model
{
    
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = "tickets";

    protected $fillable = [
        'project',
        'epic',
        'code',
        'ref_code',
        'name',
        'content',
        'product',
        'environment',
        'owner',
        'pic_dev',
        'pic_qa',
        'pic_sit',
        'pic_uat',
        'status',
        'type',
        'priority',
        'created_at',
        'waiting_user_feedback_at',
        'in_progress_at',
        'resolved_at',
        'closed_at',
        'related_tickets',
        'start_time',
        'end_time',
        'related_user',
        'department',
        'location',
        'root_cause',
        'solution',

        // Local additions
        'revised_end_time',
        'end_time_revised_counter',
    ];

    public $timestamps = false;

    public function statusMaster()
    {
        return $this->belongsTo(MstTicketStatus::class, 'status', 'raw_status');
    }

    public function priorityMaster()
    {
        return $this->belongsTo(MstTicketPriority::class, 'priority', 'raw_priority');
    }
}
