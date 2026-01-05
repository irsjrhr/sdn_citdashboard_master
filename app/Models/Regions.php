<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    use HasFactory;

    protected $table = 'regions';
    protected $primaryKey = 'kode_region';
    public $incrementing = false;          // karena primary key bukan integer auto increment
    protected $keyType = 'string';         // PK berupa string

    protected $fillable = [
        'kode_region',
        'nama_region',
    ];

    public $timestamps = true; // jika ada created_at & updated_at
}
