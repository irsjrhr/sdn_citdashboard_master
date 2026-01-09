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
<style>
    .chart_container_scroll canvas {
        min-height: 500px !important;
        height: auto !important;
    }

    .chart_container_scroll {
        min-height: 550px;
        position: relative;
    }
</style>


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

<div class="modal fade" id="modal_detail_stackbar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        {{-- container detail stackbar - successRateCollectionBranch_all --}}
                        <div class="col-12 container_detail_stackbar active" id="successRateCollectionBranch_all">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Cabang / Territory</h6>
                                <p class="mb-0">
                                    <span id="detail_label">CIANJUR - COD</span>
                                </p>
                                <small class="text-muted">
                                    Territory: <span id="detail_territory">CIANJUR</span> |
                                    Order Type: <span id="detail_ordertype">COD</span>
                                </small>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Collected Rate</h6>
                                        <h3 class="mb-0"><span id="detail_collected_rate">94.18%</span></h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-danger fw-bold">Uncollected Rate</h6>
                                        <h3 class="mb-0"><span id="detail_uncollected_rate">5.82%</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar">Rp 1.146.475.063</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount">Rp 983.308.553</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount">Rp 66.700.251</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount">Rp 1.079.774.812</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-danger">
                                                    - Rp 117.106.685
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>



                        </div>

                        {{-- end of container detail stackbar - successRateCollectionBranch_all --}}

                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
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

    function open_modal_detailStackbar( callback = false ){
        if( callback == false ){
            callback = function(){
                return 1;
            }
        }
        const modalEl = document.getElementById('modal_detail_stackbar');
        const modal = new bootstrap.Modal(modalEl);

        callback( modalEl );

        //Memunculkan modal
        modal.show();
    }



    //Data Success Rate Collection Branch 

    $(document).ready(function(){

        open_modal_detailStackbar(function( modal_el ){ 

            modal_el = $( modal_el );

            //Membuka container detail berdasarkan dataset yang di klik pada bar
            var id_container_detail_stackbar = "#successRateCollectionOverdueBranch_all";
            var container_detail_stackbar = modal_el.find('.container_detail_stackbar');
            var container_detail_stackbarTarget = container_detail_stackbar.filter(id_container_detail_stackbar);

            alert( "Membuka modal_detailStackbar untuk container_detail_stackbar dengan id " + id_container_detail_stackbar);

            //Menutup semua container detail stackbar
            container_detail_stackbar.removeClass('active');
            //Membuka container detail stacbar target
            container_detail_stackbarTarget.addClass('active');

        });


        var data_successCollect_branchAll = @json( $summary_successCollect_branch['result_all'] ); //[ {},{},{} ]
        console.log(data_successCollect_branchAll);
        buildStackedBarChart({
            el: document.getElementById('chart_successRateCollectionBranch_all'),
            data: data_successCollect_branchAll,
            config: {
                stacks: [
                    {
                        key: 'collection_rate_pct',
                        label: 'Collected Rate ( % )',
                        backgroundColor: '#4CAF50'
                    },
                    {
                        key: 'uncollected_rate_pct',
                        label: 'Uncollected Rate ( %  )',
                        backgroundColor: '#F44336'
                    }
                ],
                heightChart : 300,
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    open_modal_detailStackbar(function( modal_el ){
                        //Akan membuka modal detail stackbar yang menampilkan detail row data


                    });
                }
            }
        });



        //Data Success Rate Collection Overdue Branch 
        var data_successCollectOverdue_branchAll = @json( $summary_successCollectOverdue_branch['result_all'] ); //[ {},{},{} ]
        buildStackedBarChart({
            el: document.getElementById('chart_successRateCollectionOverdueBranch_all'),
            data: data_successCollectOverdue_branchAll,
            config: {
                stacks: [
                    {
                        key: 'overdue_collection_rate_pct',
                        label: 'Collected Amount ( % )',
                        backgroundColor: '#4CAF50'
                    },
                    {
                        key: 'overdue_uncollected_rate_pct',
                        label: 'Uncollected Amount ( %  )',
                        backgroundColor: '#F44336'
                    }
                ],
                heightChart : 300
            }
        });



        //Data Bad Collection DriverSales TOP Salesman
        var data_badCollectionDriverTOP = @json( $summary_badCollectionDriver['result_TOP'] ); //[ {},{},{} ]

        console.log("+++++++++++++");
        console.log( data_badCollectionDriverTOP );
        buildStackedBarChart({
            el: document.getElementById('chart_badCollectionSalesDriver_top'),
            data: data_badCollectionDriverTOP,
            config: {
                stacks: [
                    {
                        key: 'confirmed_amount',
                        label: 'Collected Amount ( Rp )',
                        backgroundColor: '#4CAF50'
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: '#F44336'
                    }
                ],
                heightChart : 300,
            }
        });

        //Data Bad Collection DriverSales COD Driver 
        var data_badCollectionDriverCOD = @json( $summary_badCollectionDriver['result_COD'] ) //[ {},{},{} ]
        buildStackedBarChart({
            el: document.getElementById('chart_badCollectSalesDriver_cod'),
            data: data_badCollectionDriverCOD,
            config: {
                stacks: [
                    {
                        key: 'confirmed_amount',
                        label: 'Collected Amount ( Rp )',
                        backgroundColor: '#4CAF50'
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: '#F44336'
                    }
                ],
                heightChart : 300
            }
        });

        //Data Bad Collection Customer
        var data_badCollectionCustomer = @json( $summary_badCollectionCustomer['result_all'] ); //[ {},{},{} ]
        buildStackedBarChart({
            el: document.getElementById('chart_badCollectCustomer'),
            data: data_badCollectionCustomer,
            config: {
                stacks: [
                    {
                        key: 'confirmed_amount',
                        label: 'Collected Amount ( Rp )',
                        backgroundColor: '#4CAF50'
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: '#F44336'
                    }
                ],
                heightChart : 300
            }
        });

    });






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