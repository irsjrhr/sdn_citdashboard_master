
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

        @php
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


        {{-- Col Box Dashboard --}}
        <div class="col-md-2" style="margin-left: 20px;padding: 0;margin-bottom: 30px;">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> {{ $key }} </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $value }}

                </div>
            </div>
        </div>
        {{-- End Of Col Box Dashboard --}}

        @endforeach
        {{-- End Of Loop Row Card Dashboard --}}



    </div>
    {{-- End Of Row Box Dashboard --}}

</div>
{{-- End Of Row Cashier Driver --}}




{{-- Top Entities - Branches Success Rate Collection --}}
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

                @if ( !empty($summary_successCollectOverdue_branch['result_all']) )
                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_successRateCollectionBranch_all"></canvas>
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


{{-- Top Entities - Branches Success Rate Collection Overdue By Branches --}}
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
                        <canvas class="chart_successRateCollectionOverdueBranch_all"></canvas>
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







{{-- Top Entities - Drivers --}}
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

                        Top 10 Bad Collection by Salesman ( TOP )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionDriver['result_TOP']) )
                <div class="row">
                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_badCollectionDriver_top" style="height: 500px !important; max-height: 500px;"></canvas>
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

                        Top 10 Bad Collection by Drivers ( COD )

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_driversales"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionDriver['result_COD']) )
                <div class="row">
                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_badCollectSalesDriver_cod" style="height: 500px !important; max-height: 500px;"></canvas>
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



{{-- Top Entities - Bad Colletion Customer --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - ALL --}}
        <div class="col-12 col_main_grafik">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 20 Bad Collection By Customer

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($summary_badCollectionCustomer['result_all']) )
                <div class="row">

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_badCollectCustomer"></canvas>
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















