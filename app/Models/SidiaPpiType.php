<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaPpiType extends Model
{
    use HasFactory;
    protected $table = 'sidia_ppitype';
    protected $primaryKey = 'ppi_code';

    public $incrementing = false;
    protected $keyType = 'string';

    // table ini PUNYA created_at & updated_at,
    // jadi timestamps tetap true (default)
    public $timestamps = true;

    protected $fillable = [
        'ppi_code',
        'ppi_name',
        'created_by',
        'updated_by',
    ];
}
