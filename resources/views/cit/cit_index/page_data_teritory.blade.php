
<div class="title_type_data">
    Summary Per Territory 
</div>

<div class="container-fluid">
    {{-- Row Table --}}
    <div class="row row_table">
        <div class="col-12">
            <div class="table-responsive">
                @if ( !empty($data_view_teritory) )

                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-light">
                        <tr>
                            @foreach ($data_view_teritory[0] as $key => $kolom )
                            <td>{{ $maps_header_teritory[$key] }}</td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_view_teritory as $row_data)
                        <tr>
                            @foreach ($data_view_teritory[0] as $key2=>$kolom)
                            @php
                            $class_kolom_angka = "";
                            $nilai = $row_data[$key2];
                            if ( $key2 != "territoryname" && $key2 != "ordertype" && $key2 != "territoryid" ) {
                                // Kalo nilai kolomnya string berbentuk nilai nominal, maka ubah tipe datanya ke angka dan number format
                                // angka itu kolomnya rata kanan 
                                $class_kolom_angka = "kolom_angka";
                                $nilai = (float) $nilai;
                                if ( $key2 == "collection_rate_pct" || $key2 == "trust_gap_pct" ) {
                                    $nilai = formatAbbreviatedNumber(number_format($nilai, 2, '.', ''));
                                }else{
                                    $nilai = number_format(  $nilai  );
                                }
                            }
                            @endphp

                            <td class="{{$class_kolom_angka}}"> {{$nilai}}  </td>
                            @endforeach
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
        </div>
    </div>
    {{-- End Of Row Table --}}
{{--     <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td> S </td>
                    <td> S </td>
                    <td> S </td>
                    <td> S </td>
                </tr>
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td>s</td>
                    <td>s</td>
                    <td>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12" style="border-bottom: 3px solid #000;">
                                    s
                                </div>
                                <div class="col-12">
                                    s
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12" style="border-bottom: 3px solid #000;">
                                    s
                                </div>
                                <div class="col-12">
                                    s
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endfor

                
            </table>
        </div>
    </div>
    --}}


</div>




