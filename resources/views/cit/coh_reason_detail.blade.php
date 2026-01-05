
@extends('layouts.app')
@section('titlepage', 'COH Dashboard')

@section('content')
@section('navigasi')
<span>COH Dashboard</span>
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
            <div class="alert alert-primary py-2 mb-3">
                <strong> View Detail By Territory ID : {{request()->input('branchCode')}} </strong> 
            </div>
        </div>
    </div>
</div>

<div class="row row_container row_main">   

    {{-- Col_container_data - page_data_teritory --}}
    <div class="col-12 col_container_data type_data active pt-3" id="data_table">

{{--         <div class="title_type_data">
            Summary Per Territory 
        </div>
        --}}
        <div class="container-fluid">
            {{-- Row Table --}}
            <div class="row row_table">
                <div class="col-12">
                    <div class="table-responsive">
                        @if (!empty($data_coh_detail))

                        <table class="table table-bordered table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    @foreach ($data_coh_detail[0] as $key_kolom => $nilai_kolom )
                                    <td> {{ $key_kolom }} </td>
                                    @endforeach
                                    <td> Action </td>
                                </tr>

                            </thead>

                            <tbody>
                                @for ($i = 0; $i < 15; $i++)
                                @php
                                $row_coh_detail = $data_coh_detail[$i];
                                @endphp
                                <tr data-row-db="{{ @json_encode( $row_coh_detail ) }}">
                                    @foreach ($row_coh_detail as $key_kolom => $nilai_kolom )
                                    @php
                                    //Melakukan number formating untuk nilai yang keynya termasuk ke dalam $key_rupiah_data
                                    $nilai = $row_coh_detail[$key_kolom];
                                    if( in_array( $key_kolom, $key_rupiah_data ) ){
                                        $nilai = number_format( $nilai );
                                    }   
                                    @endphp
                                    <td> {{ $nilai }} </td>
                                    @endforeach

                                    <td> 
                                        <button type="button" class="btn btn-primary btn_modal_update"> <i class="fas fa-pen me-2"></i> Edit </button>
                                    </td>
                                </tr>
                                @endfor

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



        </div>

    </div>
    {{-- End Of Col_container_data - page_data_teritory --}}



</div>





{{-- Modal Update --}}
<div class="modal fade" id="modal_update" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Update Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <form class="modal_update_form">

          {{-- Header Information --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="small text-muted fw-bold">Branch Code</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                  <input type="text" name="territoryid" class="form-control" readonly>
              </div>
          </div>
      </div>

      <div class="col-md-6">
          <div class="form-group mb-3">
            <label class="small text-muted fw-bold">Collection Date</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="ti ti-calendar"></i></span>
              <input type="text" name="collectiondate" class="form-control" readonly>
          </div>
      </div>
  </div>
</div>
{{-- End Header Information --}}

{{-- Personal Information --}}
<div class="row">
    <div class="col-md-6">
      <div class="form-group mb-3">
        <label class="small text-muted fw-bold">Branch Name</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="fas fa-user"></i></span>
          <input type="text" name="territoryname" class="form-control" readonly>
      </div>
  </div>
</div>


<div class="col-md-6">
  <div class="form-group mb-3">
    <label class="small text-muted fw-bold"> Billing Number </label>
    <div class="input-group input-group-merge">
      <span class="input-group-text"><i class="fas fa-file"></i></span>
      <input type="text" name="billingnumber" class="form-control" readonly>
  </div>
</div>
</div>

</div>
{{-- End Personal Information --}}

{{-- Diff Reason --}}
<div class="row">
    <div class="col-12">
      <div class="form-group mb-3">
        <label class="small text-muted fw-bold">Diff Reason</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-notes"></i></span>
          <textarea name="differencereason_detail"
          class="form-control"
          rows="3"
          placeholder="Input diff reason"></textarea>
      </div>
  </div>
</div>
</div>
{{-- End Diff Reason --}}

</form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
      Close
  </button>
  <button type="button" class="btn btn-primary btn_save_update">
      Save changes
  </button>
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

    //Mengubah dari bentuk object ke bentuk json encrypt 
    function cv_obj_json( obj ) {
        // Ubah object ke json
        return JSON.stringify(obj);
    }
    //Mengubah dari bentuk json yang di encrypt ke bntuk object 
    function cv_json_obj(json) {
        // Ubah json ke obj
        return JSON.parse( json )
    }

    var form_update_fill = ( rowData ) =>{
        {{-- rowData adalah bentuk object --}}
        const form = $('#modal_update form');


        // Header Information
        form.find('[name="territoryid"]').val(rowData.territoryid);
        form.find('[name="territoryname"]').val(rowData.territoryname);
        form.find('[name="collectiondate"]').val(rowData.collectiondate);
        form.find('[name="billingnumber"]').val(rowData.billingnumber);
        form.find('[name="differencereason_detail"]').val(rowData.differencereason_detail);

    }

    $(document).ready(function(){
        $('.btn_modal_update').on('click', function(e){

            var btn_update_modal = $(this);
            var tr = btn_update_modal.parents('tr');
            var data_id = tr.attr('data-id');
            var data_row_db = tr.attr('data-row-db');
            data_row_db = cv_json_obj( data_row_db );
            var modal_update = document.getElementById('modal_update');
            modal_update = new bootstrap.Modal( modal_update );   

            form_update_fill( data_row_db );
            modal_update.show();
        });

    });


</script>


@endsection



@push('myscript')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ asset('assets/js/utils/number-format-abbreviated.js') }}"></script>



@endpush