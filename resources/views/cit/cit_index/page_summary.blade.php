
<div class="title_type_data">
    Summary AR
</div>


@php

        // ==== Data Kategori Row Cashier Driver 

$driver_collected_amount =  formatAbbreviatedNumber($row_cashier_driver['driver_collected_amount']);
$cashier_confirmed_amount =  formatAbbreviatedNumber($row_cashier_driver['cashier_confirmed_amount']);
$total_difference =  formatAbbreviatedNumber($row_cashier_driver['total_difference']);
$confirmation_rate_pct = formatAbbreviatedNumber(number_format($row_cashier_driver['confirmation_rate_pct'], 2, '.', ''));


        // ==== Data Kategori Row Ar Remainning 
$remaining_ar_real = formatAbbreviatedNumber($row_ar_remaining['remaining_ar_real']);
$overdue_pct = formatAbbreviatedNumber(number_format($row_ar_remaining['overdue_pct'], 2, '.', ''));


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


        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Outstanding AR </h6>
                <div class="h4 fw-bold text-secondary">
                    Rp {{ $remaining_ar_real }}

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Collected </h6>
                <div class="h4 fw-bold text-secondary">
                    Rp {{ $remaining_ar_real }}

                </div>
            </div>
        </div>

        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Confirmed Payment </h6>
                <div class="h4 fw-bold text-secondary">
                    Rp {{ $driver_collected_amount }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> % Payment Percentage </h6>
                <div class="h4 fw-bold text-secondary">
                    Rp {{ $cashier_confirmed_amount }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Confirmed Payment / Total Invoice Document </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $confirmation_rate_pct }}%

                </div>
            </div>
        </div>

        {{-- Col Box Dashboard --}}
        <div class="col-md-3">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Amount AR OD </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $overdue_pct }}%
                </div>
            </div>
        </div>

        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Collected AR OD </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $confirmation_rate_pct }}%

                </div>
            </div>
        </div>

        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Total Confirm Payment AR OD </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $confirmation_rate_pct }}%

                </div>
            </div>
        </div>

        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> % Confirm Payment AR OD </h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $confirmation_rate_pct }}%

                </div>
            </div>
        </div>

        <div class="col-md-3 col_box_dashboard">
            <div class="card shadow-sm p-4 text-center">
                <h6 class="text-black fw-bold"> Confirm Payment OD / Total Invoice OD</h6>
                <div class="h4 fw-bold text-secondary">
                    {{ $confirmation_rate_pct }}%
                </div>
            </div>
        </div>
        {{-- End Of Col Box Dashboard --}}
        
    </div>
    {{-- End Of Row Box Dashboard --}}

</div>
{{-- End Of Row Cashier Driver --}}




{{-- Top Entities - Branches --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - TOP --}}
        <div class="col-6 col_main_grafik col_grafik_top">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top Most 10 Branches with Uncollected Amount By OrderType TOP

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_territory_TOP) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_territory_TOP as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_top_branches" id="chart_top_branches" style="height: 500px !important; max-height: 500px;"></canvas>
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
        <div class="col-6 col_main_grafik col_grafik_cod">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top Most 10 Branches with Uncollected Amount By OrderType TOP

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_territory_TOP) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_territory_TOP as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_top_branches" id="chart_cod_branches" style="height: 500px !important; max-height: 500px;"></canvas>
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
{{-- End Of Top Entities - Branches --}}




{{-- Top Entities - Drivers --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - TOP --}}
        <div class="col-6 col_main_grafik col_grafik_top">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Bad Collection by Salesman (ToP)

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_customer_TOP) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_drivers_TOP as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_top_drivers" id="chart_top_branches" style="height: 500px !important; max-height: 500px;"></canvas>
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
        <div class="col-6 col_main_grafik col_grafik_cod">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top 10 Bad Collection by Drivers (CoD)

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_drivers_COD) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_drivers_COD as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_cod_drivers" id="chart_cod_branches" style="height: 500px !important; max-height: 500px;"></canvas>
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





{{-- Top Entities - Customer --}}
<div class="container-fluid container_summary wow animate__animated animate__fadeInUp" id="entities_branches">

    <div class="row row_main_summary">

        {{-- Col Main Grafik - TOP --}}
        <div class="col-6 col_main_grafik col_grafik_top">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top Most 10 Customer with Uncollected Amount By OrderType TOP

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_customer_TOP) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_customer_TOP as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_top_customer" id="chart_top_customer" style="height: 500px !important; max-height: 500px;"></canvas>
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
        <div class="col-6 col_main_grafik col_grafik_cod">
            <div class="container-fluid">
                <div class="row row_title_summary">
                    <div class="col-12">

                        Top Most 10 Customer with Uncollected Amount By OrderType COD

                        <button class="btn btn-secondary btn_tab_indicator tab_indicator" data-target="page_data_teritory"> View Detail Data </button>
                    </div>
                </div>

                @if ( !empty($result_customer_COD) )
                <div class="row">
                    {{-- col_card_entities --}}
                    <div class="col-sm-12 col_card_entities">
                        <div class="container_scroll_x">
                            <div class="row_scroll">
                                @foreach ($result_customer_COD as $key => $row_entities)
                                {{-- Card Entities --}}
                                <div class="card card_entities">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5> {{$row_entities['label']}} </h5>
                                            </div>
                                            <div class="col red_col">
                                                <p> Uncollected Amount  </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['unconfirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                            <div class="col green_col">
                                                <p> Confirmed Amount </p>
                                                <h5> 
                                                    Rp {{ formatAbbreviatedNumber( $row_entities['confirmed_amount'] ) }} 
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Of Card Entities --}}
                                @endforeach

                            </div>
                        </div>

                    </div>
                    {{-- end of col_card_entities --}}

                    {{-- col grafik --}}
                    <div class="col-sm-12">
                        <canvas class="chart_cod_customer" id="chart_cod_customer" style="height: 500px !important; max-height: 500px;"></canvas>
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










