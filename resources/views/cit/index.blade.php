@extends('layouts.app')
@section('titlepage', 'CIT Dashboard')

@section('content')
@section('navigasi')
<span>CIT Dashboard</span>
@endsection

@php
$asset_dashboard = asset('assets/img/icons/dashboard/');

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


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/cit/dashboard.css')}}">


<div class="row row_container mb-3">
    <div class="col-12 col_nav">
        <div class="nav_container">
            <div class="nav_tab">
                <div class="tab_el tab_indicator" data-target="page_summary_ar"> Summary AR </div>
                <div class="tab_el tab_indicator" data-target="page_data_teritory"> Teritory </div>
                <div class="tab_el tab_indicator" data-target="page_data_driversales"> Driver/Sales </div>
                <div class="tab_el tab_indicator" data-target="page_data_customer"> Customer </div>
            </div>
        </div>
        <div class="nav_container">
            {{-- Filter --}}
            <form action="{{ route('cit.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 mb-4">

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

                {{-- BRANCH FILTER --}}
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
        </div>
    </div>
</div>

<div class="row row_container row_main">   


    {{-- Col_container_data - Summary AR --}}
    <div class="col-12 col_container_data type_data" id="page_summary_ar">

        @include( 'cit.cit_index.page_summary' )

    </div>
    {{-- End Of Col_container_data - Summary AR --}}

    {{-- Col_container_data - page_data_teritory --}}
    <div class="col-12 col_container_data type_data" id="page_data_teritory">

        @include( 'cit.cit_index.page_data_teritory' )

    </div>
    {{-- End Of Col_container_data - page_data_teritory --}}

    {{-- Col_container_data - page_data_driversales --}}
    <div class="col-12 col_container_data type_data" id="page_data_driversales">

        @include( 'cit.cit_index.page_data_driversales' )

    </div>
    {{-- End Of Col_container_data - page_data_driversales --}}

    {{-- Col_container_data - page_data_customer --}}
    <div class="col-12 col_container_data type_data" id="page_data_customer">

        @include( 'cit.cit_index.page_data_customer' )

    </div>
    {{-- End Of Col_container_data - page_data_customer --}}



</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/pages/cit_dashboard/main.js') }}"></script>
<script>
    setTimeout(function(){
      new WOW().init();
  }, 1000)
</script>


@endsection



@push('myscript')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>

{{-- Script Bar Yang Saling Ketergantungan --}}
{{-- Init Bar --}}
<script src="{{ asset('assets/js/pages/cit_dashboard/generalBarChart.js') }}"></script>
{{-- Custom Bar --}}
<script src="{{ asset('assets/js/pages/cit_dashboard/chartDashboard.js') }}"></script>
<script src="{{ asset('assets/js/pages/cit_dashboard/stackbar_chart.js') }}"></script>
{{-- End Of Script Bar Yang Saling Ketergantungan --}}



<script>

    Chart.register(ChartDataLabels);

    window.citDashboardData = {
        top_branches: @json( $result_territory_TOP ),
        cod_branches: @json( $result_territory_COD ),
        top_drivers: @json( $result_drivers_TOP ),
        cod_drivers: @json( $result_drivers_COD ),
        top_customer: @json( $result_customer_TOP ),
        cod_customer: @json( $result_customer_COD ),
    }

    const {
        top_branches, 
        cod_branches, 
        top_drivers, 
        cod_drivers, 
        top_customer, 
        cod_customer, 
    } = window.citDashboardData;


    var build_stackbar_section = (  TOP, COD, judul_x = "JUDUL" ) =>{

        {{-- TOP Branches --}}
        buildStackbarByClass({
            className: TOP.selector,
            datasets: TOP.datasets,
            ordertype: 'TOP',
            horizontal: false,
            xLabel: judul_x,
            yLabel: ''
        });
        {{-- COD Branches --}}
        buildStackbarByClass({
            className: COD.selector,
            datasets: COD.datasets,
            ordertype: 'COD',
            horizontal: false,
            xLabel: judul_x,
            yLabel: ''
        });

    }


    build_stackbar_section( 
    {

        selector: "chart_top_branches",
        datasets: top_branches

    },


    {

        selector: "chart_cod_branches",
        datasets: cod_branches

    },


    "BRANCHES"

    );



    build_stackbar_section( 
    {

        selector: "chart_top_drivers",
        datasets: top_drivers

    },

    {

        selector: "chart_cod_drivers",
        datasets: cod_drivers

    },
    "DRIVERS SALES"


    );


    build_stackbar_section( 
    {

        selector: "chart_top_customer",
        datasets: top_customer

    },

    {

        selector: "chart_cod_customer",
        datasets: cod_customer

    },
    "CUSTOMER"


    );



</script>



@endpush