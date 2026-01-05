<?php

namespace App\Http\Controllers;

use App\Services\UserMetricFilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use PDO;

class TMSDashboardController extends Controller
{
    private $sdndwh;
    private $zhinterface;
    private $userBranchCode;
    private $userTitle;
    private $userRegion;

    public function __construct(){
        // DB Connection
        $this->sdndwh = DB::connection('sqlsrv-sdndwh');
        $this->zhinterface = DB::connection('sqlsrv-zhinterface');

        // Regional user Access Metric
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $this->userBranchCode = $user->karyawan->kode_cabang;
            $this->userTitle      = $user->karyawan->jabatan->nama_jabatan;
            $this->userRegion     = $user->karyawan->cabang->kode_region;

            return $next($request);
        });
    }

    public function podReturnIndex(Request $request)
    {
        $scope = UserMetricFilterService::getUserAccessScrope($this->userTitle, $this->userBranchCode);
        $pdo = $this->sdndwh->getPdo();
        
        /* -------------------- Filters -------------------- */
        $startDate = Carbon::parse(request()->input('startDate', null)) ?? Carbon::now()->startOfMonth();
        $endDate = Carbon::parse(request()->input('endDate', null)) ?? Carbon::now()->endOfMonth();
        $page = request()->input('page') ?? 1;
        $filters = [
            'region'              => $request->input('region'),
            'branch'              => $request->input('branch'),
            'startDate'           => $request->input('startDate'),
            'endDate'             => $request->input('endDate'),
        ];
        // dd($filters)

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        // dd($filters);

        $locationFilters = UserMetricFilterService::getFilters(
            $this->sdndwh,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        $regions   = $locationFilters['regions'];
        $branches  = $locationFilters['branches'];
        $locations = $locationFilters['locations'];

        $params = [
            'startDate'           => $filters['startDate'] ?? null,
            'endDate'             => $filters['endDate'] ?? null,
        ];

        $lastUpdatedDate = $this->zhinterface->table('ZH_Pod_Epod')
            ->max('sentdatedocument');

        $summarySql = "
            EXEC sp_PortalSDN_GetSummaryPODDashboard
                @startDate = :startDate,
                @endDate   = :endDate
        ";
        $resultSet = execStoredProcedure($pdo, $summarySql, $params) ?? [];
        $summaryData = $resultSet[0][0];
        $channelData = $resultSet[1];
        $reasonData = $resultSet[2];

        // dd($reasonData);
        $branchData = new LengthAwarePaginator(
            $resultSet[3], // branch data
            $resultSet[4][0]['total_rows'] ?? 0, // total rows
            25, // per page
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        // dd($reasonData[0]['Return Reason']);
        
        return view('monitoring-dashboard.tms.pod_return_index', compact(
            'summaryData',
            'channelData',
            'reasonData',
            'branchData',
            'startDate',
            'endDate',
            'branches',
            'regions',
            'lastUpdatedDate'
        ));
    }

    public function podReturnReasonDetailIndex(Request $request)
    {
        $scope = UserMetricFilterService::getUserAccessScrope($this->userTitle, $this->userBranchCode);

        $pdo = $this->sdndwh->getPdo();

        // Filters
        $startDate = request()->input('startDate', null) ?? Carbon::now()->startOfMonth();
        $endDate = request()->input('endDate', null) ?? Carbon::now()->endOfMonth();
        $filters = [
            'region'              => $request->input('region'),
            'branch'              => $request->input('branch'),
            'startDate'           => $request->input('startDate'),
            'endDate'             => $request->input('endDate'),
        ];

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        // dd($filters);

        $locationFilters = UserMetricFilterService::getFilters(
            $this->sdndwh,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        $regions   = $locationFilters['regions'];
        $branches  = $locationFilters['branches'];
        $locations = $locationFilters['locations'];

        $sql = "
            EXEC sp_PortalSDN_GetPODReturnReasonDetails
                @startDate = :startDate,
                @endDate   = :endDate
        ";
        $params = [
            'startDate'           => $filters['startDate'] ?? null,
            'endDate'             => $filters['endDate'] ?? null,
        ];

        $datasets = execStoredProcedure($pdo, $sql, [
            ...$params,
        ]) ?? [];

        $allRejection = $datasets[0] ?? [];
        $rejectionByPrincipal = $datasets[1] ?? [];
        $top5ReasonBySKU = $datasets[2] ?? [];
        $top5ReasonBySalesman = $datasets[3] ?? [];
        $top5ReasonByDriver = $datasets[4] ?? [];
        $top5ReasonByDistChannelByBranch = $datasets[5] ?? [];

        $lastUpdatedDate = $this->zhinterface->table('ZH_Pod_Epod')
            ->max('sentdatedocument');

        return view('monitoring-dashboard.tms.pod_return_reason_detail_index', compact(
            'lastUpdatedDate',
            'startDate',
            'endDate',
            'regions',
            'branches',
            'allRejection',
            'rejectionByPrincipal',
            'top5ReasonBySKU',
            'top5ReasonBySalesman',
            'top5ReasonByDriver',
            'top5ReasonByDistChannelByBranch'
        ));
    }
}