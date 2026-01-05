<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalesReturnService;
use App\Services\UserMetricFilterService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalesDashboardController extends Controller
{
    protected $salesReturnService;

    private $sdndwh;
    private $userBranchCode;
    private $userTitle;
    private $userRegion;

    public function __construct(SalesReturnService $salesReturnService)
    {
        // Service Injection
        $this->salesReturnService = $salesReturnService;

        // DB Connection
        $this->sdndwh = DB::connection('sqlsrv-sdndwh');

        // Regional user Access Metric
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $this->userBranchCode = $user->karyawan->kode_cabang;
            $this->userTitle      = $user->karyawan->jabatan->nama_jabatan;
            $this->userRegion     = $user->karyawan->cabang->kode_region;

            return $next($request);
        });
    }

    public function salesReturnIndex(Request $request)
    {
        $scope = UserMetricFilterService::getUserAccessScrope($this->userTitle, $this->userBranchCode);

        // === 1️⃣ DATE FILTERS (same logic as index) ===
        $startDate = $request->input('startDate')
            ? Carbon::parse($request->input('startDate'))->startOfDay()
            : Carbon::now()->startOfYear()->startOfDay();

        $endDate = $request->input('endDate')
            ? Carbon::parse($request->input('endDate'))->endOfDay()
            : Carbon::now()->endOfYear()->endOfDay();

        /* -------------------- Filters -------------------- */
         // 🔹 Extract all filters from request safely
        $sortBy  = $request->input('sortBy')  ?? 'TotalReturns';
        $orderBy = $request->input('orderBy') ?? 'DESC';
        $filters = [
            'region'              => $request->input('region'),
            'branch'              => $request->input('branch'),
            'distributionChannel' => $request->input('distributionChannel'),
            'businessType'        => $request->input('businessType'),
            'principalCode'       => $request->input('principalCode'),
            'startDate'           => $startDate,
            'endDate'             => $endDate,
            'sortBy'              => $sortBy,
            'orderBy'             => $orderBy,
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

        $distributionChannels = $this->sdndwh->table('Zh_Mst_Customer')
            ->select('DistributionChannel')
            ->distinct()
            ->orderBy('DistributionChannel', 'ASC')
            ->get();
        $distributionChannels->push((object) ['DistributionChannel' => 'LMT']);

        $businessTypes = collect([
            (object) ['BusinessType' => 'MIX'],
            (object) ['BusinessType' => 'ULI'],
            (object) ['BusinessType' => 'JTI'],
        ]);
        
        // $customerGroup = $this->sdndwh->table('Fact_Sales_Business')
        //     ->select('Customer Group AS CustomerGroup')
        //     ->distinct()
        //     ->orderBy('CustomerGroup', 'ASC')
        //     ->get();

        $principals = $this->sdndwh->table('mst_principal')
            ->select('principal_code AS PrincipalCode', 'principal_name AS PrincipalName')
            ->distinct()
            ->orderBy('PrincipalName', 'ASC')
            ->get();

        $lastUpdatedDate = $this->sdndwh->selectOne("
            SELECT
                MAX(
                    CONVERT(DATETIME,
                        STUFF(STUFF(CAST(h.run_date AS CHAR(8)), 5, 0, '-'), 8, 0, '-') + ' ' +
                        STUFF(
                            STUFF(RIGHT('000000' + CAST(h.run_time AS VARCHAR(6)), 6), 3, 0, ':'),
                            6, 0, ':'
                        )
                    )
                ) AS LastSuccessRun
            FROM msdb.dbo.sysjobs j
            JOIN msdb.dbo.sysjobhistory h ON j.job_id = h.job_id
            WHERE h.run_status = 1      -- success
            AND h.step_id = 0         -- job outcome
            AND j.name = ?
        ", ['JOB_Load_Fact_Sales_Business_Daily (YTD)'])->LastSuccessRun;

        // dd($filters);

        // 🔹 Call service
        $highLevelOverview = $this->salesReturnService->getHighLevelOverview($filters);
        // dd($highLevelOverview);

        return view('monitoring-dashboard.finance.sales-return.sales_return_index', compact(
            'highLevelOverview',
            'lastUpdatedDate',
            'startDate',
            'endDate',
            'regions',
            'branches',
            'distributionChannels',
            'businessTypes',
            // 'customerGroup',
            'principals',
            'sortBy',
            'orderBy',
            'scope'
        ));
    }

    public function salesReturnHeaderAnalysis(Request $request, $dimension)
    {
        $dimensionMap = [
            'branch' => [
                'title' => 'Branch Analysis',
                'sp' => 'Branch',
                'dimension' => 'branch',
                'label' => 'Branch'
            ],
            'region' => [
                'title' => 'Region Analysis',
                'sp' => 'Region',
                'dimension' => 'region',
                'label' => 'Region'
            ],
            'distributionChannel' => [
                'title' => 'Distribution Channel Analysis',
                'sp' => 'distributionChannel',
                'dimension' => 'distributionChannel',
                'label' => 'Distribution Channel'
            ],
            'businessType' => [
                'title' => 'Business Type Analysis',
                'sp' => 'BusinessType',
                'dimension' => 'businessType',
                'label' => 'Business Type'
            ],
            'principal' => [
                'title' => 'Principal Analysis',
                'sp' => 'Principal',
                'dimension' => 'principal',
                'label' => 'Principal'
            ],
            'store' => [
                'title' => 'Store Analysis',
                'sp' => 'Store',
                'dimension' => 'store',
                'label' => 'Store'
            ],
            'salesmen' => [
                'title' => 'Salesman Analysis',
                'sp' => 'Salesman',
                'dimension' => 'salesmen',
                'label' => 'Salesman'
            ],
            'sku' => [
                'title' => 'SKU Analysis',
                'sp' => 'SKU',
                'dimension' => 'sku',
                'label' => 'SKU'
            ],
        ];

        if (!isset($dimensionMap[$dimension])) {
            abort(404);
        }

        $config = $dimensionMap[$dimension];

        $filters = [
            'businessType'        => $request->input('businessType'),
            'region'              => $request->input('region'),
            'branch'              => $request->input('branch'),
            'distributionChannel' => $request->input('distributionChannel'),
            'principalCode'       => $request->input('principalCode'),
            'startDate'           => $request->input('startDate'),
            'endDate'             => $request->input('endDate'),
            'sortBy'              => $request->input('sortBy') ?? 'TotalReturns',
            'orderBy'             => $request->input('orderBy') ?? 'DESC',
            'page'                => (int) $request->input('page', 1),
            'pageSize'            => 100,
        ];

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );

        $config = $dimensionMap[$dimension];

        $rawData   = $this->salesReturnService->getHeaderAnalysis($config['sp'], $filters);
        $rows      = $rawData['rows']      ?? [];
        $summaryRow= $rawData['summary']   ?? null;
        $top10     = $rawData['top10']     ?? [];
        $totalRows = $rawData['totalRows'] ?? 0;

        $page     = $filters['page'];
        $pageSize = $filters['pageSize'];

        $paginatedHeaderData = new LengthAwarePaginator(
            $rows,
            $totalRows,
            $pageSize,
            $page,
            [
                'path'     => url()->current(),
                'pageName' => 'page',
                'query'    => $request->query(),
            ]
        );

        // summary (same as before)
        $rawTotalSales   = (float) ($summaryRow['TotalSales'] ?? 0);
        $rawTotalReturns = (float) ($summaryRow['TotalReturns'] ?? 0);
        $rawQtySales     = (float) ($summaryRow['QtyTotalSales'] ?? 0);
        $rawQtyReturns   = (float) ($summaryRow['QtyTotalReturns'] ?? 0);

        $summary = [
            'totalSales'      => formatAbbreviatedNumber($rawTotalSales),
            'totalReturns'    => formatAbbreviatedNumber($rawTotalReturns),
            'returnRate'      => $rawTotalSales > 0
                ? ($rawTotalReturns / $rawTotalSales) * 100
                : 0,
            'qtyTotalSales'   => $rawQtySales,
            'qtyTotalReturns' => $rawQtyReturns,
            'qtyReturnRate'   => $rawQtySales > 0
                ? ($rawQtyReturns / $rawQtySales) * 100
                : 0,
        ];

        // === TOP 10 FROM DB (NOT FROM CURRENT PAGE) ===
        $top10Collection = collect($top10);

        $chart = [
            'labels'  => $top10Collection->pluck('DimensionName'),
            'sales'   => $top10Collection->pluck('TotalSales'),
            'returns' => $top10Collection->pluck('TotalReturns'),
        ];

        $dimensionConfig = [
            'title'       => $config['title'],
            'routeBase'   => 'sales.return.dashboard.header.index',
            'tableRoute'  => 'sales.return.dashboard.detail.index',
            'dimension'   => $dimension,
            'tableColumns'=> [
                'label'   => $config['label'],
                'sales'   => 'Sales (Rp.)',
                'returns' => 'Returns (Rp.)',
                'rate'    => 'Rate (%)'
            ]
        ];

        return view('monitoring-dashboard.finance.sales-return.dynamic_dimension_header', [
            'dimensionConfig' => $dimensionConfig,
            'headerData'      => $paginatedHeaderData,
            'summary'         => $summary,
            'chart'           => $chart,
            'filters'         => $filters,
        ]);
    }

    public function salesReturnDetailAnalysis(Request $request, $dimension)
    {
        $detailDimensionMap = [
            // BRANCH
            'branch' => [
                'dimension' => 'branch',
                'title' => 'Branch Detail',
                'label' => 'Branch',
                'codeField' => 'BranchCode',
                'nameField' => 'BranchName',
                'childDimensions' => ['stores', 'salesmen', 'skus']
            ],

            // SALESMAN
            'salesmen' => [
                'dimension' => 'salesmen',
                'title' => 'Salesman Detail',
                'label' => 'Salesman',
                'codeField' => 'SalesmanCode',
                'nameField' => 'SalesmanName',
                'childDimensions' => ['branches', 'stores', 'skus'] // ❗ salesmen should NOT show salesmen again
            ],

            // SKU
            'sku' => [
                'dimension' => 'sku',
                'title' => 'SKU Detail',
                'label' => 'SKU',
                'codeField' => 'SKUCode',
                'nameField' => 'SKUName',
                'childDimensions' => ['branches', 'stores', 'salesmen']
            ],

            // PRINCIPAL
            'principal' => [
                'dimension' => 'principal',
                'title' => 'Principal Detail',
                'label' => 'Principal',
                'codeField' => 'PrincipalCode', // returned by your SP
                'nameField' => 'PrincipalName',
                'childDimensions' => ['branches', 'salesmen', 'stores', 'skus']
            ],

            // REGION
            'region' => [
                'dimension' => 'region',
                'title' => 'Region Detail',
                'label' => 'Region',
                'codeField' => 'RegionCode',
                'nameField' => 'Region',
                'childDimensions' => ['branches', 'salesmen', 'stores', 'skus']
            ],

            // DISTRIBUTION CHANNEL
            'distributionChannel' => [
                'dimension' => 'distributionChannel',
                'title' => 'Distribution Channel Detail',
                'label' => 'Distribution Channel',
                'codeField' => 'DistributionChannel',
                'nameField' => 'DistributionChannel',
                'childDimensions' => ['branches', 'salesmen', 'stores', 'skus']
            ],

            // BUSINESS TYPE
            'businessType' => [
                'dimension' => 'businessType',
                'title' => 'Business Type Detail',
                'label' => 'Business Type',
                'codeField' => 'BusinessType',
                'nameField' => 'BusinessType',
                'childDimensions' => ['branches', 'salesmen', 'stores', 'skus']
            ],

            // STORE
            'store' => [
                'dimension' => 'store',
                'title' => 'Store Detail',
                'label' => 'Store',
                'codeField' => 'StoreCode',
                'nameField' => 'StoreName',
                'childDimensions' => ['branches', 'salesmen', 'skus'] // ✔ store should NOT show stores
            ],
        ];

        if (!isset($detailDimensionMap[$dimension])) abort(404);
        $config = $detailDimensionMap[$dimension];

        // Pagination per table
        $filters = [
            'dimension' => $dimension,
            'code'      => $request->input('code'),
            'startDate' => $request->startDate,
            'endDate'   => $request->endDate,
            'distributionChannel' => $request->distributionChannel,
            'principalCode'       => $request->principalCode,
            'businessType'        => $request->businessType,
            'region'              => $request->region,
            'branch'              => $request->branch,
            'sortBy'              => $request->input('sortBy', 'TotalReturns'),
            'orderBy'             => $request->input('orderBy', 'DESC'),

            // NEW
            'branchesPage'  => $request->input('branches_page', 1),
            'storesPage'    => $request->input('stores_page', 1),
            'salesmenPage'  => $request->input('salesmen_page', 1),
            'skusPage'      => $request->input('skus_page', 1),
            'pageSize'      => 25,
        ];

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );

        $raw = $this->salesReturnService->getDetailAnalysis($filters);

        // Build paginated objects
        $detailData = [
            'overview' => $raw['overview'],
            'trend'    => $raw['trend'],

            'branches' => new LengthAwarePaginator(
                $raw['branches_rows'],
                $raw['branches_total'],
                $filters['pageSize'],
                $filters['branchesPage'],
                ['path' => url()->current(), 'pageName' => 'branches_page', 'query' => $request->query()]
            ),

            'stores' => new LengthAwarePaginator(
                $raw['stores_rows'],
                $raw['stores_total'],
                $filters['pageSize'],
                $filters['storesPage'],
                ['path' => url()->current(), 'pageName' => 'stores_page', 'query' => $request->query()]
            ),

            'salesmen' => new LengthAwarePaginator(
                $raw['salesmen_rows'],
                $raw['salesmen_total'],
                $filters['pageSize'],
                $filters['salesmenPage'],
                ['path' => url()->current(), 'pageName' => 'salesmen_page', 'query' => $request->query()]
            ),

            'skus' => new LengthAwarePaginator(
                $raw['skus_rows'],
                $raw['skus_total'],
                $filters['pageSize'],
                $filters['skusPage'],
                ['path' => url()->current(), 'pageName' => 'skus_page', 'query' => $request->query()]
            ),
        ];

        // dd($detailData);
        // dd($filters);

        return view('monitoring-dashboard.finance.sales-return.dynamic_dimension_detail', compact(
            'config',
            'detailData',
            'filters'
        ));
    }
}