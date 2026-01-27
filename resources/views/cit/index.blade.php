@extends('layouts.app')
@section('titlepage', 'CIT Dashboard' )

@section('content')
@section('navigasi')
<span>CIT Dashboard</span>
@isset($last_update)
<br>
<span class="text-muted small">
    Last Updated: {{ \Carbon\Carbon::parse($last_update)->format('d M Y H:i') }}
</span>
@endisset

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
    option.option_branch{
        display: none;
    }
    .option_branch.active{
        display : block;
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

                {{-- REGION FILTER --}}
                <div>
                    <label class="small text-muted fw-bold">Region</label>
                    <select name="region" class="form-control form-control-sm">
                        <option class="option_region_all" value="" autosave>All</option>
                        @foreach ($regions as $r)
                        <option class="option_region" value="{{ $r->region }}">
                            {{ $r->region }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- BRANCH FILTER --}}
                <div>
                    <label class="small text-muted fw-bold">Branch</label>
                    <select name="branch" class="form-control form-control-sm" autosave>
                        <option class="option_branch_all" value="">All</option>
                        @foreach ($branches as $b)
                        <option class="option_branch" data-region="{{$b->region}}" value="{{ $b->territory_code }}"
                            {{ request('branch') == $b->territory_code ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                        @endforeach
                    </select>
                </div>


                {{-- BUSINESS TYPE FILTER --}}
                <div>
                    <label class="small text-muted fw-bold">Order Type</label>
                    <select name="orderType" class="form-control form-control-sm" autosave>
                        <option value="">All</option>
                        <option value="TOP" {{ request('orderType') == "TOP" ? 'selected' : '' }}>TOP</option>
                        <option value="COD" {{ request('orderType') == "COD" ? 'selected' : '' }}>COD</option>
                    </select>
                </div>




                {{-- ORDER TYPE FILTER --}}
                <div>
                    <label class="small text-muted fw-bold">Business Type</label>
                    <select name="businessType" class="form-control form-control-sm" autosave>
                        <option value="">All</option>
                        <option value="MIX" {{ request('businessType') == "MIX" ? 'selected' : '' }}>MIX</option>
                        <option value="JTI" {{ request('businessType') == "JTI" ? 'selected' : '' }}>JTI</option>
                        <option value="ULI" {{ request('businessType') == "ULI" ? 'selected' : '' }}>ULI</option>
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


@include('cit.cit_index.modal_detail_chart');














<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/pages/cit_dashboard/main.js') }}"></script>
<script>
    setTimeout(function(){
      new WOW().init();
  }, 1000)




    @php
    if ( isset($_GET['region']) ) {
        $region_default = $_GET['region'];
    }else{

        $region_default = "All";
    }
    @endphp

        //Filter Metric 
    $(document).ready(function(){

        //+++ Default Filter Region dan Branch

        var region_default = "<?= $region_default ?>";

        //Memilih option pada select region berdasarkan region default
        var select_region = $('select[name=region]');
        var option_region = select_region.find('option');
        var option_region_target = option_region.filter('[value="'+region_default+'"]');
        option_region_target.prop('selected', true);

        //Membuka default list branch berdasarkan region dari request atau region_defaultnya 
        render_branch_byRegion( region_default );


        //+++ Event ketika region dipilih branch berdasarkan region yang dipilih atau semua branch
        $('select[name=region]').on('change', function(){

            var option_region_target = $(this);
            var region_target = option_region_target.val();

            //Cek apakah option region yang dipilih all atau bukan, kalo iya maka jadikan nilai render argumentnya jadi All agar semua branch ditampilkan

            var option_region_all = $('.option_region_all');
            if( option_region_all.is(':selected') ){
                render_branch_byRegion( "All" );
            }else{
                render_branch_byRegion( region_target );
            }

            //Pada input select branch, selalu pilih nilainya di all agar menjadi nilai awal 
            $('.option_branch_all').prop('selected', true);

        });

        {{-- render_branch_byRegion("Region 3                             "); --}}

    });


    //Method memunculkan option branch berdasarkan region 
    function render_branch_byRegion( region = "All", callback = false ){

        if( callback == false ){
            callback = function(){
                return 1;
            }
        }


        var option_branch = $('.option_branch');


        //Tampilkan semua branch atau branch sesuai dengan region
        if( region == "All" ){
            console.log( "Menampilkan semua elemen option_branch");


            //Jika yang ditampilkan semua branch
            option_branch.addClass('active');

        }else {
            //Jika yang ditampilkan branch berdasarkan region
            console.log( "Menampilkan elemen option_branch hanya yang memiliki data-region " + region);


            var option_branch_byRegion = option_branch.filter('[data-region="'+region+'"]');
            console.log( "Elemen option_branch pada region "+ region + "ada sebanyak :" + option_branch_byRegion.length );
            //Hilangkan semua branch
            option_branch.removeClass('active');
             //Tampilkan branch hanya yang punya data-region dari filter region yang dipilih
            option_branch_byRegion.addClass('active');
        }



        callback();
    }


</script>


@endsection



@push('myscript')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>
<script src="{{ asset('assets/js/pages/cit_dashboard/chart_main.js') }}"></script>
{{-- End Of Script Bar Yang Saling Ketergantungan --}}



<script>

    function open_modal_detailChart( id_container_detail_chart, callback = false ){

        //Membuka container detail berdasarkan dataset yang di klik pada bar di modal_detail_chart


        if( callback == false ){
            callback = function(){
                return 1;
            }
        }
        const modalEl = document.getElementById('modal_detail_chart');
        const modal = new bootstrap.Modal(modalEl);


        //Membuka container detail
        var modal_el = $( modalEl );

        var container_detail_chart = modal_el.find('.container_detail_chart');
        var container_detail_chartTarget = container_detail_chart.filter(id_container_detail_chart);

        console.log( "+++ Membuka modal_detailchart untuk container_detail_chart dengan id " + id_container_detail_chart + "+++++++++");

        //Menutup semua container detail chart 
        container_detail_chart.removeClass('active');
        //Membuka container detail stacbar target
        container_detail_chartTarget.addClass('active');

        callback( modal_el, container_detail_chart );

        //Memunculkan modal
        modal.show();
    }
    //=============== METHOD KETIKA MODAL DETAIL CHART TERBUKA DAN MENGIMPLEMENTASIKAN ROW DATA DARI CALLBACK CHART ==========
    function renderDetail_successRateCollectionBranch(row_data) {


        const container_detail_chartTarget = $('.container_detail_chart#successRateCollectionBranch_all');

        // IDENTITAS
        container_detail_chartTarget.find('#detail_label').text(row_data.label);
        container_detail_chartTarget.find('#detail_territory').text(row_data.territoryname);
        container_detail_chartTarget.find('#detail_ordertype').text(row_data.ordertype);

        // RATE
        container_detail_chartTarget.find('#detail_collected_rate').text(
            formatPercent(row_data.collection_rate_pct)
            );

        container_detail_chartTarget.find('#detail_uncollected_rate').text(
            formatPercent(row_data.uncollected_rate_pct)
            );

        // FINANCIAL
        container_detail_chartTarget.find('#detail_total_ar').text(
            formatRupiah(row_data.total_ar)
            );

        container_detail_chartTarget.find('#detail_collected_amount').text(
            formatRupiah(row_data.collected_amount)
            );

        container_detail_chartTarget.find('#detail_unconfirmed_amount').text(
            formatRupiah(row_data.unconfirmed_amount)
            );

        container_detail_chartTarget.find('#detail_confirmed_amount').text(
            formatRupiah(row_data.confirmed_amount)
            );

        // TOTAL DIFFERENCE (auto warna)
        const diffVal = Number(row_data.total_difference || 0);
        const diffEl = container_detail_chartTarget.find('#detail_difference');


        //Menambahkan efek class warna berdasarkan nilai. Jika minus maka akan class warna merah, kalo tidak minus jadi hijau
        diffEl
        .text(formatRupiah(diffVal))
        .removeClass('text-danger text-success')
        .addClass(diffVal < 0 ? 'text-danger' : 'text-success');
    }
    function renderDetail_successRateCollectionOverdueBranch(row_data) {

        const container_detail_chartTarget = $('.container_detail_chart#successCollectOverdue_branchAll');


        // IDENTITAS
        container_detail_chartTarget.find('#detail_overdue_label').text(row_data.label);
        container_detail_chartTarget.find('#detail_overdue_territory').text(row_data.territoryname);

        // RATE
        container_detail_chartTarget.find('#detail_overdue_collected_rate').text(
            formatPercent(row_data.overdue_collection_rate_pct)
            );

        container_detail_chartTarget.find('#detail_overdue_uncollected_rate').text(
            formatPercent(row_data.overdue_uncollected_rate_pct)
            );

        // FINANCIAL
        container_detail_chartTarget.find('#detail_total_ar_overdue').text(
            formatRupiah(row_data.total_ar_overdue)
            );

        container_detail_chartTarget.find('#detail_collected_amount_overdue').text(
            formatRupiah(row_data.collected_amount_overdue)
            );

        container_detail_chartTarget.find('#detail_unconfirmed_amount_overdue').text(
            formatRupiah(row_data.unconfirmed_amount_overdue)
            );

        container_detail_chartTarget.find('#detail_confirmed_amount_overdue').text(
            formatRupiah(row_data.confirmed_amount_overdue)
            );

        // TOTAL DIFFERENCE (auto warna)
        const diffVal = Number(row_data.total_difference_overdue || 0);
        const diffEl = container_detail_chartTarget.find('#detail_difference_overdue');

        diffEl
        .text(formatRupiah(diffVal))
        .removeClass('text-danger text-success')
        .addClass(diffVal < 0 ? 'text-danger' : 'text-success');
    }

    function renderDetail_badCollectionSalesDriver(row_data) {

        const container_detail_chartTarget = $('.container_detail_chart#badCollectionSalesTOPCOD');


        // IDENTITAS
        container_detail_chartTarget.find('#detail_sales_label').text(
            row_data.salesnameordrivername || row_data.label || '-'
            );

        container_detail_chartTarget.find('#detail_sales_ordertype').text(row_data.ordertype);
        container_detail_chartTarget.find('#detail_avg_days_late').text(row_data.avg_days_late || 0);

        // RATE / AMOUNT BOX
        container_detail_chartTarget.find('#detail_collected_amount').text(
            formatRupiah(row_data.confirmed_amount));

        container_detail_chartTarget.find('#detail_uncollected_amount').text(
            formatRupiah(row_data.unconfirmed_amount));

        // FINANCIAL SUMMARY
        container_detail_chartTarget.find('#detail_total_ar').text(
            formatRupiah(row_data.total_ar));

        // TOTAL DIFFERENCE (auto warna)
        const diffVal = Number(row_data.total_difference || 0);
        const diffEl = container_detail_chartTarget.find('#detail_difference');

        diffEl
        .text(formatRupiah(diffVal))
        .removeClass('text-danger text-success')
        .addClass(diffVal < 0 ? 'text-danger' : 'text-success');
    }
    function renderDetailBadCollectionCustomer(row_data) {

        const container_detail_chartTarget = $('.container_detail_chart#badCollectionCustomer_all');


        // IDENTITAS
        container_detail_chartTarget.find('#detail_customer_label').text(
            row_data.customername || row_data.label || '-'
            );

        container_detail_chartTarget.find('#detail_customer_code').text(
            row_data.customercode || '-'
            );

        container_detail_chartTarget.find('#detail_invoice_count').text(row_data.invoice_count);

        // HIGHLIGHT
        container_detail_chartTarget.find('#detail_collected_amount').text(
            formatRupiah(row_data.confirmed_amount));

        container_detail_chartTarget.find('#detail_uncollected_amount').text(
            formatRupiah(row_data.unconfirmed_amount));

        // FINANCIAL SUMMARY
        container_detail_chartTarget.find('#detail_total_ar').text(
            formatRupiah(row_data.total_ar)
            );
        // TOTAL DIFFERENCE (auto warna)
        const diffVal = Number(row_data.total_difference || 0);
        const diffEl = container_detail_chartTarget.find('#detail_difference');

        diffEl
        .text(formatRupiah(diffVal))
        .removeClass('text-danger text-success')
        .addClass(diffVal < 0 ? 'text-danger' : 'text-success');
    }



    var uncollected_color = "#F44336";
    var collected_color = "#4CAF50";

    $(document).ready(function(){





        {{-- Data SuccessCollect_branchAll --}}
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
                        backgroundColor: collected_color
                    },
                    {
                        key: 'uncollected_rate_pct',
                        label: 'Uncollected Rate ( %  )',
                        backgroundColor: uncollected_color
                    }
                ],
                heightChart : 500,
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    //Callback chart diklik
                    //Akan membuka modal detail chart yang menampilkan detail row data
                    open_modal_detailChart( '#successRateCollectionBranch_all',  function( modal_el, container_detail_chartTarget ){
                        //Ubah content di dalam container detail chart berdasarkan row data nya
                        console.log("++++ ROW DATA DETAIL YANG DIBUKA ++++++");
                        console.log( row_data );

                        renderDetail_successRateCollectionBranch( row_data )


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
                        backgroundColor: collected_color
                    },
                    {
                        key: 'overdue_uncollected_rate_pct',
                        label: 'Uncollected Amount ( %  )',
                        backgroundColor: uncollected_color
                    }
                ],
                heightChart : 500,
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    //Callback chart diklik
                    //Akan membuka modal detail chart yang menampilkan detail row data
                    open_modal_detailChart( '#successCollectOverdue_branchAll',  function( modal_el, container_detail_chartTarget ){
                        //Ubah content di dalam container detail chart berdasarkan row data nya
                        console.log("++++ ROW DATA DETAIL YANG DIBUKA ++++++");
                        console.log( row_data );

                        renderDetail_successRateCollectionOverdueBranch( row_data )
                    });
                }
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
                        backgroundColor: collected_color
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: uncollected_color
                    }
                ],
                heightChart : 500,
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    //Callback chart diklik
                    //Akan membuka modal detail chart yang menampilkan detail row data
                    open_modal_detailChart( '#badCollectionSalesTOPCOD',  function( modal_el, container_detail_chartTarget ){
                        //Ubah content di dalam container detail chart berdasarkan row data nya
                        console.log("++++ ROW DATA DETAIL YANG DIBUKA ++++++");
                        console.log( row_data );

                        renderDetail_badCollectionSalesDriver( row_data )

                    });
                }
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
                        backgroundColor: collected_color
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: uncollected_color
                    }
                ],
                heightChart : 500, 
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    //Callback chart diklik
                    //Akan membuka modal detail chart yang menampilkan detail row data
                    open_modal_detailChart( '#badCollectionSalesTOPCOD',  function( modal_el, container_detail_chartTarget ){
                        //Ubah content di dalam container detail chart berdasarkan row data nya
                        console.log("++++ ROW DATA DETAIL YANG DIBUKA ++++++");
                        console.log( row_data );
                        renderDetail_badCollectionSalesDriver( row_data )


                    });
                }
            }
        });

        //Data Bad Collection Customer
        var data_badCollectionCustomer = @json( $summary_badCollectionCustomer['result_all'] ); //[ {},{},{} ]
        buildStackedBarChart_customer({
            el: document.getElementById('chart_badCollectCustomer'),
            data: data_badCollectionCustomer,
            config: {
                stacks: [
                    {
                        key: 'confirmed_amount',
                        label: 'Collected Amount ( Rp )',
                        backgroundColor: collected_color
                    },
                    {
                        key: 'unconfirmed_amount',
                        label: 'Uncollected Amount ( Rp )',
                        backgroundColor: uncollected_color
                    }
                ],
                heightChart : 500,
                onBarClick: function( label, row_data, datasetLabel, value, dataIndex, datasetIndex ){
                    console.log( row_data )
                    //Callback chart diklik
                    //Akan membuka modal detail chart yang menampilkan detail row data
                    open_modal_detailChart( '#badCollectionCustomer_all',  function( modal_el, container_detail_chartTarget ){
                        //Ubah content di dalam container detail chart berdasarkan row data nya
                        console.log("++++ ROW DATA DETAIL YANG DIBUKA ++++++");
                        console.log( row_data );

                        renderDetailBadCollectionCustomer( row_data );


                    });
                }
            }
        }); 


        //+++++++++++++++++++++++++++++ PIE CHART SUMMARY PAYMENT TYPE +++++++++

        //Data Summary Payment Type TOP 
        var data_summary_paymentTypeTOP = @json( $summary_paymentType['result_TOP'] ); //[ {},{},{} ]
        buildPieChart({
            el: document.getElementById('chart_summaryPaymentTypeOrder_TOP'),
            datasets: data_summary_paymentTypeTOP,
            key_value: "confirmed_amount",
            label_color: {
                "Giro": "#22C55E",
                "Transfer": "#3B82F6",
                "Cash": "#F59E0B"
            }
        });

        //Data Summary Payment Type COD
        var data_summary_paymentTypeCOD = @json( $summary_paymentType['result_COD'] ); //[ {},{},{} ]
        buildPieChart({
            el: document.getElementById('chart_summaryPaymentTypeOrder_COD'),
            datasets: data_summary_paymentTypeCOD,
            key_value: "confirmed_amount",
            label_color: {
                "Giro": "#22C55E",
                "Transfer": "#3B82F6",
                "Cash": "#F59E0B"
            }
        });




    });









</script>



@endpush



