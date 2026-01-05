<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidiaApproval extends Model
{
    use HasFactory;
    protected $table = 'sidia_approval';
    protected $primaryKey = 'approval_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'approval_no',
        'category_code',
        'kode_cabang',
        'subject',
        'description',
        'ppi_code',
        'amount',
        'status',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'status' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'approval_no';
    }

    /* ================= RELATIONS ================= */

    public function approvers()
    {
        return $this->hasMany(SidiaApprovalApprover::class, 'approval_no');
    }

    public function threads()
    {
        return $this->hasMany(SidiaApprovalThread::class, 'approval_no');
    }

    public function category()
    {
        return $this->belongsTo(SidiaCategory::class, 'category_code');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'name');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang');
    }

    public function ppiType()
    {
        return $this->belongsTo(SidiaPpiType::class, 'ppi_code');
    }

    const STATUS_SUBMIT   = 1;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_INQUIRY  = 5;

    /* ================= HELPERS ================= */

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isInquiry()
    {
        return $this->status === self::STATUS_INQUIRY;
    }
}
