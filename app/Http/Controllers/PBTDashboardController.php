<?php

namespace App\Http\Controllers;

use App\Services\UserMetricFilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class PBTDashboardController extends Controller
{
    private $sdndwh;
    private $reportSDN;
    private $userBranchCode;
    private $userTitle;
    private $userRegion;

    public function __construct()
    {
        // DB Connection
        $this->sdndwh = DB::connection('sqlsrv-sdndwh');
        $this->reportSDN = DB::connection('sqlsrv-reportsdn');

        // Regional user Access Metric
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $this->userBranchCode = $user->karyawan->kode_cabang;
            $this->userTitle      = $user->karyawan->jabatan->nama_jabatan;
            $this->userRegion     = $user->karyawan->cabang->kode_region;

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        /* -------------------- Filters -------------------- */
        $filters = [
            'year'     => $request->input('year', now()->year),
            'month'    => $request->input('month'),
            'branch'   => $request->input('branch'),
            'location' => $request->input('location'),
            'viewBy'   => $request->input('viewBy', 'idr'),
        ];

        // Extract region code safely
        $regionParts = explode(' ', $request->input('region', ''));
        $filters['region'] = $regionParts[1] ?? null;

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        // dd($filters);

        /* -------------------- Dropdown Data -------------------- */
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $selectedMonth = $filters['month'] ?? null;

        $locationFilters = UserMetricFilterService::getFilters(
            $this->sdndwh,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );

        $regions   = $locationFilters['regions'];
        $branches  = $locationFilters['branches'];
        $locations = $locationFilters['locations'];

        /* -------------------- Report Data -------------------- */
        $pdo = $this->reportSDN->getPdo();

        $groupedByLocationStmt = $pdo->prepare("
            EXEC sp_Finance_Report_update
                @year     = :year,
                @month    = :month,
                @region   = :region,
                @location = :location,
                @branch   = :branch,
                @groupBy  = 'location',
                @view     = :viewBy
        ");

        $stmt = $pdo->prepare("
            EXEC sp_Finance_Report_update
                @year     = :year,
                @month    = :month,
                @region   = :region,
                @location = :location,
                @branch   = :branch,
                @groupBy  = 'branch',
                @view     = :viewBy
        ");

        $totalStmt = $pdo->prepare("
            EXEC sp_Finance_Report_update
                @year     = :year,
                @month    = :month,
                @region   = :region,
                @location = :location,
                @branch   = :branch,
                @groupBy  = 'total',
                @view     = :viewBy
        ");

        $groupedByLocationStmt->execute([
            'year'     => $filters['year'],
            'month'    => $filters['month'],
            'region'   => $filters['region'],
            'location' => $filters['location'],
            'branch'   => $filters['branch'],
            'viewBy'   => $filters['viewBy'],
        ]);

        $stmt->execute([
            'year'     => $filters['year'],
            'month'    => $filters['month'],
            'region'   => $filters['region'],
            'location' => $filters['location'],
            'branch'   => $filters['branch'],
            'viewBy'   => $filters['viewBy'],
        ]);

        $totalStmt->execute([
            'year'     => $filters['year'],
            'month'    => $filters['month'],
            'region'   => $filters['region'],
            'location' => $filters['location'],
            'branch'   => $filters['branch'],
            'viewBy'   => $filters['viewBy'],
        ]);

        $groupedByLocationData = $groupedByLocationStmt->fetchAll(PDO::FETCH_ASSOC);
        // dd($groupedByLocationData);
        $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalData = $totalStmt->fetchAll(PDO::FETCH_ASSOC);
        // dd($totalData);

        $lastUpdatedDate = '2025-09-30 00:00:00'; // Automatic data refresh not yet implemented

        /* -------------------- Targets -------------------- */
        $targets = collect();
        $normalizedTargets = [];

        $year  = (int) $request->input('year', now()->year);
        $month = $filters['month']; // null = YTD

        $periodMonth = $month
            ? Carbon::parse($month)->format('m')   // overwrite if month selected
            : now()->format('m');                  // YTD → current month

        $period = $year . $periodMonth;

        $targets = $this->reportSDN->table('Finance_Target')
            ->where('Periode', $period)
            ->get();

        // dd($targets);

        $normalizedTargets = $targets->mapWithKeys(function ($t) {
            $key = strtolower(
                preg_replace('/[^a-z0-9]/', '', $t->Type)
            );

            return [
                $key => [
                    'value'    => (float) $t->Value,
                    'operator' => $t->Operator,
                ]
            ];
        })->toArray();

        /* -------------------- Column Selection -------------------- */
        $alwaysMioColumns = [
            'Revenue (in Mio)',
            'Margin (in Mio)',
            'PBT (in Mio)',
        ];

        $headers = collect($rawData[0] ?? [])
            ->keys()
            ->filter(function ($key) use ($filters, $alwaysMioColumns) {

                if ($key === 'Branch') {
                    return true;
                }

                // Always show Margin & PBT in Mio
                if (in_array($key, $alwaysMioColumns, true)) {
                    return true;
                }

                // Toggle other columns
                return match ($filters['viewBy']) {
                    'percent' => str_contains($key, '(%)'),
                    'idr'     => str_contains($key, '(in Mio)'),
                    default   => false,
                };
            })
            ->values();


        $normalizedHeaderMap = collect($headers)->mapWithKeys(fn ($h) => [
            $h => strtolower(preg_replace('/[^a-z0-9]/', '', $h))
        ]);

        $normalizeRow = function (array $row) use ($headers, $normalizedTargets, $normalizedHeaderMap) {
            return collect($headers)->mapWithKeys(function ($col) use ($row, $normalizedTargets, $normalizedHeaderMap) {
                $value     = $row[$col] ?? null;
                $isNumeric = is_numeric($value);
                $status    = null;

                // ✅ ROUND Mio columns to integer (display only)
                if ($isNumeric) {
                    if (str_contains($col, '(in Mio)')) {
                        $value = round((float) $value, 0);
                    } elseif (str_contains($col, '%')) {
                        $value = round((float) $value, 2);
                    }
                }

                if ($isNumeric && isset($normalizedTargets[$normalizedHeaderMap[$col]])) {
                    $t = $normalizedTargets[$normalizedHeaderMap[$col]];
                    $actual = (float) $value;

                    $status = match ($t['operator']) {
                        '>'  => $actual >  $t['value'],
                        '>=' => $actual >= $t['value'],
                        '<'  => $actual <  $t['value'],
                        '<=' => $actual <= $t['value'],
                        '='  => $actual == $t['value'],
                        default => false
                    } ? 'pass' : 'fail';
                }

                return [$col => compact('value','isNumeric','status')];
            });
        };

        $branchesByLocation = collect($rawData)->groupBy('Location');
        $locationRows = collect($groupedByLocationData)->map(function ($loc) use ($branchesByLocation, $normalizeRow) {
            return [
                'location' => $loc['Location'],
                'summary'  => $normalizeRow($loc),
                'branches' => ($branchesByLocation[$loc['Location']] ?? collect())
                                ->map(fn ($b) => $normalizeRow($b))
            ];
        });

        // dd($rows);

        $grandTotal = isset($totalData[0])
            ? $normalizeRow($totalData[0])
            : collect();



        return view('monitoring-dashboard.finance.pbt.pbt_index', compact(
            'months',
            'selectedMonth',
            'branches',
            'locations',
            'regions',
            'filters',
            'headers',
            'locationRows',
            'grandTotal',
            'targets',
            'normalizedTargets',
            'lastUpdatedDate'
        ));
    }
}
