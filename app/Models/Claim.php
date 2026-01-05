<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv-sfautility';
    protected $table = "BP_CustomerRequest";

    protected $fillable = [
        'ID',
        'ID_BP_PromoProgram',
        'BranchCode',
        'NoSKPP',
        'CustomerCode',
        'CustomerName',
        'NoBilling',
        'NilaiKwitansi',
        'TanggalKwitansi',
        'NilaiPotonganAR',
        'FakturPajak',
        'FotoDisplay',
        'TanggalPotongARCabang',
        'TanggalKirimDokumenkeHO',
        'VendorPengiriman',
        'NoResi',
        'NoTandaTerima',
        'Keterangan',
        'TanggalClearingAR',
        'TanggalTerimaDokumen',
        'NoBiaya',
        'TanggalBiaya',
        'NoDM',
        'TanggalDM',
        'NilaiDM',
        'TanggalBayarDM',
        'StatusClaim',
        'NotedHO',
        'CreatedBy',
        'CreatedDate',
        'UpdatedBy',
        'UpdatedDate',
    ];

    public $timestamps = false;
}
