@extends('layouts.app')
@section('titlepage', 'Sales Return Dashboard')

@section('content')
@section('navigasi')
<div class="d-flex flex-column">
    <span>Sales Return Dashboard</span>

    @isset($lastUpdatedDate)
        <span class="text-muted small">
            Last Updated: {{ \Carbon\Carbon::parse($lastUpdatedDate)->format('d M Y H:i') }}
        </span>
    @endisset
</div>
@endsection

@php
$filterQuery = http_build_query(request()->only([
    'startDate',
    'endDate',
    'region',
    'distributionChannel',
    'businessType',
    'principalCode',
    'sortBy',
    'orderBy',
]));
@endphp


<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                {{-- ===================== TREND ===================== --}}
                <div class="card shadow-sm p-3 mb-4">
                    <h6 class="fw-bold text-primary mb-3">📈 YTD Monthly Return Trend</h6>
                    <canvas id="trendChart" style="height: 300px !important; max-height: 300px;"></canvas>
                </div>

                {{-- ===================== Filters ===================== --}}
                <form action="{{ route('sales.return.dashboard.index') }}" method="GET"
                    class="d-flex flex-wrap align-items-end gap-3 mb-4">

                    {{-- Start Date --}}
                    <div>
                        <label class="small text-muted fw-bold">Start Date</label>
                        <input type="date" name="startDate" value="{{ $startDate->format('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="small text-muted fw-bold">End Date</label>
                        <input type="date" name="endDate" value="{{ $endDate->format('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>

                    {{-- Region Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Region</label>
                        <select name="region" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($regions as $r)
                            <option value="{{ $r->region }}"
                                {{ request('region') == $r->region ? 'selected' : '' }}>
                                {{ $r->region }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Branch Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Branch</label>
                        <select name="branch" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($branches as $b)
                            <option value="{{ $b->territory_code }}"
                                {{ request('branch') == $b->territory_code ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Distribution Channel Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Distribution Channel</label>
                        <select name="distributionChannel" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($distributionChannels as $dc)
                            <option value="{{ $dc->DistributionChannel }}"
                                {{ request('distributionChannel') == $dc->DistributionChannel ? 'selected' : '' }}>
                                {{ $dc->DistributionChannel }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Business Type Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Business type</label>
                        <select name="businessType" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($businessTypes as $by)
                            <option value="{{ $by->BusinessType }}"
                                {{ request('businessType') == $by->BusinessType ? 'selected' : '' }}>
                                {{ $by->BusinessType }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Principal Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Principal</label>
                        <select name="principalCode" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($principals as $p)
                            <option value="{{ $p->PrincipalCode }}"
                                {{ request('principalCode') == $p->PrincipalCode ? 'selected' : '' }}>
                                {{ $p->PrincipalName }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SortBy Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Sort By</label>
                        <select name="sortBy" class="form-control form-control-sm">
                            <option value="TotalSales"   {{ request('sortBy', 'TotalReturns') == 'TotalSales' ? 'selected' : '' }}>Sales</option>
                            <option value="TotalReturns" {{ request('sortBy', 'TotalReturns') == 'TotalReturns' ? 'selected' : '' }}>Return</option>
                            <option value="ReturnRatio"  {{ request('sortBy', 'TotalReturns') == 'ReturnRatio' ? 'selected' : '' }}>Return Rate</option>
                        </select>
                    </div>

                    {{-- OrderBy Filter --}}
                    <div>
                        <label class="small text-muted fw-bold">Order By</label>
                        <select name="orderBy" class="form-control form-control-sm">
                            <option value="ASC"  {{ request('orderBy', 'DESC') == 'ASC'  ? 'selected' : '' }}>ASC</option>
                            <option value="DESC" {{ request('orderBy', 'DESC') == 'DESC' ? 'selected' : '' }}>DESC</option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </a>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-filter me-1"></i> Apply
                        </button>
                    </div>

                </form>

                {{-- ====== PERIOD SUMMARY ====== --}}
                <div class="alert alert-primary py-2 mb-3">
                    <strong>Selected Period:</strong> {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}
                </div>

                @php
                    $trend = $highLevelOverview['trend'] ?? [];

                    $lastMonth = count($trend) >= 2 ? $trend[count($trend) - 2] : null;
                    $currentMonth = count($trend) >= 1 ? $trend[count($trend) - 1] : null;

                    $momChange = 0;
                    if ($lastMonth && $currentMonth && $lastMonth['ReturnValue'] > 0) {
                        $momChange = (($currentMonth['ReturnValue'] - $lastMonth['ReturnValue']) / $lastMonth['ReturnValue']) * 100;
                    }
                @endphp


                {{-- ===================== KPI ===================== --}}
                @php
                $kpi = $highLevelOverview['highLevel'][0] ?? [
                    'TotalSales' => 0,
                    'TotalReturns' => 0,
                    'ValueReturnRate' => 0,
                    'SalesQty' => 0,
                    'ReturnQty' => 0,
                    'QtyReturnRate' => 0,
                ];
                @endphp

                <!-- ===================== ROW 1: Sales, Return Value, Return Rate ===================== -->
                <div class="row g-3 mb-4">

                    <!-- TOTAL SALES -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Total Sales</h6>
                            <div class="h4 fw-bold text-success">
                                Rp. {{ $kpi['TotalSales'] ?? 0 }}
                            </div>
                        </div>
                    </div>

                    <!-- RETURN VALUE -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Return Value</h6>
                            <div class="h4 fw-bold text-danger">
                                Rp. {{ $kpi['TotalReturns'] ?? 0 }}
                            </div>
                        </div>
                    </div>

                    <!-- RETURN RATE -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Return Rate</h6>
                            <div class="h4 fw-bold text-secondary">
                                {{ number_format($kpi['ValueReturnRate'] ?? 0, 2) }}%
                            </div>
                        </div>
                    </div>

                </div>


                <!-- ===================== ROW 2: Sales Qty, Return Qty, (Optional) ===================== -->
                <div class="row g-3 mb-4">

                    <!-- SALES QTY -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Sales Qty (In KAR)</h6>
                            <div class="h4 fw-bold text-success">
                                {{ number_format($kpi['SalesQty'] ?? 0, 0) }}
                            </div>
                        </div>
                    </div>

                    <!-- RETURN QTY -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Return Qty (In KAR)</h6>
                            <div class="h4 fw-bold text-danger">
                                {{ number_format($kpi['ReturnQty'] ?? 0, 0) }}
                            </div>
                        </div>
                    </div>

                    <!-- OPTIONAL KPI (Blank or Add Return Qty Rate Later) -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Qty Return Rate</h6>
                            <div class="h4 fw-bold text-secondary">
                                {{ number_format(($kpi['QtyReturnRate'] ?? 0), 2) }}%
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== TOP CHARTS ===================== --}}
                <h5 class="fw-bold text-primary mt-4">🏆 Top Rankings</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Distribution Channels by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'distributionChannel']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    View full channel report
                                </a>
                            </div>
                            <canvas id="distributionChannelChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Business Types by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'businessType']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    View business type report
                                </a>
                            </div>
                            <canvas id="businessTypeChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    @if ($scope !== 'Branch')
                        <div class="col-md-12">
                            <div class="card shadow-sm p-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Regions by {{ $sortBy }} {{ $orderBy }}</h6>
                                    <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'region']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                        View regional performance
                                    </a>
                                </div>
                                <canvas id="regionChart" style="height: 300px !important; max-height: 300px;"></canvas>
                            </div>
                        </div>
                    @endif

                    @if ($scope === 'Head Office' || $scope === 'Region')
                        <div class="col-md-12">
                            <div class="card shadow-sm p-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Top 10 Branches by {{ $sortBy }} {{ $orderBy }}</h6>
                                    <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'branch']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                        View full branch report
                                    </a>
                                </div>
                                <canvas id="branchChart" style="height: 300px !important; max-height: 300px;"></canvas>
                            </div>
                        </div>
                    @endif

                    <!-- <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 10 Customer Group (currently still customer) by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'customerGroup']) }}" class="text-primary small text-decoration-underline" target="_blank">
                                    Explore customer group report
                                </a>
                            </div>
                            <canvas id="customerGroupChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div> -->

                    <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 20 Stores by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'store']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    Open store performance report
                                </a>
                            </div>
                            <canvas id="storeChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 10 Salesman by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'salesmen']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    View salesman breakdown
                                </a>
                            </div>
                            <canvas id="salesmanChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 20 Principal by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'principal']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    Explore principal insights
                                </a>
                            </div>
                            <canvas id="principalChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 20 SKUs by {{ $sortBy }} {{ $orderBy }}</h6>
                                <a href="{{ route('sales.return.dashboard.header.index', ['dimension' => 'sku']) }}?{{ $filterQuery }}" class="text-primary small text-decoration-underline" target="_blank">
                                    Explore SKU insights
                                </a>
                            </div>
                            <canvas id="skuChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



@push('myscript')
<script>
window.dashboardData = {
    trendData: @json($highLevelOverview['trend']),
    topDistributionChannels: @json($highLevelOverview['topDistributionChannels']),
    topRegions: @json($highLevelOverview['topRegions']),
    topPrincipals: @json($highLevelOverview['topPrincipals']),
    topBusinessTypes: @json($highLevelOverview['topBusinessTypes']),
    topBranches: @json($highLevelOverview['topBranches']),
    topSKUs: @json($highLevelOverview['topSKUs']),
    topSalesman: @json($highLevelOverview['topSalesman']),
    topStores: @json($highLevelOverview['topStores'])
};
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>
<script src="{{ asset('assets/js/pages/sales-dashboard/clustered-chart.js') }}"></script>
<script src="{{ asset('assets/js/pages/sales-dashboard/trend-chart.js') }}"></script>

<script src="{{ asset('assets/js/pages/sales-dashboard/sales-return-dashboard.js') }}"></script>

@endpush