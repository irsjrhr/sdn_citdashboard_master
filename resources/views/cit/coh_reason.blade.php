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

                                    <th>Outstanding Amount Invoice</th>
                                    <th>Nilai Input FSR / Driver</th>
                                    <th>Total Confirm Collection</th>
                                    <th>Diff (Cash, TF, BG) by Value</th>
                                    <th>Unpaid Value</th>
                                    <th>Performa Coll by Value</th>
                                    <th>Diff Note</th>

                                    <th>Total Doc Invoice</th>
                                    <th>Total Collected Doc Invoice</th>
                                    <th>Performa Coll by Inv</th>

                                    <th>Total Inv TOP OD by Value</th>
                                    <th>Total Paid Inv TOP OD by Value</th>
                                    <th>Performa Coll AR OD by Value</th>
                                    <th>Total Inv TOP OD by Doc</th>
                                    <th>Paid Inv TOP OD by Doc</th>
                                    <th>Performa Coll by Doc</th>

                                    <th>COH</th>
                                    <th>Bank In</th>
                                    <th>Balance</th>
                                    <th>Reason</th>
                                    <th>Cash Bank Manual</th>
                                    <th>Payment Performance Document</th>

                                    <th>Cash Value FSR</th>
                                    <th>Cash Confirm Kasir</th>
                                    <th>Diff Cash</th>

                                    <th>TF Value FSR</th>
                                    <th>TF Confirm Kasir</th>
                                    <th>Diff TF</th>

                                    <th>Giro Value FSR</th>
                                    <th>Giro Confirm Kasir</th>
                                    <th>Diff Giro</th>
                                </tr>

                            </thead>

                            <tbody>
                                @foreach ($data_coh as $row_data)
                                <tr>
                                    <td> 
                                        @php
                                        $branchCode = $row_data['Branch Code'];
                                        $collectionDate = $row_data['Branch Name'];
                                        $url_direct = asset('cit/coh_reason_detail?branch=') . $branchCode . "&" . "collectionDate=" . $collectionDate;
                                        @endphp
                                        <a class="btn btn-primary" href="{{ $url_direct }}">

                                            View Detail 
                                            
                                        </a>
                                    </td>

                                    <td><?= $row['Branch Code'] ?></td>
                                    <td><?= $row['Branch Name'] ?></td>
                                    <td><?= $row['Collection Date'] ?></td>
                                    <td><?= $row['Transaction Type'] ?></td>

                                    <td><?= number_format($row['Outstanding Amount Invoice'],0,',','.') ?></td>
                                    <td><?= number_format($row['Nilai Input FSR / Driver'],0,',','.') ?></td>
                                    <td><?= number_format($row['Total Confirm Collection'],0,',','.') ?></td>
                                    <td><?= number_format($row['Diff (Cash, TF, BG) by Value'],0,',','.') ?></td>
                                    <td><?= number_format($row['Unpaid Value'],0,',','.') ?></td>
                                    <td><?= $row['Performa Coll by Value'] ?></td>
                                    <td><?= $row['Diff Note (Cash, TF, BG)'] ?></td>

                                    <td><?= $row['Total Doc Invoice'] ?></td>
                                    <td><?= $row['Total Collected Doc Invoice'] ?></td>
                                    <td><?= $row['Performa Coll by Inv'] ?></td>

                                    <td><?= $row['Total Inv TOP OD by Value'] ?></td>
                                    <td><?= $row['Total Paid Inv TOP OD by Value'] ?></td>
                                    <td><?= $row['Performa Coll AR OD by Value'] ?? '-' ?></td>
                                    <td><?= $row['Total Inv TOP OD by Doc'] ?></td>
                                    <td><?= $row['Paid Inv TOP OD by Doc'] ?></td>
                                    <td><?= $row['Performa Coll by Doc'] ?></td>

                                    <td><?= $row['COH'] ?></td>
                                    <td><?= $row['Bank In'] ?></td>
                                    <td><?= $row['Balance'] ?></td>
                                    <td><?= $row['Reason'] ?></td>
                                    <td><?= $row['Cash Bank Manual'] ?></td>
                                    <td><?= $row['Payment_Performance_Document'] ?></td>

                                    <td><?= number_format($row['Cash Value FSR/Driver'],0,',','.') ?></td>
                                    <td><?= $row['Cash Confirm Kasir'] ?></td>
                                    <td><?= $row['Diff Cash'] ?></td>

                                    <td><?= $row['TF Value FSR/Driver'] ?></td>
                                    <td><?= $row['TF Confirm Kasir'] ?></td>
                                    <td><?= $row['Diff TF'] ?></td>

                                    <td><?= $row['Giro Value FSR/Driver'] ?></td>
                                    <td><?= $row['Giro Confirm Kasir'] ?></td>
                                    <td><?= $row['Diff Giro'] ?></td>
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