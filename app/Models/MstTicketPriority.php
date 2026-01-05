<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstTicketPriority extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'mst_ticket_priority';
    
    protected $fillable = [
        'level',
        'raw_priority', 
        'priority_category'
    ];
}
