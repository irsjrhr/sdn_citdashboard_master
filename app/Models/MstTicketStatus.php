<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstTicketStatus extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'mst_ticket_status';
    
    protected $fillable = [
        'raw_status', 
        'status_category'
    ];
}
