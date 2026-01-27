<style>
    .chart-scroll-wrapper canvas {
        min-height: 500px !important;
        height: auto !important;
    }

    .chart-scroll-wrapper {
        min-height: 550px;
        position: relative;
    }
{{--     .chart-wrapper{
        height:400px;
    } --}}
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
    <div class="row row_box_dashboard">


        <div class="col-12" style="padding:0">




            {{-- ================= KPI DASHBOARD ================= --}}
            <div class="kpi_grid">

                {{-- ================= TOTAL AR ================= --}}

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title"> Total AR To Be Collected </div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Outstanding AR'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Collected</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Collected'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Confirmed Payment</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Confirmed Payment'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">% Confirm Payment</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['% Confirm Payment'];
                        $value = formatAbbreviatedNumber(number_format($value, 2, '.', '')) . '%';
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Invoice Document</div>
                    <div class="kpi_value">
                        {{ $row_card_dashboard['Total Invoice Document'] }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Confirmed Invoice Paymen</div>
                    <div class="kpi_value">
                        {{ $row_card_dashboard['Total Confirmed Invoice Document'] }}
                    </div>
                </div>

                {{-- ================= AR OD ================= --}}

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Outstanding AR OD</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Outstanding AR OD'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Collected AR OD</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Collected AR OD'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Confirmed Payment AR OD</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['Total Confirmed Payment AR OD'];
                        $value = 'Rp ' . formatAbbreviatedNumber($value);
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">% Confirm Payment AR OD</div>
                    <div class="kpi_value">
                        @php
                        $value = $row_card_dashboard['% Confirm Payment AR OD'];
                        $value = formatAbbreviatedNumber(number_format($value, 2, '.', '')) . '%';
                        @endphp
                        {{ $value }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Invoice Document AR OD</div>
                    <div class="kpi_value">
                        {{ $row_card_dashboard['Total Invoice Document AR OD'] }}
                    </div>
                </div>

                <div class="kpi_card kpi_blue">
                    <div class="kpi_title">Total Confirmed Invoice Document AR OD</div>
                    <div class="kpi_value">
                        {{ $row_card_dashboard['Total Confirmed Invoice Document AR OD'] }}
                    </div>
                </div>

            </div>
            {{-- ================= END KPI DASHBOARD ================= --}}

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



                <div class="row">

                    {{-- col grafik - TOP --}}
                    <div class="col-sm-6 col_paymenyType_TOP wow animate__animated animate__fadeInRight">
                        <div class="card">
                            <div class="card-header row_title_summary">
                                Summary Payment Type - Order TOP
                            </div>
                            <div class="card-body">

                                @if ( !empty($summary_paymentType['result_TOP']) )  

                                <div class="chart_wrapper_pie">
                                    <canvas id="chart_summaryPaymentTypeOrder_TOP"></canvas>
                                </div>

                                @else

                                <div class="alert alert-danger py-2 mb-3">
                                    <strong> Data not found </strong> 
                                </div>

                                @endif



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

                                @if ( !empty($summary_paymentType['result_COD']) )  

                                <div class="chart_wrapper_pie">
                                    <canvas id="chart_summaryPaymentTypeOrder_COD"></canvas>
                                </div>

                                @else
                                
                                <div class="alert alert-danger py-2 mb-3">
                                    <strong> Data not found </strong> 
                                </div>

                                @endif

                            </div>
                        </div>

                    </div>
                    {{-- end of col grafik - COD --}}


                </div>

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

                        Success Rate Collection AR By Branch

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>


                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">

                        @if ( !empty($summary_successCollect_branch['result_TOP']) )  

                        <div class="chart-wrapper">
                            <canvas id="chart_successRateCollectionBranch_all"></canvas>
                        </div>

                        @else

                        <div class="alert alert-danger py-2 mb-3">
                            <strong> Data not found </strong> 
                        </div>

                        @endif
                    </div>
                    {{-- end of col grafik --}}

                </div>

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

                        Success Rate Collection AR Overdue By Branches

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                <div class="row">


                    <div class="col-sm-12">
                        @if ( !empty($summary_successCollectOverdue_branch['result_all']) )  

                        <div class="chart-wrapper">
                            <canvas id="chart_successRateCollectionOverdueBranch_all"></canvas>
                        </div>

                        @else

                        <div class="alert alert-danger py-2 mb-3">
                            <strong> Data not found </strong> 
                        </div>

                        @endif
                    </div>


                </div>

            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>



</div>
{{-- End Of Top Entities - Branches Success Rate Collection Overdue By Branches --}}





{{-- Container Summary - Drivers --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary row_tab_data">


        {{-- Col Main Grafik - TOP --}}
        <div class="col-sm-6 col_main_grafik col_grafik_top col_section_data active" id="col_section_top" style="margin-bottom:50px;">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Uncollected AR OD by Salesman ( TOP )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>



                <div class="row">


                    <div class="col-sm-12">
                        @if ( !empty($summary_badCollectionDriver['result_TOP']) )  

                        <div class="chart-wrapper">
                            <canvas id="chart_badCollectionSalesDriver_top"></canvas>
                        </div>

                        @else

                        <div class="alert alert-danger py-2 mb-3">
                            <strong> Data not found </strong> 
                        </div>

                        @endif
                    </div>


                </div>
            </div>   
        </div>
        {{-- End Of Col Main Grafik - TOP --}}


        {{-- Col Main Grafik  - COD--}}
        <div class="col-sm-6 col_main_grafik col_grafik_cod col_section_data active" id="col_section_cod">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Uncollected Amount By Drivers ( COD )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>


                <div class="row">


                    <div class="col-sm-12">
                        @if ( !empty($summary_badCollectionDriver['result_COD']) )  

                        <div class="chart-wrapper">
                            <canvas id="chart_badCollectSalesDriver_cod"></canvas>
                        </div>

                        @else

                        <div class="alert alert-danger py-2 mb-3">
                            <strong> Data not found </strong> 
                        </div>

                        @endif
                    </div>


                </div>

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


                <div class="row">


                    <div class="col-sm-12">
                        @if ( !empty($summary_badCollectionCustomer['result_all']) )  

                        <div class="chart-wrapper-customer" style="height:700px;width:100%;overflow: unset!important">
                            <canvas id="chart_badCollectCustomer"></canvas>
                        </div>

                        @else

                        <div class="alert alert-danger py-2 mb-3">
                            <strong> Data not found </strong> 
                        </div>

                        @endif
                    </div>


                </div>


            </div>   
        </div>
        {{-- End Of Col Main Grafik - ALL --}}


    </div>


</div>
{{-- End Of Top Entities - Bad Colletion Customer --}}







{{-- Container Summary - COH VS BANK IN --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">



    {{-- Row Chart --}}

    <div class="row row_main_summary">
        {{-- Col Main Grafik - COH Bank In By Branch --}}
        <div class="col-sm-12 col_main_grafik col_grafik_top col_section_data active" id="col_section_top" style="margin-bottom:50px;">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        COH VS Bank In By Branch
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="chart-wrapper">
                            <canvas id="chart_cohBankIn"></canvas>
                        </div>
                    </div>
                </div>

            </div>   
        </div>
        {{-- End Of Col Main Grafik - COH Bank In By Branch --}}


    </div>
    {{-- End Of Row Chart --}}

    {{--  Row Tabular Data --}}
    <div class="row">
        <div class="col-12">
            <div class="container-fluid">


                {{-- Row Table --}}
                <div class="row row_table">
                    <div class="col-12">
                        {{-- Table Responsive --}}
                        <div class="table-responsive">
                            @if (!empty($data_tabular_cohBankIn))

                            <table class="table table-bordered table-striped align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Branch Code</th>
                                        <th>Branch Name</th>
                                        <th>Collection Date</th>
                                        <th>Transaction Type</th>

                                        <th>Outstanding AR</th>
                                        <th>Total Collection</th>
                                        <th>Payment Difference</th>
                                        <th>Payment Performance (Value)</th>
                                        <th>Difference Flag</th>

                                        <th>Total Invoice</th>
                                        <th>Total Collected Doc</th>
                                        <th>Payment Performance (Doc)</th>

                                        <th>Payment Cash</th>
                                        <th>Payment Transfer</th>
                                        <th>Payment Giro</th>
                                        <th>Total Payment Cash + TF</th>

                                        <th>Total TOP OD</th>
                                        <th>Total Paid TOP OD</th>
                                        <th>Payment Performance OD</th>

                                        <th>COH</th>
                                        <th>Bank In</th>
                                        <th>Balance</th>
                                        <th>Reason</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    @foreach ($data_tabular_cohBankIn as $row_data)
                                    <tr>

                                        <td><?= $row_data['BranchCode'] ?></td>
                                        <td><?= $row_data['BranchName'] ?></td>
                                        <td><?= $row_data['CollectionDate'] ?></td>
                                        <td><?= $row_data['TransactionType'] ?></td>

                                        <td class="kolom_angka"><?= number_format($row_data['Outstanding_AR'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Total_Collection'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Selisih_Payment'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Payment_Performance_Value'], 0, ',', '.') ?></td>
                                        <td><?= $row_data['Flag_Selisih'] ?></td>

                                        <td class="kolom_angka"><?= $row_data['Total_Doc_Invoice'] ?></td>
                                        <td class="kolom_angka"><?= $row_data['Total_Collected_Doc'] ?></td>
                                        <td class="kolom_angka"><?= $row_data['Payment_Performance_Document'] ?></td>

                                        <td class="kolom_angka"><?= number_format($row_data['Total_Payment_Cash'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Total_Payment_TF'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Total_Payment_Giro'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Total_Payment_Cash_TF'], 0, ',', '.') ?></td>

                                        <td class="kolom_angka"><?= number_format($row_data['Total_TOP_OD_Value'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Total_Paid_TOP_OD_Value'], 0, ',', '.') ?></td>
                                        <td><?= $row_data['Payment_Performance_OD_Value'] ?? '-' ?></td>

                                        <td class="kolom_angka"><?= number_format($row_data['COH'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Bank In'], 0, ',', '.') ?></td>
                                        <td class="kolom_angka"><?= number_format($row_data['Balance'], 0, ',', '.') ?></td>
                                        <td><?= $row_data['Reason'] ?: '-' ?></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @else

                            <div class="alert alert-danger py-2 mb-3">
                                <strong> Data Not Founded </strong> 
                            </div>

                            @endif
                        </div>
                        {{-- End Of Table Responsive --}}

                        @if ( !empty( $data_tabular_cohBankIn ) )

                        <div class="float-end small">{{ $data_paginator_cohBankIn->links() }}</div>

                        @endif

                    </div>
                </div>
                {{-- End Of Row Table --}}

            </div>

        </div>
    </div>
    {{--  End Of Row Tabular Data --}}





</div>
{{-- Container Summary - COH VS BANK IN --}}













