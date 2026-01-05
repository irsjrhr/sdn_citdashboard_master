@extends('layouts.app')
@section('titlepage', 'POD Return Reason Detail')

@section('content')
@section('navigasi')
<div class="d-flex flex-column">
    <span>POD Return Reason Analysis</span>

    @isset($lastUpdatedDate)
        <span class="text-muted small">
            Last Updated: {{ \Carbon\Carbon::parse($lastUpdatedDate)->format('d M Y H:i') }}
        </span>
    @endisset
</div>
@endsection

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                {{-- ====== Visualization ====== --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 5 Rejection Reasons by Salesman</h6>
                            </div>
                            <canvas id="salesmanChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 5 Rejection Reasons by Driver</h6>
                            </div>
                            <canvas id="driverChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 5 Rejection Reasons by Channel by Branch</h6>
                            </div>
                            <canvas id="channelChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 5 Rejection Reasons by Principal</h6>
                            </div>
                            <canvas id="principalChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 small">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Top 5 Rejection Reasons by SKU</h6>
                            </div>
                            <canvas id="skuChart"></canvas>
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
    window.datasets = {
        salesman: @json($top5ReasonBySalesman),
        driver: @json($top5ReasonByDriver),
        principal: @json($rejectionByPrincipal),
        channel: @json($top5ReasonByDistChannelByBranch),
        sku: @json($top5ReasonBySKU)
    };
</script>

<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('assets/js/charts/donut-chart.js') }}"></script>
<script src="{{ asset('assets/js/charts/bar-chart.js') }}"></script>
<!-- <script src="{{ asset('assets/js/charts/stacked-bar-chart.js') }}"></script> -->
<script src="{{ asset('assets/js/charts/stacked-bar2-chart.js') }}"></script>
<script src="{{ asset('assets/js/pages/pod/pod-return-reason-dashboard.js') }}"></script>

@endpush
