@extends('layouts.app')
@section('titlepage', $dimensionConfig['title'])

<style>
    .scroll-table {
        max-height: 550px;
        overflow-y: auto;
        border: 1px solid #bbb;
        border-radius: 6px;
    }
    .scroll-table table thead th {
        position: sticky;
        top: 0;
        background: #d1d5db !important;
        color: #111;
        z-index: 20;
    }
</style>

@section('content')
@section('navigasi')
<span>
    <a href="{{ url()->previous() }}"
       class="text-decoration-none fw-bold text-primary">
        National
    </a>

    <span class="mx-1 text-secondary">&gt;</span>

    <span class="fw-bold">{{ $dimensionConfig['title'] }}</span>
</span>
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

                {{-- ===================== APPLIED FILTERS ===================== --}}
                @php
                    // filters you DON'T want to show as chips
                    $hiddenFilterKeys = ['page', 'pageSize'];

                    // only filters that should be visible in UI
                    $visibleFilters = collect($filters)->except($hiddenFilterKeys);
                @endphp

                @if($visibleFilters->filter()->count() > 0)
                    <div class="p-3 mb-4 rounded" 
                        style="background:#e8f1ff; border:1px solid #bcd4ff;">

                        <h6 class="fw-bold mb-2" style="color:#0d6efd;">
                            🔎 Applied Filters
                        </h6>

                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $labels = [
                                    'startDate'           => 'Start Date',
                                    'endDate'             => 'End Date',
                                    'region'              => 'Region',
                                    'distributionChannel' => 'Distribution Channel',
                                    'businessType'        => 'Business Type',
                                    'principalCode'       => 'Principal',
                                    'sortBy'              => 'Sort By',
                                    'orderBy'             => 'Order By',
                                    'code'                => 'Code',
                                ];
                            @endphp

                            @foreach($visibleFilters as $key => $value)
                                @if(!empty($value))
                                    <span class="px-3 py-2 rounded d-inline-flex align-items-center"
                                        style="
                                            background:#0d6efd;
                                            color:white;
                                            font-size:0.85rem;
                                            font-weight:500;
                                        "
                                    >
                                        <strong>{{ $labels[$key] ?? ucwords($key) }}:</strong>&nbsp;{{ $value }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif



                {{-- ===================== KPI CARDS ===================== --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="fw-bold text-black">Total Sales</h6>
                            <div class="h4 fw-bold text-success">
                                Rp. {{ $summary['totalSales'] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="fw-bold text-black">Total Returns</h6>
                            <div class="h4 fw-bold text-danger">
                                Rp. {{ $summary['totalReturns'] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="fw-bold text-black">Return Rate</h6>
                            <div class="h4 fw-bold text-secondary">
                                {{ number_format($summary['returnRate'], 2) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===================== ROW 2: Sales Qty, Return Qty, (Optional) ===================== -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Sales Qty (In KAR)</h6>
                            <div class="h4 fw-bold text-success">
                                {{ number_format($summary['qtyTotalSales'] ?? 0, 0) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Return Qty (In KAR)</h6>
                            <div class="h4 fw-bold text-danger">
                                {{ number_format($summary['qtyTotalReturns'] ?? 0, 0) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-4 text-center">
                            <h6 class="text-black fw-bold">Qty Return Rate</h6>
                            <div class="h4 fw-bold text-secondary">
                                {{ number_format(($summary['qtyReturnRate'] ?? 0), 2) }}%
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== TOP 10 CHART ===================== --}}
                <div class="card shadow-sm p-3 mb-4">
                    <h6 class="fw-bold text-primary mb-2">
                        🏆 Top 10 {{ $dimensionConfig['tableColumns']['label'] }} by {{ $filters['sortBy'] }} ({{ $filters['orderBy'] }})
                    </h6>
                    <canvas id="topDimensionChart" style="height: 280px !important;"></canvas>
                </div>

                {{-- ===================== TABLE ===================== --}}
                <div class="row g-4 mt-2">
                    <div class="scroll-table">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ $dimensionConfig['tableColumns']['label'] }}</th>
                                    <th>{{ $dimensionConfig['tableColumns']['sales'] }}</th>
                                    <th>{{ $dimensionConfig['tableColumns']['returns'] }}</th>
                                    <th>{{ $dimensionConfig['tableColumns']['rate'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($headerData as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.return.dashboard.detail.index', ['dimension' => $dimensionConfig['dimension']]) }}?code={{ $row['DimensionCode'] }}&{{ $filterQuery }}" target="_blank">
                                            {{ $row['DimensionName'] }}
                                        </a>
                                    </td>
                                    <td class="text-end">{{ number_format($row['TotalSales']) }}</td>
                                    <td class="text-end">{{ number_format($row['TotalReturns']) }}</td>
                                    <td>{{ number_format($row['ReturnRate'], 2) }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        {{ $headerData->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>
<script src="{{ asset('assets/js/pages/sales-dashboard/clustered-chart.js') }}"></script>

<script src="{{ asset('assets/js/pages/sales-dashboard/sales-return-dashboard.js') }}"></script>

<script>
    clusteredChart(
        "topDimensionChart",
        @json($chart['labels']),
        @json($chart['sales']),
        @json($chart['returns'])
    );
</script>
@endpush