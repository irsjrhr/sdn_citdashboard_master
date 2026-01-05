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

{{--         <div class="title_type_data">
            Summary Per Territory 
        </div>
        --}}
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
                                    @foreach ($data_coh[0] as $key_kolom => $nilai_kolom )
                                    @php
                                    if ( $key_kolom == "TotalRows" ) {
                                        continue;
                                    }
                                    @endphp
                                    <td> {{ $key_kolom }} </td>
                                    @endforeach
                                    <td> Action </td>
                                </tr>

                            </thead>

                            <tbody>
                                @foreach ($data_coh as $row_coh)
                                <tr>
                                    @foreach ($row_coh as $key_kolom => $nilai_kolom )
                                    @php
                                    //Melakukan number formating untuk nilai yang keynya termasuk ke dalam $key_rupiah_data
                                    $nilai = $row_coh[$key_kolom];
                                    if( in_array( $key_kolom, $key_rupiah_data ) ){
                                        $nilai = number_format( $nilai );
                                    }   

                                    if ( $key_kolom == "TotalRows" ) {
                                        continue;
                                    }
                                    @endphp
                                    <td> {{ $nilai }} </td>
                                    @endforeach

                                    <td> 
                                        @php
                                        $branchCode = $row_coh['BranchCode'];
                                        @endphp
                                        <a class="btn btn-primary" href="{{ asset('cit/coh_reason_detail?branchCode=') . $branchCode }}">

                                            View Detail 
                                            
                                        </a>
                                    </td>
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