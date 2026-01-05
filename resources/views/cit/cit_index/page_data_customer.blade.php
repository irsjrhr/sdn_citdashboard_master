
<div class="title_type_data">
    Customer Report 
</div>

<div class="container-fluid">
    {{-- Row Table --}}
    <div class="row row_table">
        <div class="col-12">
            <div class="table-responsive">
                @if (!empty($data_customer))
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-light">
                        <tr>
                            @foreach ($data_customer[0] as $key => $kolom )
                            <td>{{ $maps_header_customer[$key] }}</td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_customer as $row_data)


                        <tr>
                            @foreach ($data_customer[0] as $key2=>$kolom)
                            @php
                            $nilai = $row_data[$key2];
                            $class_kolom_angka = "";
                            if ( $key2 != "customername" && $key2 != "customercode" && $key2 != "ordertype"  ) {
                                // Kalo nilai kolomnya string berbentuk nilai nominal, maka ubah tipe datanya ke angka dan number format
                                // Kalo nilai kolomnya string berbentuk nilai nominal, maka ubah tipe datanya ke angka dan number format
                                // angka itu kolomnya rata kanan 
                                $class_kolom_angka = "kolom_angka";
                                $nilai = number_format( ( float ) $nilai  );
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


</div>


