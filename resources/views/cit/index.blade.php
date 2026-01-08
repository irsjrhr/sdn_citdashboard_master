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



    //Ubah array associatif multi dimensi yang isinya array index menjadi object yang isinya object 


    //Data Success Rate Collection Branch 
    buildStackbarByClass({
        className: 'chart_successRateCollectionBranch_all',
        datasets: @json( $summary_successCollect_branch['result_all'] ),
        seriesConfig: [
            {
                label: 'Collected Rate ( % )',
                key: 'collection_rate_pct',
                color: 'rgba(34,197,94,0.7)'
            },
            {
                label: 'Uncollected Rate ( % )',
                key: 'uncollected_rate_pct',
                color: 'rgba(239,68,68,0.7)'
            }
        ],
        xLabel: 'Branch',
        yLabel: 'Rate Percentage ( % )'
    });


    //Data Success Rate Collection Overdue Branch 
    buildStackbarByClass({
        className: 'chart_successRateCollectionOverdueBranch_all',
        datasets: @json( $summary_successCollectOverdue_branch['result_all'] ),
        seriesConfig: [
            {
                label: 'Collected Rate ( % )',
                key: 'overdue_collection_rate_pct',
                color: 'rgba(34,197,94,0.7)'
            },
            {
                label: 'Uncollected Rate ( % )',
                key: 'overdue_uncollected_rate_pct',
                color: 'rgba(239,68,68,0.7)'
            }
        ],
        xLabel: 'Branch',
        yLabel: 'Rate Percentage ( % )'
    });


    //Data Bad Collection DriverSales TOP 
    buildStackbarByClass({
        className: 'chart_badCollectionDriver_top',
        datasets: @json( $summary_badCollectionDriver['result_TOP'] ),
        seriesConfig: [
            {
                label: 'Collected Amount ( Rp )',
                key: 'confirmed_amount',
                color: 'rgba(34,197,94,0.7)'
            },
            {
                label: 'Uncollected Amount ( Rp )',
                key: 'unconfirmed_amount',
                color: 'rgba(239,68,68,0.7)'
            }
        ],
        xLabel: 'Salesman',
        yLabel: 'Amount ( Rp )'
    });

    //Data Bad Collection DriverSales COD 
    buildStackbarByClass({
        className: 'chart_badCollectSalesDriver_cod',
        datasets: @json( $summary_badCollectionDriver['result_COD'] ),
        seriesConfig: [
            {
                label: 'Collected Amount ( Rp )',
                key: 'confirmed_amount',
                color: 'rgba(34,197,94,0.7)'
            },
            {
                label: 'Uncollected Amount ( Rp )',
                key: 'unconfirmed_amount',
                color: 'rgba(239,68,68,0.7)'
            }
        ],
        xLabel: 'Driver',
        yLabel: 'Amount ( Rp )'
    });


    //Data Bad Collection Customer 
    buildStackbarByClass({
        className: 'chart_badCollectCustomer',
        datasets: @json( $summary_badCollectionCustomer['result_all'] ),
        seriesConfig: [
            {
                label: 'Collected Amount ( Rp )',
                key: 'confirmed_amount',
                color: 'rgba(34,197,94,0.7)'
            },
            {
                label: 'Uncollected Amount ( Rp )',
                key: 'unconfirmed_amount',
                color: 'rgba(239,68,68,0.7)'
            }
        ],
        xLabel: 'Salesman',
        yLabel: 'Amount ( Rp )'
    });







    {{-- chart_badCollectSalesDriver_cod --}}



</script>



@endpush



{{-- Chart.register(ChartDataLabels); --}}
{{--     window.citDashboardData = {
        top_branches: @json( $result_territory_TOP ),
        cod_branches: @json( $result_territory_COD ),
        all_branches: @json( $result_territory_all ),
        top_drivers: @json( $result_drivers_TOP ),
        cod_drivers: @json( $result_drivers_COD ),
        top_customer: @json( $result_customer_TOP ),
        cod_customer: @json( $result_customer_COD ),
    }


    var {
        top_branches, 
        cod_branches, 
        all_branches, 
        top_drivers, 
        cod_drivers, 
        top_customer, 
        cod_customer, 
    } = window.citDashboardData;
    --}}



 {{--     var build_stackbar_section = ( DATA, judul_x = "JUDUL" ) => {

        buildStackbarByClass({
            className: DATA.selector,
            datasets: DATA.datasets,
            xLabel: 'Bulan',
            yLabel: 'Jumlah (Rp)',
            scrollable: true,
            horizontal: false,
            maxWidth: '1200px',
            maxHeight: '800px',
            barWidth: 100,      // Lebar bar
            spacing: 60        // Jarak antar bar - BESAR BANGET!
        });
    }
    --}}


{{--     buildStackbarByClass({
        className: 'chart-cit',
        datasets: dataFromAPI,
        seriesConfig: [
        {
            label: 'Collected',
            key: 'confirmed_amount',
            color: 'rgba(34,197,94,0.7)'
        },
        {
            label: 'Uncollected',
            key: 'unconfirmed_amount',
            color: 'rgba(239,68,68,0.7)'
        }
        ],
        xLabel: 'Branch',
        yLabel: 'Amount (Rp)'
    });



    build_stackbar_section({
        selector:"chart_successRateCollectionBranch_all",
        datasets : data_successRateCollectionBranch_all
    }, "BRANCHES");


    build_stackbar_section({
        selector:"chart_successRateCollectionOverdueBranch_all",
        datasets : data_successRateCollectionOverdueBranch_all
    }, "BRANCHES");
 --}}