<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    public function index()
    {
        // --- Audit & Compliance ---
        $statusClaimDistribution = Claim::select('StatusClaim', DB::raw('COUNT(*) as total'))
            ->groupBy('StatusClaim')
            ->get();

        $claimsWithNotes = Claim::whereNotNull('NotedHO')
            ->whereRaw("LTRIM(RTRIM(NotedHO)) <> ''")
            ->count();

        $avgUpdateDelay = Claim::whereNotNull('CreatedDate')
            ->whereNotNull('UpdatedDate')
            ->select(DB::raw('AVG(DATEDIFF(DAY, CreatedDate, UpdatedDate)) as avg_delay'))
            ->value('avg_delay');


        // --- Process Flow & Timeliness ---
        $timeliness = Claim::select(
            'ID',
            DB::raw('DATEDIFF(DAY, TanggalKwitansi, TanggalKirimDokumenkeHO) AS days_kwitansi_to_kirim'),
            DB::raw('DATEDIFF(DAY, TanggalKirimDokumenkeHO, TanggalTerimaDokumen) AS days_kirim_to_terima'),
            DB::raw('DATEDIFF(DAY, TanggalTerimaDokumen, TanggalClearingAR) AS days_terima_to_clearing'),
            DB::raw('DATEDIFF(DAY, TanggalClearingAR, TanggalDM) AS days_clearing_to_dm'),
            DB::raw('DATEDIFF(DAY, TanggalDM, TanggalBayarDM) AS days_dm_to_bayar'),
            DB::raw('DATEDIFF(DAY, TanggalKwitansi, TanggalBayarDM) AS total_lifecycle')
        )->get();


        // --- Financial Insights ---
        $financial = Claim::selectRaw("
            SUM(NilaiKwitansi) AS total_claimed,
            SUM(NilaiPotonganAR) AS total_potongan_ar,
            SUM(NilaiKwitansi - ISNULL(NilaiPotonganAR, 0)) AS net_claim_value,
            SUM(NilaiDM) AS total_dm_paid,
            CASE 
                WHEN SUM(NilaiKwitansi) > 0 THEN 
                    ROUND(SUM(NilaiDM) * 100.0 / SUM(NilaiKwitansi), 2)
                ELSE 0 
            END AS claim_to_payment_ratio
        ")
        ->where('NilaiKwitansi', '!=', '')
        ->where('NilaiPotonganAR', '!=', '')
        ->where('NilaiDM', '!=', '')
        ->first();


        // --- Branch & Program Performance ---
        $claimsPerBranch = Claim::select('BranchCode', DB::raw('COUNT(*) as total_claims'))
            ->groupBy('BranchCode')
            ->orderByDesc('total_claims')
            ->get();

        $avgClaimPerBranch = Claim::select('BranchCode', DB::raw('AVG(NilaiKwitansi) as avg_claim_value'))
            ->groupBy('BranchCode')
            ->get();

        $avgProcessingPerBranch = Claim::whereNotNull('TanggalBayarDM')
            ->select('BranchCode', DB::raw('AVG(DATEDIFF(DAY, TanggalKwitansi, TanggalBayarDM)) as avg_processing_days'))
            ->groupBy('BranchCode')
            ->get();

        $claimsPerPromo = Claim::select('ID_BP_PromoProgram', DB::raw('COUNT(*) as total_claims'))
            ->groupBy('ID_BP_PromoProgram')
            ->orderByDesc('total_claims')
            ->get();

        $branchCompliance = Claim::whereNotNull('TanggalKwitansi')
            ->whereNotNull('TanggalKirimDokumenkeHO')
            ->select(
                'BranchCode',
                DB::raw('COUNT(*) AS total_claims'),
                DB::raw('SUM(CASE WHEN DATEDIFF(DAY, TanggalKwitansi, TanggalKirimDokumenkeHO) <= 7 THEN 1 ELSE 0 END) AS compliant_claims'),
                DB::raw('ROUND(SUM(CASE WHEN DATEDIFF(DAY, TanggalKwitansi, TanggalKirimDokumenkeHO) <= 7 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) AS compliance_rate')
            )
            ->groupBy('BranchCode')
            ->get();


        // --- AR Data Checks ---
        $kwitansiGtDm = Claim::whereRaw('NilaiKwitansi > ISNULL(NilaiDM, 0)')->count();
        $clearingNullDmExists = Claim::whereNull('TanggalClearingAR')->whereNotNull('NoDM')->count();
        $dmIssuedNotPaid = Claim::whereNotNull('NoDM')->whereNull('TanggalBayarDM')->count();
        $branchOffsetNotCleared = Claim::whereNotNull('TanggalPotongARCabang')->whereNull('TanggalClearingAR')->count();


        // --- Logistic Insights ---
        $avgDeliveryDays = Claim::whereNotNull('TanggalKirimDokumenkeHO')
            ->whereNotNull('TanggalTerimaDokumen')
            ->select(DB::raw('AVG(DATEDIFF(DAY, TanggalKirimDokumenkeHO, TanggalTerimaDokumen)) as avg_delivery_days'))
            ->value('avg_delivery_days');

        $vendorPerformance = Claim::select(
            'VendorPengiriman',
            DB::raw('COUNT(*) as total_shipments'),
            DB::raw('AVG(DATEDIFF(DAY, TanggalKirimDokumenkeHO, TanggalTerimaDokumen)) as avg_delivery_days'),
            DB::raw('SUM(CASE WHEN TanggalTerimaDokumen IS NULL THEN 1 ELSE 0 END) as missing_deliveries')
        )
        ->groupBy('VendorPengiriman')
        ->orderBy('avg_delivery_days')
        ->get();

        $missingResi = Claim::whereNull('NoResi')
            ->orWhereRaw("LTRIM(RTRIM(NoResi)) = ''")
            ->count();

        $receivedNoClaim = Claim::whereNotNull('TanggalTerimaDokumen')
            ->whereNull('TanggalKwitansi')
            ->count();


        // --- Fraud & Anomaly Detection ---
        $promoAvg = Claim::select('ID_BP_PromoProgram', DB::raw('AVG(NilaiKwitansi) as avg_per_program'))
            ->groupBy('ID_BP_PromoProgram');
        $aboveAvgClaims = Claim::joinSub($promoAvg, 'p', function ($join) {
                $join->on('BP_CustomerRequest.ID_BP_PromoProgram', '=', 'p.ID_BP_PromoProgram');
            })
            ->whereRaw('BP_CustomerRequest.NilaiKwitansi > p.avg_per_program * 1.2')
            ->count();

        $submittedLate = Claim::whereRaw('DATEDIFF(MONTH, TanggalKwitansi, CreatedDate) > 1')->count();
        $paymentBeforeDM = Claim::whereRaw('TanggalBayarDM < TanggalDM')->count();
        $duplicateInvoices = Claim::select(DB::raw('COALESCE(NoKwitansi, NoBilling) as duplicate_value'), DB::raw('COUNT(*) as duplicate_count'))
            ->whereNotNull(DB::raw('COALESCE(NoKwitansi, NoBilling)'))
            ->groupBy(DB::raw('COALESCE(NoKwitansi, NoBilling)'))
            ->havingRaw('COUNT(*) > 1')
            ->count();
        $missingFotoApproved = Claim::where(function ($q) {
                $q->whereNull('FotoDisplay')->orWhereRaw("LTRIM(RTRIM(FotoDisplay)) = ''");
            })
            ->whereIn('StatusClaim', ['Approved', 'Completed'])
            ->count();

        // --- Collect all into chart data ---
        $chartData = [
            'auditCompliance' => [
                'statusDistribution' => $statusClaimDistribution,
                'claimsWithNotes'    => $claimsWithNotes,
                'avgUpdateDelay'     => round($avgUpdateDelay, 2),
            ],
            'timeliness' => $timeliness,
            'financial' => [
                'totalClaimed' => $financial->total_claimed,
                'totalPotonganAR' => $financial->total_potongan_ar,
                'netClaimValue' => $financial->net_claim_value,
                'totalDMPaid' => $financial->total_dm_paid,
                'claimToPaymentRatio' => $financial->claim_to_payment_ratio,
            ],
            'branchPerformance' => [
                'claimsPerBranch' => $claimsPerBranch,
                'avgClaimPerBranch' => $avgClaimPerBranch,
                'avgProcessingPerBranch' => $avgProcessingPerBranch,
                'claimsPerPromo' => $claimsPerPromo,
                'branchCompliance' => $branchCompliance,
            ],
            'arDataChecks' => [
                'kwitansiGtDm' => $kwitansiGtDm,
                'clearingNullDmExists' => $clearingNullDmExists,
                'dmIssuedNotPaid' => $dmIssuedNotPaid,
                'branchOffsetNotCleared' => $branchOffsetNotCleared,
            ],
            'logistics' => [
                'avgDeliveryDays' => round($avgDeliveryDays, 2),
                'vendorPerformance' => $vendorPerformance,
                'missingResi' => $missingResi,
                'receivedNoClaim' => $receivedNoClaim,
            ],
            'fraud' => [
                'aboveAvgClaims' => $aboveAvgClaims,
                'submittedLate' => $submittedLate,
                'paymentBeforeDM' => $paymentBeforeDM,
                'duplicateInvoices' => $duplicateInvoices,
                'missingFotoApproved' => $missingFotoApproved,
            ]
        ];

        return view('claim.index', compact('chartData'));
    }

}
