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
            {{-- Filter --}}
            <form action="{{ route('cit.coh_reason') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 mb-4">

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
        </div>
    </div>
</div>

<div class="row row_container row_main">   

    {{-- Col_container_data - page_data_teritory --}}
    <div class="col-12 col_container_data type_data active pt-3" id="data_table">

        <div class="title_type_data">
            Summary Per Territory 
        </div>
        
        <div class="container-fluid">
            {{-- Row Table --}}
            <div class="row row_table">
                <div class="col-12">
                    {{-- Table Responsive --}}
                    <div class="table-responsive">
                        @if (!empty($data_coh))

                        <table class="table table-bordered table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
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
                                @foreach ($data_coh as $row_data)
                                <tr>
                                    <td> 
                                        @php
                                        $branchCode = $row_data['BranchCode'];
                                        $collectionDate = $row_data['CollectionDate'];
                                        $url_direct = asset('cit/coh_reason_detail?branch=') . $branchCode . "&" . "collectionDate=" . $collectionDate;
                                        @endphp
                                        <a class="btn btn-primary" href="{{ $url_direct }}">

                                            View Detail 
                                            
                                        </a>
                                    </td>

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

                    @if ( !empty( $data_coh ) )

                    <div class="float-end small">{{ $data_paginator->links() }}</div>

                    @endif

                </div>
            </div>
            {{-- End Of Row Table --}}

        </div>

    </div>
    {{-- End Of Col_container_data - page_data_teritory --}}



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


@endpush