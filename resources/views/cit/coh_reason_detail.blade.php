
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

<style>
    #form_update_detailCOH,
    #form_update_detailCIT{
        display : none
    }
    #form_update_detailCOH.active,
    #form_update_detailCIT.active{

        display : block
    }
</style>


<div class="row row_container mb-3">
    <div class="col-12 col_nav">
        <div class="nav_container">
            <div class="alert alert-primary py-2 mb-3">
                <strong> View Detail By Territory ID : {{request()->input('branchCode')}} </strong> 
            </div>
        </div>
        <div class="nav_container">
            <div class="nav_tab">
                <div class="tab_el tab_indicator" data-target="data_table_cit"> Detail Table CIT </div>
                <div class="tab_el tab_indicator" data-target="data_table_coh"> Detail Table COH </div>
            </div>
        </div>
    </div>
</div>

<div class="row row_container row_main">   

    {{-- Col_container_data - page data_table_cit--}}
    <div class="col-12 col_container_data type_data active pt-3" id="data_table_cit">

        <div class="title_type_data">
            Detail Table CIT 
        </div>

        <div class="container-fluid">
            {{-- Row Table --}}
            <div class="row row_table">
                <div class="col-12">
                    <div class="table-responsive">
                        @if (!empty($data_coh_detailCIT))

                        <table class="table table-bordered table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th> Action </th>
                                    <th>Billing Number</th>
                                    <th>Region</th>
                                    <th>Territory ID</th>
                                    <th>Territory Name</th>
                                    <th>Sales / Driver</th>
                                    <th>Collection Type</th>
                                    <th>Collection Plan ID</th>
                                    <th>Collection Date</th>
                                    <th>Customer Code</th>
                                    <th>Customer Name</th>
                                    <th>Billing Date</th>
                                    <th>Billing Due Date</th>
                                    <th>Order Type</th>

                                    <th>Billing Amount</th>

                                    <th>Cash Collection</th>
                                    <th>Cash Confirm</th>
                                    <th>Cash Difference</th>

                                    <th>Transfer Collection</th>
                                    <th>Transfer Confirm</th>
                                    <th>Transfer Difference</th>

                                    <th>Giro Collection</th>
                                    <th>Giro Confirm</th>
                                    <th>Giro Difference</th>

                                    <th>Total Payment</th>
                                    <th>Claim / Promo / Return</th>

                                    <th>Bank Name (Transfer)</th>
                                    <th>Due Date Giro</th>
                                    <th>Giro Number</th>
                                    <th>Bank Name (Giro)</th>

                                    <th>Uncollectible Reason</th>

                                    <th>Doc Status</th>
                                    <th>End Session</th>

                                    <th>Dafin Created Date</th>
                                    <th>Dafin Created By</th>
                                    <th>Updated Date</th>
                                    <th>Updated By</th>
                                    <th>Send Date</th>

                                    <th>Reason</th>
                                </tr>

                            </thead>

                            <tbody>
                                @for ($i = 0; $i < count( $data_coh_detailCIT ); $i++)
                                @php
                                $row_data = $data_coh_detailCIT[$i];
                                @endphp

                                <tr data-row-db="{{ @json_encode( $row_data ) }}">
                                    <td> 
                                        <button type="button" id="btn_update_detailCIT" class="btn btn-primary btn_modal_update"> 
                                            <i class="fas fa-pen me-2"></i> Edit 
                                        </button>
                                    </td>
                                    <td><?= $row_data['billingnumber'] ?></td>
                                    <td><?= trim($row_data['region']) ?></td>
                                    <td><?= $row_data['territoryid'] ?></td>
                                    <td><?= $row_data['territoryname'] ?></td>
                                    <td><?= $row_data['salesnameordrivername'] ?></td>
                                    <td><?= $row_data['collectiontype'] ?></td>
                                    <td><?= $row_data['collectionplanid'] ?></td>
                                    <td><?= $row_data['collectiondate'] ?></td>
                                    <td><?= $row_data['customercode'] ?></td>
                                    <td><?= $row_data['customername'] ?></td>
                                    <td><?= $row_data['billingdate'] ?></td>
                                    <td><?= $row_data['billingduedate'] ?></td>
                                    <td><?= $row_data['ordertype'] ?></td>

                                    <td class="kolom_angka"><?= number_format($row_data['amount'], 0, ',', '.') ?></td>

                                    <td class="kolom_angka"><?= number_format($row_data['cash_collectionamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['cash_confirmamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['cash_differenceamount'], 0, ',', '.') ?></td>

                                    <td class="kolom_angka"><?= number_format($row_data['transfer_collectionamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['transfer_confirmamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['transfer_differenceamount'], 0, ',', '.') ?></td>

                                    <td class="kolom_angka"><?= number_format($row_data['giro_collectionamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['giro_confirmamount'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['giro_differenceamount'], 0, ',', '.') ?></td>

                                    <td class="kolom_angka"><?= number_format($row_data['total_payment'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['claimpromoorreturn'], 0, ',', '.') ?></td>

                                    <td><?= $row_data['banknametransfer'] ?: '-' ?></td>
                                    <td><?= $row_data['duedategiro'] ?: '-' ?></td>
                                    <td><?= $row_data['gironumber'] ?: '-' ?></td>
                                    <td><?= $row_data['banknamegiro'] ?: '-' ?></td>

                                    <td><?= $row_data['Uncollectible Reason'] ?></td>

                                    <td><?= $row_data['docstatus'] ?></td>
                                    <td><?= $row_data['endsessionstatus'] ?></td>

                                    <td><?= $row_data['dafincreateddate'] ?></td>
                                    <td><?= $row_data['dafincreatedby'] ?></td>
                                    <td><?= $row_data['updateddate'] ?></td>
                                    <td><?= $row_data['updatedby'] ?></td>
                                    <td><?= $row_data['senddate'] ?></td>

                                    <td><?= $row_data['reason'] ?? '-' ?></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>

                        @else

                        <div class="alert alert-danger py-2 mb-3"><strong> Data Not Founded </strong> </div>

                        @endif

                    </div>
                </div>
            </div>



        </div>

    </div>
    {{-- End Of Col_container_data - page data_table_cit--}}



    {{-- Col_container_data - page data_table_coh--}}
    <div class="col-12 col_container_data type_data pt-3" id="data_table_coh">

        <div class="title_type_data">
            Detail Table COH 
        </div>

        <div class="container-fluid">
            {{-- Row Table --}}
            <div class="row row_table">
                <div class="col-12">
                    <div class="table-responsive">
                        @if (!empty($data_coh_detailCOH))

                        <table class="table table-bordered table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th> Action </th>
                                    <th>Territory ID</th>
                                    <th>Payment Type</th>
                                    <th>Collection Date</th>
                                    <th>COH</th>
                                    <th>Bank In</th>
                                    <th>Difference</th>
                                    <th>Difference Reason</th>
                                </tr>
                            </thead>

                            <tbody>
                                @for ($i = 0; $i < count( $data_coh_detailCOH ); $i++)
                                @php
                                $row_data = $data_coh_detailCOH[$i];
                                @endphp
                                <tr data-row-db="{{ @json_encode( $row_data ) }}">
                                    <td> 
                                        <button type="button" id="btn_update_detailCOH" class="btn btn-primary btn_modal_update"> 
                                            <i class="fas fa-pen me-2"></i> Edit 
                                        </button>
                                    </td>
                                    <td><?= $row_data['territoryid'] ?></td>
                                    <td><?= $row_data['Payment Type'] ?></td>
                                    <td><?= $row_data['Collection Date'] ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['COH'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['Bank In'], 0, ',', '.') ?></td>
                                    <td class="kolom_angka"><?= number_format($row_data['Difference'], 0, ',', '.') ?></td>
                                    <td><?= $row_data['DifferenceReason'] ?? '-' ?></td>

                                </tr>
                                @endfor
                            </tbody>
                        </table>

                        @else

                        <div class="alert alert-danger py-2 mb-3"><strong> Data Not Founded </strong> </div>

                        @endif

                    </div>
                </div>
            </div>



        </div>

    </div>
    {{-- End Of Col_container_data - page data_table_coh--}}



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


        {{-- modal update form - form_update_detailCIT --}}
        <form class="modal_update_form" id="form_update_detailCIT">
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
        {{-- End Of modal update form - form_update_detailCIT --}}

        {{-- modal update form - form_update_detailCOH --}}
        <form class="modal_update_form" id="form_update_detailCOH">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Branch Code</label>
                        <input type="text" name="territoryid" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Collection Date</label>
                        <input type="text" name="collectiondate" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Payment Type</label>
                        <input type="text" name="payment_type" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Difference</label>
                        <input type="text" name="difference" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">COH</label>
                        <input type="text" name="coh" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Bank In</label>
                        <input type="text" name="bank_in" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold">Difference Reason</label>
                        <textarea
                        name="differencereason"
                        class="form-control"
                        rows="3"
                        placeholder="Input difference reason"></textarea>
                    </div>
                </div>
            </div>

        </form>
        {{-- End Of modal update form - form_update_detailCOH --}}





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



    $(document).ready(function(){



        //Event method untuk .btn_modal_update untuk update detail CIT di table section CIT untuk form #form_update_detailCIT
        $('.btn_modal_update#btn_update_detailCIT').on('click', function(e){


            //+++++++ Menyiapkan dan mengambil row data dari atribut tr +++++++
            var btn_update_modal = $(this);
            var tr = btn_update_modal.parents('tr');
            var data_row_db = tr.attr('data-row-db');
            var row_data = cv_json_obj( data_row_db );


            //+++++++ Membuka modal update, Membuka form update CIT, mengimplementasikan ke kolom form dari row data +++++++
            var modal_update = document.getElementById('modal_update');
            modal_update = new bootstrap.Modal( modal_update );  //Inisiasi modal dalam bootstrap 5 
            var form = $('#modal_update').find('form');
            var formTarget = form.filter('#form_update_detailCIT');


            console.log( row_data );


            //Membuka form_update_detailCIT dan menutup form_update_detailCOH dengan tanda berupa class active 
            console.log("Membuka form", formTarget);
            form.removeClass('active');
            formTarget.addClass('active');

            // Header Information
            formTarget.find('[name="territoryid"]').val(row_data.territoryid);
            formTarget.find('[name="territoryname"]').val(row_data.territoryname);
            formTarget.find('[name="collectiondate"]').val(row_data.collectiondate);
            formTarget.find('[name="billingnumber"]').val(row_data.billingnumber);
            formTarget.find('[name="differencereason_detail"]').val(row_data.differencereason_detail);


            modal_update.show();
        });

        //Event method untuk .btn_modal_update untuk update detail COH di table section COH untuk form #form_update_detailCOH
        $('.btn_modal_update#btn_update_detailCOH').on('click', function () {

            var tr = $(this).parents('tr');
            var data_row_db = tr.attr('data-row-db');
            var row_data = cv_json_obj(data_row_db);

            var modal_update = new bootstrap.Modal(
                document.getElementById('modal_update')
                );

            var form = $('#modal_update').find('form');
            var formTarget = form.filter('#form_update_detailCOH');

            // switch form
            form.removeClass('active');
            formTarget.addClass('active');

            // mapping data
            formTarget.find('[name="territoryid"]').val(row_data.territoryid);
            formTarget.find('[name="collectiondate"]').val(row_data['Collection Date']);
            formTarget.find('[name="payment_type"]').val(row_data['Payment Type']);
            formTarget.find('[name="coh"]').val(row_data['COH']);
            formTarget.find('[name="bank_in"]').val(row_data['Bank In']);
            formTarget.find('[name="difference"]').val(row_data['Difference']);
            formTarget.find('[name="differencereason"]').val(row_data['DifferenceReason']);

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