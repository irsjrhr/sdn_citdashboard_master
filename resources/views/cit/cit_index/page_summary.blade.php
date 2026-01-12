<style>
    .chart-scroll-wrapper canvas {
        min-height: 500px !important;
        height: auto !important;
    }

    .chart-scroll-wrapper {
        min-height: 550px;
        position: relative;
    }
</style>

<div class="title_type_data">
    Summary AR
</div>



@php

        // ==== Data Kategori Row Cashier Driver 

// $driver_collected_amount =  formatAbbreviatedNumber($row_cashier_driver['driver_collected_amount']);
// $cashier_confirmed_amount =  formatAbbreviatedNumber($row_cashier_driver['cashier_confirmed_amount']);
// $total_difference =  formatAbbreviatedNumber($row_cashier_driver['total_difference']);
// $confirmation_rate_pct = formatAbbreviatedNumber(number_format($row_cashier_driver['confirmation_rate_pct'], 2, '.', ''));


//         // ==== Data Kategori Row Ar Remainning 
// $remaining_ar_real = formatAbbreviatedNumber($row_ar_remaining['remaining_ar_real']);
// $overdue_pct = formatAbbreviatedNumber(number_format($row_ar_remaining['overdue_pct'], 2, '.', ''));
                //Key untuk Nilai Uang / Nominal
$nominal_keys = [
    "Total Outstanding AR",
    "Total Collected",
    "Total Confirmed Payment",
    "Total Outstanding AR OD",
    "Total Collected AR OD",
    "Total Confirmed Payment AR OD",
];

                //Key untuk Persentase / Ratio
$percentage_keys = [
    "% Confirm Payment",
    "% Confirm Payment AR OD",
];

                //Key untuk Jumlah Dokumen / Count
$document_keys = [
    "Total Invoice Document",
    "Total Confirmed Invoice Document",
    "Total Invoice Document AR OD",
    "Total Confirmed Invoice Document AR OD",
];

@endphp

{{-- Row Cashier Driver --}}
<div class="container-fluid container_summary" id="row_cashier_driver">
    <div class="row row_title_summary">
        <div class="col-12">
            Summary AR CIT
        </div>
    </div>
    {{-- Row Box Dashboard  --}}
    <div class="row row_box_dashboard wow animate__animated animate__fadeInUp">


        <div class="col-12" style="padding:0">
            <div class="kpi_grid">



                {{-- Loop Row Card Dashboard --}}

                @foreach ($row_card_dashboard as $key => $value )


                {{-- Kalo dia bukan array --}}
                @php
                if ( in_array($key, $nominal_keys) ) {
                    $value = "Rp" . formatAbbreviatedNumber($value);
                }else if ( in_array($key, $percentage_keys) ) {
                    $value =  "%" . formatAbbreviatedNumber(number_format($value, 2, '.', ''));
                }else if( in_array($key, $document_keys ) ){
                    $value = $value;
                }
                @endphp


                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">{{ $key }}</div>
                    <div class="kpi_value">{{ $value }}</div>
                </div>


                @endforeach
                {{-- End Of Loop Row Card Dashboard --}}

            </div>
        </div>






    </div>
    {{-- End Of Row Box Dashboard --}}

</div>
{{-- End Of Row Cashier Driver --}}


{{-- Container Summary - Summary Payment Type --}}
<div class="container-fluid container_summary" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - ALL --}}
        <div class="col-12 col_main_grafik">
            <div class="container-fluid">



                @if ( !empty($summary_paymentType['result_TOP']) && !empty($summary_paymentType['result_COD'])  )
                <div class="row">

                    {{-- col grafik - TOP --}}
                    <div class="col-sm-6 col_paymenyType_TOP wow animate__animated animate__fadeInRight">
                        <div class="card">
                            <div class="card-header row_title_summary">
                                Summary Payment Type - Order TOP
                            </div>
                            <div class="card-body">
                                <canvas id="chart_summaryPaymentTypeOrder_TOP"></canvas>
                            </div>
                        </div>

                    </div>
                    {{-- end of col grafik - TOP --}}


                    {{-- col grafik - COD --}}
                    <div class="col-sm-6 col_paymenyType_COD wow animate__animated animate__fadeInLeft">
                        <div class="card">
                            <div class="card-header row_title_summary">
                                Summary Payment Type - Order COD
                            </div>
                            <div class="card-body">
                                <canvas id="chart_summaryPaymentTypeOrder_COD"></canvas>
                            </div>
                        </div>

                    </div>
                    {{-- end of col grafik - COD --}}


                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>



</div>

{{-- Container Summary - Summary Payment Type --}}




{{-- Container Summary - Branches Success Rate Collection --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - ALL --}}
        <div class="col-12 col_main_grafik">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Success Rate Collection By Branch

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>



                @if ( !empty($summary_successCollect_branch['result_all']) )
                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_successRateCollectionBranch_all"></canvas>
                        </div>

                    </div>
                    {{-- end of col grafik --}}

                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>



</div>
{{-- End Of Top Entities - Branches --}}


{{-- Container Summary - Branches Success Rate Collection Overdue By Branches --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - ALL --}}
        <div class="col-12 col_main_grafik">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Success Rate Collection Overdue By Branches

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_successCollectOverdue_branch['result_all']) )
                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_successRateCollectionOverdueBranch_all"></canvas>
                        </div>
                    </div>
                    {{-- end of col grafik --}}

                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>



