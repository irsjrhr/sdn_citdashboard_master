<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TicketingController extends Controller
{
    public function index(Request $request)
    {
        // Filter options (not applied to query yet)
        $departments = Ticketing::select('department')->distinct()->pluck('department'); 
        $products = Ticketing::select('product')->distinct()->pluck('product');
        $types = Ticketing::select('type')->distinct()->pluck('type');
        $categories = Ticketing::select('epic')->distinct()->pluck('epic');
        $statuses = Ticketing::select('status')->distinct()->pluck('status');
        $priorities = Ticketing::select('priority')->distinct()->pluck('priority');
        

        // ===== 1️⃣ DATE FILTERS =====
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now();

        $sqlsrv = DB::connection('sqlsrv');

        $tickets = Ticketing::whereBetween('created_at', [$startDate, $endDate])
            ->paginate(20);

        // ===== 2️⃣ TICKET VOLUME & TRENDS =====
        $ticketsByStatus = $sqlsrv->table('tickets as t')
            ->join('mst_ticket_status as s', 't.status', '=', 's.raw_status')
            ->select('s.status_category', DB::raw('COUNT(t.id) as tickets'))
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->whereIn('t.project', ['DMS - SR', 'DMS  - MIX2'])
            ->where('t.epic', '!=', 'Baseline Issue')
            ->groupBy('s.status_category')
            ->get();

        // Convert to key-value pairs
        $statusCounts = $ticketsByStatus->pluck('tickets', 'status_category');
        // dd($statusCounts);

        // Safely extract each status (default to 0 if not found)
        $totalTickets = $statusCounts->sum();
        $closedTickets   = $statusCounts['Closed']   ?? 0;
        $ongoingTickets  = $statusCounts['Ongoing']  ?? 0;
        $resolvedTickets = $statusCounts['Resolved'] ?? 0;
        $openTickets     = $statusCounts['Open']     ?? 0;

        // Compare to previous period
        $ticketsLastPeriod = Ticketing::whereBetween('created_at', [
            Carbon::parse($startDate)->subDays($endDate->diffInDays($startDate)),
            Carbon::parse($startDate)->subDay(),
        ])->count();

        $trend = $ticketsLastPeriod > 0 
            ? round((($totalTickets - $ticketsLastPeriod) / $ticketsLastPeriod) * 100, 1)
            : 0;

        $ticketsByType = Ticketing::select('type', DB::raw('COUNT(*) AS total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('epic','!=', 'Baseline Issue') 
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy('type')
            ->orderBy(DB::raw('COUNT(*)'))
            ->pluck('total', 'type');

        $ticketsByDept = Ticketing::select('department', DB::raw('COUNT(*) AS total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('epic','!=', 'Baseline Issue')
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy('department')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->pluck('total', 'department');

        $monthlyTrendRaw = Ticketing::select(
                DB::raw("FORMAT(created_at, 'yyyy-MM') AS month"),
                DB::raw("COUNT(*) AS total")
            )
            ->where('epic','!=', 'Baseline Issue') 
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw("FORMAT(created_at, 'yyyy-MM')"))
            ->orderBy(DB::raw("FORMAT(created_at, 'yyyy-MM')"))
            ->pluck('total', 'month'
        );

        // 2️⃣ Fill missing months with 0
        $monthlyTrend = collect(range(1, now()->month))
            ->mapWithKeys(fn($m) => [
                sprintf('%04d-%02d', $currentYear, $m) => $monthlyTrendRaw[sprintf('%04d-%02d', $currentYear, $m)] ?? 0
            ]);

        $ticketsByCategory = Ticketing::select('epic', DB::raw('COUNT(*) AS total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('epic','!=', 'Baseline Issue') 
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy('epic')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->get()
            ->pipe(function ($data) {
                $top = $data->take(5);
                $others = $data->skip(5)->sum('total');
                if ($others > 0) {
                    $top->push((object)['epic' => 'Others', 'total' => $others]);
                }
                return $top->pluck('total', 'epic');
            });
        
        $ticketsByProduct = Ticketing::select('product', DB::raw('COUNT(*) AS total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('epic','!=', 'Baseline Issue') 
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy('product')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->get()
            ->pipe(function ($data) {
                $top = $data->take(5);
                $others = $data->skip(5)->sum('total');
                if ($others > 0) {
                    $top->push((object)['product' => 'Others', 'total' => $others]);
                }
                return $top->pluck('total', 'product');
            });

        // ===== 3️⃣ SLA & RESPONSE PERFORMANCE =====
        $result = $sqlsrv->table('tickets as t')
            ->join('mst_ticket_status as s', 's.raw_status', '=', 't.status')
            ->where('t.epic', '!=', 'Baseline Issue')
            ->whereIn('t.project', ['DMS - SR', 'DMS  - MIX2'])
            ->whereIn('s.status_category', ['Resolved', 'Closed'])
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->selectRaw("
                -- 🕒 Average response time (Created → In Progress)
                AVG(
                    CASE 
                        WHEN t.in_progress_at IS NOT NULL 
                        THEN DATEDIFF(MINUTE, t.created_at, t.in_progress_at) / 60.0 
                    END
                ) AS avg_response_hours,

                -- ⏱️ Average resolution time (Created → Resolved or Closed)
                AVG(
                    CASE 
                        WHEN t.resolved_at IS NOT NULL 
                        THEN DATEDIFF(MINUTE, t.created_at, t.resolved_at) / 60.0 
                    END
                ) AS avg_resolution_hours,

                -- ✅ Total Resolved or Closed
                COUNT(CASE WHEN t.resolved_at IS NOT NULL THEN 1 END) AS total_resolved,

                -- 🚀 Within SLA (24h) or still open
                COUNT(
                    CASE 
                        WHEN (t.resolved_at IS NULL AND t.closed_at IS NULL)
                            OR DATEDIFF(HOUR, t.created_at, COALESCE(t.resolved_at, t.closed_at)) <= 24 
                        THEN 1 
                    END
                ) AS within_sla
            ")
            ->first();


        $avgResponseHours   = round($result->avg_response_hours ?? 0, 1);
        $avgResolutionHours = round($result->avg_resolution_hours ?? 0, 1);
        $totalFinished      = $resolvedTickets + $closedTickets;
        $withinSLA          = (int) ($result->within_sla ?? 0);

        $slaWithinPct = $totalFinished > 0 
            ? min(100, round(($withinSLA / $totalFinished) * 100, 1)) 
            : 0;

        $slaBreachPct = max(0, round(100 - $slaWithinPct, 1));

        

        // ===== 4️⃣ PRIORITY & IMPACT =====
        $highPriorityYTDTrendRaw = Ticketing::select(
                DB::raw("FORMAT(created_at, 'yyyy-MM') AS month"),
                DB::raw("COUNT(*) AS total")
            )
            ->join('mst_ticket_priority as p', 'tickets.priority', '=', 'p.raw_priority')
            ->whereYear('created_at', $currentYear)
            ->where('p.priority_category', 'High')
            ->where('epic','!=', 'Baseline Issue')
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy(DB::raw("FORMAT(created_at, 'yyyy-MM')"))
            ->pluck('total', 'month'
        );

        $highPriorityYTDTrend = collect(range(1, (int) $currentMonth))
            ->mapWithKeys(function ($m) use ($highPriorityYTDTrendRaw, $currentYear) {
                $monthStr = str_pad((string) $m, 2, '0', STR_PAD_LEFT); // convert to "01", "02", etc.
                $key = "$currentYear-$monthStr";
                return [$key => $highPriorityYTDTrendRaw[$key] ?? 0];
            });

        $ticketsByPriority = Ticketing::query()
            ->join('mst_ticket_priority as p', 'tickets.priority', '=', 'p.raw_priority')
            ->select('p.priority_category', DB::raw('COUNT(tickets.id) as total'))
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->where('epic', '!=', 'Baseline Issue')
            ->whereIn('project', ['DMS - SR', 'DMS  - MIX2'])
            ->groupBy('p.priority_category')
            ->orderByRaw("MAX(p.level) DESC")
            ->pluck('total', 'p.priority_category');

        $highPriorityUnresolved = Ticketing::query()
            ->join('mst_ticket_status as mts', 'tickets.status', '=', 'mts.raw_status')
            ->select('tickets.code', 'tickets.name')
            ->where('tickets.priority', 'High')
            ->whereNotIn('mts.status_category', ['Closed', 'Resolved'])
            ->where('epic','!=', 'Baseline Issue') 
            ->orderBy('tickets.created_at')
            ->limit(3)
            ->get();

        // ===== 5️⃣ USER SATISFACTION =====
        $resolvedAndClosedTicketsCount = $resolvedTickets + $closedTickets;

        $acknowledgedCount = $closedTickets;
        $unacknowledgedCount = $resolvedTickets;

        $ackPct = $resolvedAndClosedTicketsCount > 0 ? round(($closedTickets / $resolvedAndClosedTicketsCount) * 100, 1) : 0;
        
        // Step 1: Get top 3 related tickets (excluding Baseline Issue)
        $recurringTickets = $sqlsrv->table('tickets AS t')
            ->join('mst_ticket_status AS mts', 't.status', '=', 'mts.raw_status')
            ->whereNotNull('t.related_tickets')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->whereRaw("LTRIM(RTRIM(t.related_tickets)) <> ''")
            ->selectRaw("
                t.id,
                t.code,
                REPLACE(t.related_tickets, ' (related_to)', '') AS clean_related,
                t.name,
                t.status,
                t.priority,
                t.created_at
            ");

        $recurringComplaints = $sqlsrv->table(DB::raw("({$recurringTickets->toSql()}) AS c"))
            ->mergeBindings($recurringTickets)
            ->join('tickets AS r', 'c.clean_related', '=', 'r.code')
            ->leftJoin('mst_ticket_status AS rmts', 'r.status', '=', 'rmts.raw_status')
            ->selectRaw("
                r.code AS root_code,
                r.name AS root_name,
                r.status AS root_status,
                c.code AS related_code,
                c.name AS related_name,
                c.status AS related_status,
                c.priority AS related_priority,
                c.created_at AS related_created,
                COUNT(*) OVER (PARTITION BY r.code) AS recurring_count
            ")
            ->where('r.project', '=', 'DMS - SR')
            ->orderByDesc('recurring_count')   // most recurring baselines first
            ->orderByDesc('related_created')   // newest related tickets first
            ->get();


        // ===== 6️⃣ OUTSTANDING & AGING =====
        $result = Ticketing::whereHas('statusMaster', function ($q) {
                $q->where('status_category', '!=', 'Closed')
                ->where('status_category', '!=', 'Resolved');
            })
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->where('epic', '!=', 'Baseline Issue')
            ->selectRaw("
                COUNT(CASE WHEN DATEDIFF(DAY, created_at, GETDATE()) BETWEEN 0 AND 7 THEN 1 END) AS aging0to7,
                COUNT(CASE WHEN DATEDIFF(DAY, created_at, GETDATE()) BETWEEN 8 AND 14 THEN 1 END) AS aging8to14,
                COUNT(CASE WHEN DATEDIFF(DAY, created_at, GETDATE()) BETWEEN 15 AND 30 THEN 1 END) AS aging15to30,
                COUNT(CASE WHEN DATEDIFF(DAY, created_at, GETDATE()) > 30 THEN 1 END) AS agingOver30
            ")
            ->first();

        $aging0to7   = $result->aging0to7;
        $aging8to14  = $result->aging8to14;
        $aging15to30 = $result->aging15to30;
        $agingOver30 = $result->agingOver30;



        // ===== 7️⃣ EXECUTIVE SUMMARY =====
        $topIssues = Ticketing::select('type', DB::raw('COUNT(*) AS total'))
            ->where('epic','!=', 'Baseline Issue') 
            ->groupBy('type')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'type');

        // ===== 8️⃣ BASELINE ISSUES TICKETS =====
        $baselineAnalysis = 0;
        $baselineDev = 0;
        $baselineUat = 0;
        $baselineDeployment = 0;
        $baselineQuery = Ticketing::query()
            ->join('mst_ticket_status as mts', 'tickets.status', '=', 'mts.raw_status')
            ->selectRaw('COUNT(tickets.id) as total, mts.baseline_issue_category')
            ->where('project', '=', 'DMS - SR')
            ->where('tickets.epic', 'Baseline Issue')
            ->where('tickets.priority', 'High')
            ->where('tickets.type', 'Bug')
            ->groupBy('mts.baseline_issue_category')
            ->get();

            foreach ($baselineQuery as $row) {
                switch ($row->baseline_issue_category) {
                    case 'Analysis':
                        $baselineAnalysis = $row->total;
                        break;
                    case 'Dev':
                        $baselineDev = $row->total;
                        break;
                    case 'UAT':
                        $baselineUat = $row->total;
                        break;
                    case 'Deployment':
                        $baselineDeployment = $row->total;
                        break;
                }
            }
        $baselineStatusMapping = $sqlsrv->table('mst_ticket_status')
            ->select('baseline_issue_category', DB::raw('STRING_AGG(raw_status, \', \') AS statuses'))
            ->whereNotNull('baseline_issue_category')
            ->groupBy('baseline_issue_category')
            ->get();

        $baselineDevTickets = DB::connection('sqlsrv')->select("
            SELECT 
                t.code AS root_code,
                t.name AS root_name,
                LTRIM(RTRIM(REPLACE(split.value, ' (related_to)', ''))) AS related_code,
                r.name AS related_name,
                r.end_time AS uat_date,
                r.revised_end_time AS updated_uat_date,
                r.end_time_revised_counter AS counter,
                -- ✅ difference in days between revised_end_time or end_time and current date
                DATEDIFF(
                    DAY, 
                    COALESCE(r.revised_end_time, r.end_time), 
                    GETDATE()
                ) AS days_since_end
            FROM tickets t
            JOIN mst_ticket_status s 
                ON t.status = s.raw_status
            OUTER APPLY (
                SELECT LTRIM(RTRIM(REPLACE(value, ' (related_to)', ''))) AS value
                FROM STRING_SPLIT(t.related_tickets, ',')
                WHERE LTRIM(RTRIM(REPLACE(value, ' (related_to)', ''))) NOT LIKE 'SR-%'
            ) AS split
            LEFT JOIN tickets r 
                ON split.value = r.code
            WHERE 
                t.epic = 'Baseline Issue'
                AND t.project = 'DMS - SR'
                AND s.baseline_issue_category = 'Dev'
                AND t.priority = 'High'
                AND t.type = 'Bug'
                AND s.status_category != 'Closed'
            ORDER BY 
                root_code ASC;
        ");


        $baseQuery = Ticketing::whereHas('statusMaster', fn($q) =>
                $q->where('status_category', '!=', 'Closed'))
            ->where('project', '=', 'DMS - SR')
            ->where('epic', '=', 'Baseline Issue')
            ->where('priority', '=', 'High')
            ->where('type', '=', 'Bug');

        $baselineAging0to15 = (clone $baseQuery)
            ->whereRaw('DATEDIFF(DAY, tickets.created_at, GETDATE()) BETWEEN 0 AND 15')
            ->count();

        $baselineAging16to30 = (clone $baseQuery)
            ->whereRaw('DATEDIFF(DAY, tickets.created_at, GETDATE()) BETWEEN 16 AND 30')
            ->count();

        $baselineAging31to45 = (clone $baseQuery)
            ->whereRaw('DATEDIFF(DAY, tickets.created_at, GETDATE()) BETWEEN 31 AND 45')
            ->count();

        $baselineAgingOver45 = (clone $baseQuery)
            ->whereRaw('DATEDIFF(DAY, tickets.created_at, GETDATE()) > 45')
            ->count();

        $recurringBaseline = collect($sqlsrv->select(" 
            WITH Related AS (
                SELECT 
                    t.id,
                    t.code,
                    LTRIM(RTRIM(value)) AS clean_related,
                    t.name,
                    t.status,
                    t.priority,
                    t.created_at
                FROM tickets AS t
                CROSS APPLY STRING_SPLIT(REPLACE(t.related_tickets, ' (related_to)', ''), ',') AS s
                WHERE t.related_tickets IS NOT NULL
                AND LTRIM(RTRIM(t.related_tickets)) <> ''
            ),
            Mapped AS (
                SELECT
                    r.code AS root_code,
                    r.name AS root_name,
                    r.status AS root_status,
                    c.code AS related_code,
                    c.name AS related_name,
                    c.status AS related_status,
                    c.priority AS related_priority,
                    c.created_at AS related_created,
                    COUNT(*) OVER (PARTITION BY r.code) AS recurring_count
                FROM Related AS c
                INNER JOIN tickets AS r ON c.clean_related = r.code
                LEFT JOIN mst_ticket_status AS rmts ON r.status = rmts.raw_status
                WHERE REPLACE(LTRIM(RTRIM(c.clean_related)), ' ', '') LIKE 'SR-%'
                AND r.project = 'DMS - SR'
                AND r.epic = 'Baseline Issue'
                AND c.code LIKE 'SR-%'
            )
            SELECT *
            FROM Mapped
            ORDER BY recurring_count DESC, related_created DESC;
        "));

        // ===== 8️⃣ AGGREGATED DASHBOARD DATA =====
        $chartData = [
            // KPI
            'totalTickets' => $totalTickets,
            'openCount' => $openTickets,
            'openRate' => $totalTickets > 0 ? round(($openTickets / $totalTickets) * 100, 1) : 0,
            'ongoingCount' => $ongoingTickets,
            'ongoingRate' => $totalTickets > 0 ? round(($ongoingTickets / $totalTickets) * 100, 1) : 0,
            'resolvedCount' => $resolvedTickets,
            'resolvedRate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0,
            'closedTickets' => $closedTickets,
            'closedTicketRate' => $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100, 1) : 0,
            'trend' => $trend,
            'firstResponse' => round($avgResponseHours, 1),
            'resolution' => round($avgResolutionHours, 1),

            'sla' => [
                'withinCount' => $withinSLA,
                'breachCount' => $totalFinished - $withinSLA,
                'withinPct' => $slaWithinPct,
                'breachPct' => $slaBreachPct,
                'totalResolved' => $totalFinished,
            ],

            'acknowledgement' => [
                'acknowledgedCount'   => $acknowledgedCount,
                'unacknowledgedCount' => $unacknowledgedCount,
                'ackPct'    => $ackPct,
                'unackPct'  => round(100 - $ackPct, 1),
                'totalResolved'       => $totalFinished,
            ],
            
            'highPriorityUnresolved' => $highPriorityUnresolved,
            
            // Charts
            'ticketsByType' => $ticketsByType,
            'ticketsByDept' => $ticketsByDept,
            'ticketsByCategory' => $ticketsByCategory, // top 5 + others
            'ticketsByProduct' => $ticketsByProduct, // top 5 + others
            'ticketsByPriority' => $ticketsByPriority,
            'monthlyTrend' => $monthlyTrend,
            'highPriorityYTDTrend' => $highPriorityYTDTrend,
            'recurringComplaints' => $recurringComplaints,
            'aging' => [
                '7d' => $aging0to7,
                '14d' => $aging8to14,
                '30d' => $aging15to30,
                'moreThan30d' => $agingOver30,
            ],

            'baselineIssue' => [
                'analysis' => $baselineAnalysis,
                'dev' => $baselineDev,
                'uat' => $baselineUat,
                'deployment' => $baselineDeployment,
            ],
            'baselineStatusMapping' => $baselineStatusMapping,
            'baselineDevTickets' => $baselineDevTickets,
            'baselineAging' => [
                '15d' => $baselineAging0to15,
                '30d' => $baselineAging16to30,
                '45d' => $baselineAging31to45,
                'moreThan45d' => $baselineAgingOver45,
            ],
            'recurringBaseline' => $recurringBaseline,

            // Exec Insights
            'topIssues' => $topIssues,
        ];

        return view('ticketing.index', compact(
            'tickets',
            'chartData',
            'startDate',
            'endDate',
            'departments',
            'products',
            'types',
            'categories',
            'statuses',
            'priorities',
        ));
    }

    public function uploadIndex(){
        $uploadHistory = DB::connection('sqlsrv')->table('ticket_upload_log')
            ->select('*')
            ->orderByDesc('uploaded_date')
            ->get();

        return view('ticketing.upload.index', compact(
            'uploadHistory'
        ));
    }

    public function uploadExcel(Request $request)
    {
        $user = auth()->user()->name ?? 'SYSTEM';
        $filename = $request->file('file')->getClientOriginalName();

        try {
            // ===================== VALIDATION =====================
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);

            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $expectedHeaders = [
                'A' => 'Project', 'B' => 'Epic', 'C' => 'Code', 'D' => 'Ref Code',
                'E' => 'Name', 'F' => 'Content', 'G' => 'Product', 'H' => 'Environment',
                'I' => 'Owner', 'J' => 'PIC DEV', 'K' => 'PIC QA', 'L' => 'PIC SIT',
                'M' => 'PIC UAT', 'N' => 'Status', 'O' => 'Type', 'P' => 'Priority',
                'Q' => 'Created At', 'R' => 'Waiting user Feedback at',
                'S' => 'In-progress at', 'T' => 'Resolved at', 'U' => 'Closed at',
                'V' => 'Related tickets', 'W' => 'Start Time', 'X' => 'End Time',
                'Y' => 'Related user', 'Z' => 'Department', 'AA' => 'Location',
                'AB' => 'Root Cause', 'AC' => 'Solution'
            ];

            foreach ($expectedHeaders as $col => $expectedHeader) {
                $actualHeader = trim($sheet->getCell("{$col}1")->getValue());
                if (strcasecmp($actualHeader, $expectedHeader) !== 0) {
                    throw new \Exception(
                        "Invalid template — expected '{$expectedHeader}' in column {$col}, found '{$actualHeader}'"
                    );
                }
            }

            // ===================== BUILD DATA =====================
            $highestRow = $sheet->getHighestRow();
            $data = collect();

            $backOfficeList = [
                "Order Management System (OMS)",
                "Warehouse Management System (WMS)",
                "Warehouse Management System (WMS) 2",
                "Sales Force Automation (SFA) - Back Office",
            ];

            for ($row = 2; $row <= $highestRow; $row++) {
                $rawProduct = trim($sheet->getCell("G{$row}")->getValue());

                $data->push([
                    'code' => $sheet->getCell("C{$row}")->getValue(),
                    'project' => $sheet->getCell("A{$row}")->getValue(),
                    'epic' => $sheet->getCell("B{$row}")->getValue(),
                    'ref_code' => $sheet->getCell("D{$row}")->getValue(),
                    'name' => $sheet->getCell("E{$row}")->getValue(),
                    'content' => $sheet->getCell("F{$row}")->getValue(),
                    'product' => in_array($rawProduct, $backOfficeList) ? 'Back Office' : $rawProduct,
                    'environment' => $sheet->getCell("H{$row}")->getValue(),
                    'owner' => $sheet->getCell("I{$row}")->getValue(),
                    'pic_dev' => $sheet->getCell("J{$row}")->getValue(),
                    'pic_qa' => $sheet->getCell("K{$row}")->getValue(),
                    'pic_sit' => $sheet->getCell("L{$row}")->getValue(),
                    'pic_uat' => $sheet->getCell("M{$row}")->getValue(),
                    'status' => $sheet->getCell("N{$row}")->getValue(),
                    'type' => $sheet->getCell("O{$row}")->getValue(),
                    'priority' => $sheet->getCell("P{$row}")->getValue(),
                    'created_at' => $this->parseExcelDate($sheet->getCell("Q{$row}")),
                    'waiting_user_feedback_at' => $this->parseExcelDate($sheet->getCell("R{$row}")),
                    'in_progress_at' => $this->parseExcelDate($sheet->getCell("S{$row}")),
                    'resolved_at' => $this->parseExcelDate($sheet->getCell("T{$row}")),
                    'closed_at' => $this->parseExcelDate($sheet->getCell("U{$row}")),
                    'related_tickets' => $sheet->getCell("V{$row}")->getValue(),
                    'start_time' => $this->parseExcelDate($sheet->getCell("W{$row}")),
                    'end_time' => $this->parseExcelDate($sheet->getCell("X{$row}")),
                    'related_user' => $sheet->getCell("Y{$row}")->getValue(),
                    'department' => $sheet->getCell("Z{$row}")->getValue(),
                    'location' => $sheet->getCell("AA{$row}")->getValue(),
                    'root_cause' => $sheet->getCell("AB{$row}")->getValue(),
                    'solution' => $sheet->getCell("AC{$row}")->getValue(),
                ]);
            }

            // ===================== UPSERT =====================
            $codes = $data->pluck('code')->filter()->unique()->values();
            $existing = Ticketing::whereIn('code', $codes)->get()->keyBy('code');

            $newRecords = [];
            $updateRecords = [];

            foreach ($data as $row) {
                if (!$row['code']) continue;

                if (isset($existing[$row['code']])) {
                    $update = $row;
                    unset($update['code']);
                    $updateRecords[] = ['code' => $row['code'], 'update' => $update];
                } else {
                    $newRecords[] = $row;
                }
            }

            DB::transaction(function () use ($newRecords, $updateRecords) {
                // ✅ INSERT IN CHUNKS (SQL Server safe)
                collect($newRecords)
                    ->chunk(25)
                    ->each(function ($chunk) {
                        Ticketing::insert($chunk->toArray());
                    });
                    
                 // ⚠️ Still row-by-row, but now controlled
                collect($updateRecords)
                    ->chunk(25)
                    ->each(function ($chunk) {
                        foreach ($chunk as $item) {
                            Ticketing::where('code', $item['code'])->update($item['update']);
                        }
                    });
            });

            // ===================== SUCCESS LOG =====================
            DB::connection('sqlsrv')->table('ticket_upload_log')->insert([
                'filename' => $filename,
                'uploaded_by' => $user,
                'uploaded_date' => now(),
                'status' => 'Success',
            ]);

            return redirect()->route('ticketing.index')
                ->with('success', 'Excel imported successfully');

        } catch (\Throwable $e) {

            // ===================== FAILED LOG =====================
            DB::connection('sqlsrv')->table('ticket_upload_log')->insert([
                'filename' => $filename,
                'uploaded_by' => $user,
                'uploaded_date' => now(),
                'status' => 'Failed',
            ]);

            return back()->withErrors([
                'file' => '❌ Upload failed: ' . $e->getMessage()
            ]);
        }
    }


    public function downloadUploadTemplate()
    {
        $filePath = storage_path('app/templates/ticketing_upload_template.xlsx');

        return response()->download($filePath, 'ticketing_upload_template.xlsx');
    }

    public function exportToCSV(Request $request)
    {
        // === 1️⃣ DATE FILTERS (same logic as index) ===
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now();

        // === 2️⃣ FETCH DATA ===
        $tickets = Ticketing::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get([
                'project',
                'epic',
                'code',
                'ref_code',
                'name',
                'content',
                'product',
                'environment',
                'owner',
                'pic_dev',
                'pic_qa',
                'pic_sit',
                'pic_uat',
                'status',
                'type',
                'priority',
                'created_at',
                'waiting_user_feedback_at',
                'in_progress_at',
                'resolved_at',
                'closed_at',
                'related_tickets',
                'start_time',
                'end_time',
                'related_user',
                'department',
                'location',
                'root_cause',
                'solution',
            ]);

        // === 3️⃣ PREPARE CSV HEADERS ===
        $filename = 'ZH_Tickets_' . now()->format('Ymd') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // === 4️⃣ STREAM CSV OUTPUT ===
        $columns = [
            'Project', 'Epic', 'Code', 'Ref Code', 'Name', 'Content', 'Product', 'Environment',
            'Owner', 'PIC Dev', 'PIC QA', 'PIC SIT', 'PIC UAT', 'Status', 'Type', 'Priority',
            'Created At', 'Waiting User Feedback At', 'In Progress At', 'Resolved At', 'Closed At',
            'Related Tickets', 'Start Time', 'End Time', 'Related User', 'Department', 'Location',
            'Root Cause', 'Solution'
        ];

        $callback = function() use ($tickets, $columns) {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write header row with ";" as delimiter
            fputcsv($file, $columns, ';');

            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->project,
                    $t->epic,
                    $t->code,
                    $t->ref_code,
                    $t->name,
                    $t->content,
                    $t->product,
                    $t->environment,
                    $t->owner,
                    $t->pic_dev,
                    $t->pic_qa,
                    $t->pic_sit,
                    $t->pic_uat,
                    $t->status,
                    $t->type,
                    $t->priority,
                    $t->created_at,
                    $t->waiting_user_feedback_at,
                    $t->in_progress_at,
                    $t->resolved_at,
                    $t->closed_at,
                    $t->related_tickets,
                    $t->start_time,
                    $t->end_time,
                    $t->related_user,
                    $t->department,
                    $t->location,
                    $t->root_cause,
                    $t->solution,
                ], ';'); // 👈 delimiter set to semicolon
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // === HELPER FUNCTION TO PARSE EXCEL DATES ===
    private function parseExcelDate($cell)
    {
        if (!$cell instanceof Cell) return null;
        $raw = $cell->getValue();

        // Numeric = real Excel date/time serial
        if (is_numeric($raw)) {
            return Date::excelToDateTimeObject($raw)->format('Y-m-d H:i:s');
        }

        // Sometimes Excel stores visible text instead
        $formatted = trim($cell->getFormattedValue());
        if ($formatted && strtotime($formatted)) {
            return date('Y-m-d H:i:s', strtotime($formatted));
        }

        return null;
    }



}
