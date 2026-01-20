<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKaryawan extends Model
{
    use HasFactory;
    protected $table = 'users_karyawan';
    protected $guarded = [];
}