</div>
{{-- End Of Top Entities - Branches Success Rate Collection Overdue By Branches --}}







{{-- Container Summary - Drivers --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary row_tab_data">

        <div class="col-12 mb-4">
            <div class="nav_container">
                <div class="nav_tab">
                    <div class="tab_el tab_indicator_section text-center" data-target="col_section_top"> Salesman TOP </div>
                    <div class="tab_el tab_indicator_section text-center" data-target="col_section_cod"> Drivers COD </div>
                </div>
            </div>
        </div>

        {{-- Col Main Grafik - TOP --}}
        <div class="col-12 col_main_grafik col_grafik_top col_section_data" id="col_section_top">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Uncollected AR OD by Salesman ( TOP )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionDriver['result_TOP']) )
                <div class="row">
                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_badCollectionSalesDriver_top"></canvas>
                        </div>
                    </div>
                    {{-- end of col grafik --}}
                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik - TOP --}}


        {{-- Col Main Grafik  - COD--}}
        <div class="col-12 col_main_grafik col_grafik_cod col_section_data" id="col_section_cod">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Uncollected Amount By Drivers ( COD )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionDriver['result_COD']) )
                <div class="row">
                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_badCollectSalesDriver_cod"></canvas>
                        </div>
                    </div>
                    {{-- end of col grafik --}}
                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik  - COD --}}


    </div>



</div>
{{-- End Of Top Entities - Drivers --}}



{{-- Container Summary - Bad Colletion Customer --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - ALL --}}
        <div class="col-12 col_main_grafik">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 20 Uncollected AR OD by Customer

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionCustomer['result_all']) )
                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_badCollectCustomer"></canvas>
                        </div>
                    </div>
                    {{-- end of col grafik --}}
                </div>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data not found </strong> 
                </div>

                @endif
            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>



</div>
{{-- End Of Top Entities - Bad Colletion Customer --}}


<div class="modal fade" id="modal_detail_stackbar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        {{-- container detail stackbar - successRateCollectionBranch_all --}}
                        <div class="col-12 container_detail_stackbar" id="successRateCollectionBranch_all">

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

                        {{-- container detail stackbar - successCollectOverdue_branchAll --}}
                        <div class="col-12 container_detail_stackbar" id="successCollectOverdue_branchAll">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Cabang / Territory</h6>
                                <p class="mb-0">
                                    <span id="detail_overdue_label">TASIKMALAYA</span>
                                </p>
                                <small class="text-muted">
                                    Territory:
                                    <span id="detail_overdue_territory">TASIKMALAYA</span>
                                </small>
                            </div>

                            <!-- RATE -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Overdue Collected Rate</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_overdue_collected_rate">0.00%</span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-danger fw-bold">Overdue Uncollected Rate</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_overdue_uncollected_rate">100.00%</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Overdue Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar_overdue">
                                                    Rp 10.134.706.065
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount Overdue</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount_overdue">
                                                    Rp 766.544.240
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount Overdue</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount_overdue">
                                                    Rp 10.134.706.065
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount_overdue">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference_overdue" class="text-danger">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end of container detail stackbar - successCollectOverdue_branchAll --}}


                        {{-- container detail stackbar - sales TOP / driver COD --}}
                        <div class="col-12 container_detail_stackbar" id="BadCollectionSalesTOPCOD">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Sales / Driver</h6>
                                <p class="mb-0">
                                    <span id="detail_sales_label">unknown</span>
                                </p>
                                <small class="text-muted">
                                    Order Type:
                                    <span id="detail_sales_ordertype">TOP</span> |
                                    Avg Days Late:
                                    <span id="detail_avg_days_late">7</span> hari
                                </small>
                            </div>

                            <!-- RATE -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Collected Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_collected_amount">
                                                Rp 81.662.469.269
                                            </span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-warning fw-bold">Unconfirmed Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_unconfirmed_amount">
                                                Rp 17.436.040.470
                                            </span>
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
                                                <span id="detail_total_ar">
                                                    Rp 99.098.509.739
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount_summary">
                                                    Rp 81.662.469.269
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount_summary">
                                                    Rp 17.436.040.470
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount">
                                                    Rp 81.662.469.269
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-success">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{-- end of container detail stackbar - sales TOP / driver COD --}}

                        {{-- container detail stackbar - customer --}}
                        <div class="col-12 container_detail_stackbar" id="BadCollectionCustomer_all">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Customer</h6>
                                <p class="mb-0">
                                    <span id="detail_customer_label">
                                        PT. INDOMARCO PRISMATAMA
                                    </span>
                                </p>
                                <small class="text-muted">
                                    Customer Code:
                                    <span id="detail_customer_code">5810024345</span> |
                                    Total Invoice:
                                    <span id="detail_invoice_count">3.852</span>
                                </small>
                            </div>

                            <!-- HIGHLIGHT -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Collected Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_collected_amount">
                                                Rp 20.969.807.159
                                            </span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-warning fw-bold">Unconfirmed Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_unconfirmed_amount">
                                                Rp 5.794.746.512
                                            </span>
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
                                                <span id="detail_total_ar">
                                                    Rp 26.764.553.671
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount_summary">
                                                    Rp 20.969.807.159
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount_summary">
                                                    Rp 5.794.746.512
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount">
                                                    Rp 20.969.807.159
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-success">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{-- end of container detail stackbar - customer --}}


                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
















