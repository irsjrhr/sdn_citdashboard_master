@extends('layouts.app')
@section('titlepage', $config['title'])

@section('content')
@section('navigasi')
<span>
    <a href="{{ route('sales.return.dashboard.index') }}" 
       class="text-decoration-none fw-bold text-primary">
        National
    </a>

    <span class="mx-1 text-secondary">&gt;</span>

    <a href="{{ url()->previous() }}"
       class="text-decoration-none fw-bold text-primary">
        {{ ucfirst($filters['dimension']) }}
    </a>

    <span class="mx-1 text-secondary">&gt;</span>

    <span class="fw-bold">{{ $filters['code'] }}</span>
</span>
@endsection

@php
    $singular = [
        'branches' => 'branch',
        'salesmen' => 'salesmen',
        'stores'   => 'store',
        'skus'     => 'sku',
        'principals' => 'principal',
        'regions'   => 'region',
        'businessTypes' => 'businessType',
        'distributionChannels' => 'distributionChannel',
    ];
@endphp

<style>
    .scroll-table { max-height: 550px; overflow-y: auto; border: 1px solid #bbb; border-radius: 6px; }
    .scroll-table table thead th { position: sticky; top: 0; background: #d1d5db !important; color: #111; }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                {{-- ===================== APPLIED FILTERS ===================== --}}
                @if(collect($filters)->filter()->count() > 0)
                    <div class="p-3 mb-4 rounded"
                        style="background:#e8f1ff; border:1px solid #bcd4ff;">

                        <h6 class="fw-bold mb-2" style="color:#0d6efd;">
                            🔎 Applied Filters
                        </h6>

                        <div class="d-flex flex-wrap gap-2">
                            @php
                                // keys you DON'T want to show
                                $exclude = [
                                    'branchesPage',
                                    'storesPage',
                                    'salesmenPage',
                                    'skusPage',
                                    'pageSize'
                                ];

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
                                    'dimension'           => 'Dimension'
                                ];
                            @endphp

                            @foreach($filters as $key => $value)
                                @if(!empty($value) && !in_array($key, $exclude))
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

                {{-- ===================== OVERVIEW ===================== --}}
                <div class="card shadow-sm border border-primary mt-4 mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">📊 {{ $config['label'] }} Overview</h4>

                        <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                            <span class="fw-bold">{{ $config['label'] }}</span>
                            <span class="fw-bold">{{ $detailData['overview']['DimensionName'] }}</span>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                            <span class="fw-bold">Total Sales</span>
                            <span class="fw-bold text-success">
                                {{ number_format($detailData['overview']['TotalSales']) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="fw-bold">Total Returns</span>
                            <span class="fw-bold text-danger">
                                {{ number_format($detailData['overview']['TotalReturns']) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ===================== TREND ===================== --}}
                <div class="card shadow-sm p-3 mb-4">
                    <h6 class="fw-bold text-primary mb-3">📈 YTD Monthly Trend</h6>
                    <canvas id="trendChart" style="height: 300px"></canvas>
                </div>

                {{-- ===================== SUB TABLES ===================== --}}
                <div class="row g-4">

                    @foreach ($config['childDimensions'] as $child)
                        @php
                            // fallback: if key doesn't exist, use raw child
                            $dimension = $singular[$child] ?? $child;

                            $childRows = $detailData[$child] ?? [];
                            $title = ucfirst($child);
                            $codeFieldMap = [
                                'branches' => 'BranchCode',
                                'stores'   => 'StoreCode',
                                'salesmen' => 'SalesmanCode',
                                'skus'     => 'SKUCode',
                            ];

                            $nameFieldMap = [
                                'branches' => 'BranchName',
                                'stores'   => 'StoreName',
                                'salesmen' => 'SalesmanName',
                                'skus'     => 'SKUName',
                            ];

                            $codeField = $codeFieldMap[$child] ?? 'DimensionCode';
                            $nameField = $nameFieldMap[$child] ?? 'DimensionName';
                        @endphp

                        <div class="col-md-6">
                            <h6 class="fw-bold text-secondary mb-2">{{ $title }}</h6>

                            <div class="scroll-table">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ $title }}</th>
                                            <th>Sales</th>
                                            <th>Returns</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($childRows as $row)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sales.return.dashboard.detail.index', $dimension) }}?code={{ urlencode($row[$codeField]) }}" target="_blank">
                                                    {{ !empty($row['BranchCode']) ? $row['BranchCode'] . ' - ' : '' }}{{ $row[$nameField] }}
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
                                {{ $childRows->links() }}
                            </div>
                        </div>
                    @endforeach
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
<script src="{{ asset('assets/js/pages/sales-dashboard/trend-chart.js') }}"></script>

<script>
     document.addEventListener("DOMContentLoaded", () => {
        const trendData = @json($detailData['trend'] ?? []);

        if (window.trendChart && trendData.length > 0) {
            window.trendChart("trendChart", trendData);
        }
    });

</script>

</script>
@endpush
