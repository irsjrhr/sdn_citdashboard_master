@extends('layouts.app')
@section('titlepage', 'POD Return Summary')

@section('content')
@section('navigasi')
<div class="d-flex flex-column">
    <span>POD Return Dashboard</span>

    @isset($lastUpdatedDate)
        <span class="text-muted small">
            Last Updated: {{ \Carbon\Carbon::parse($lastUpdatedDate)->format('d M Y H:i') }}
        </span>
    @endisset
</div>
@endsection

<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr); /* 5 columns */
        gap: 15px;
    }

    .kpi-card {
        border-radius: 12px;
        background: white;
        padding: 18px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 15px;
    }
    .kpi-title { font-size: 0.9rem; font-weight: 600; color: #555; }
    .kpi-value { font-size: 1.6rem; font-weight: 700; color: #0d6efd; }

    /* Subtle variants */
    .kpi--blue {
        background: #f3f7ff;
        border-left-color: #4c6ef5;
    }

    .kpi--green {
        background: #f3fbf6;
        border-left-color: #2f9e44;
    }

    .kpi--orange {
        background: #fff6ec;
        border-left-color: #f59f00;
    }

    .kpi--red {
        background: #fff5f5;
        border-left-color: #e03131;
    }

    /* Responsive fallback */
    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                {{-- ====================== FILTER BAR ====================== --}}
                <form action="{{ route('dashboard.tms.podreturn.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 mb-4">
                    {{-- Start Date --}}
                    <div>
                        <label class="small text-muted fw-bold">Start Date</label>
                        <input type="date" name="startDate" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="small text-muted fw-bold">End Date</label>
                        <input type="date" name="endDate" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm">
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

                {{-- ====================== SUMMARY KPI CARDS ====================== --}}
                <h4 class="fw-bold mb-3">Summary</h4>

                <div class="kpi-grid">
                    {{-- ====================== ORDER ====================== --}}
                    <div class="kpi-card kpi--blue">
                        <div class="kpi-title">Total Orders</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Order']) }}</div>
                    </div>

                    <div class="kpi-card kpi--blue">
                        <div class="kpi-title">Total Amount</div>
                        <div class="kpi-value">{{ formatAbbreviatedNumber($summaryData['Total Amount']) }}</div>
                    </div>

                    {{-- ====================== SHIPPED ====================== --}}
                    <div class="kpi-card kpi--green">
                        <div class="kpi-title">Total Shipped Order</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Shipped Order']) }}</div>
                    </div>

                    <div class="kpi-card kpi--green">
                        <div class="kpi-title">Total Shipped Amount</div>
                        <div class="kpi-value">{{ formatAbbreviatedNumber($summaryData['Total Shipped Amount']) }}</div>
                    </div>

                    <div class="kpi-card kpi--green">
                        <div class="kpi-title">% Plan Order</div>
                        <div class="kpi-value">{{ number_format($summaryData['% Plan Order'], 2) }}%</div>
                    </div>

                    {{-- ====================== REJECTED (GR) ====================== --}}
                    <div class="kpi-card kpi--red">
                        <div class="kpi-title">Rejected Orders</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejected Shipped Order']) }}</div>
                    </div>

                    <div class="kpi-card kpi--red">
                        <div class="kpi-title">Rejected Amount</div>
                        <div class="kpi-value">{{ formatAbbreviatedNumber($summaryData['Total Rejected Shipped Amount']) }}</div>
                    </div>

                    <div class="kpi-card kpi--red">
                        <div class="kpi-title">Rejected Quantity</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejected Quantity']) }}</div>
                    </div>

                    <div class="kpi-card kpi--red">
                        <div class="kpi-title">Rejected Outlet</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejection Outlet']) }}</div>
                    </div>

                    <div class="kpi-card kpi--red">
                        <div class="kpi-title">% Rejection Orders</div>
                        <div class="kpi-value">{{ number_format($summaryData['% Rejection Orders'], 2) }}%</div>
                    </div>

                    {{-- ====================== NON GR ====================== --}}
                    <div class="kpi-card kpi--orange">
                        <div class="kpi-title">Rejected Orders (Non GR)</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejection Order (Non GR)']) }}</div>
                    </div>

                    <div class="kpi-card kpi--orange">
                        <div class="kpi-title">Rejected Amount (Non GR)</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejection Amount (Non GR)']) }}</div>
                    </div>

                    <div class="kpi-card kpi--orange">
                        <div class="kpi-title">Rejected Quantity (Non GR)</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejection Quantity (Non GR)']) }}</div>
                    </div>

                    <div class="kpi-card kpi--orange">
                        <div class="kpi-title">Rejected Outlet (Non GR)</div>
                        <div class="kpi-value">{{ number_format($summaryData['Total Rejection Outlet (Non GR)']) }}</div>
                    </div>

                    <div class="kpi-card kpi--orange">
                        <div class="kpi-title">% Rejection (Non GR)</div>
                        <div class="kpi-value">{{ number_format($summaryData['% Rejection (Non GR)'], 2) }}%</div>
                    </div>
                </div>

                {{-- ====================== Rejection Count By Dist Channel ====================== --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Rejections by Distribution Channels</h6>
                            </div>
                            <canvas id="distributionChannelChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>

                    {{-- ====================== Rejection Count By Reasons ====================== --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Rejection Reasons</h6>
                                <a href="{{ Route('dashboard.tms.podreturn.reasondetail.index') }}" target="_blank" class="text-primary small text-decoration-underline">
                                    See details
                                </a>
                            </div>
                            <canvas id="reasonChart" style="height: 300px !important; max-height: 300px;"></canvas>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Rejections by Branches</h6>
                            </div>
                            @if ($branchData->count() > 0)
                                @php
                                    $firstRow = $branchData->first();
                                    $columns = $firstRow ? array_keys((array) $firstRow) : [];
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                @foreach ($columns as $col)
                                                    <th class="text-nowrap">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($branchData as $row)
                                                <tr>
                                                    @foreach ($columns as $col)
                                                        <td class="text-nowrap">
                                                             {{ is_numeric($row[$col]) ? number_format($row[$col]) : ($row[$col] ?? '-') }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $branchData->links() }}
                            @else
                                <div class="text-muted text-center py-4">
                                    No data available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.distributionChannelData = @json($channelData);
    window.reasonChartData = @json($reasonData);
</script>


<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('assets/js/charts/donut-chart.js') }}"></script>
<script src="{{ asset('assets/js/charts/bar-chart.js') }}"></script>
<script src="{{ asset('assets/js/pages/pod/pod-return-dashboard.js') }}"></script>

@endpush
