<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Regions;

class Cabang extends Model
{
    use HasFactory;
    protected $table = "cabang";
    protected $primaryKey = "kode_cabang";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    /**
     * Relasi ke Region
     */
    public function region()
    {
        return $this->belongsTo(Regions::class, 'kode_region', 'kode_region');
    }
}
