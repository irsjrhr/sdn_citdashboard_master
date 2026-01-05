@extends('layouts.app')
@section('titlepage', 'Activity Types')

@section('content')
@section('navigasi')
    <span>Activity Types</span>
@endsection

<div class="row">
    <div class="col-lg-8 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btnCreateActivityType">
                    <i class="fa fa-plus me-2"></i> Tambah Activity Type
                </a>
            </div>
            <div class="card-body">
                {{-- Filter --}}
                <div class="row mb-2">
                    <div class="col-12">
                        <form action="{{ route('activitytypes.index') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-8 col-sm-12 col-md-12">
                                    <x-input-with-icon
                                        label="Search Name"
                                        value="{{ request('name') }}"
                                        name="name"
                                        icon="ti ti-search" />
                                </div>
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <button class="btn btn-primary mt-3 mt-lg-4">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="row">
                    <div class="col-12">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive mb-2">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 60px;">No.</th>
                                        <th>Name</th>
                                        <th style="width: 160px;">Total Activities</th>
                                        <th style="width: 80px;">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activityTypes as $type)
                                        <tr>
                                            <td>{{ $loop->iteration + $activityTypes->firstItem() - 1 }}</td>
                                            <td>{{ $type->name }}</td>
                                            <td>{{ $type->activities_count }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @if ($type->activities_count == 0)
                                                        {{-- EDIT & DELETE aktif kalau belum dipakai --}}
                                                        <div>
                                                            <a href="#"
                                                               class="me-2 editActivityType"
                                                               data-id="{{ $type->id }}">
                                                                <i class="fa fa-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                        <div>
                                                            <form method="POST"
                                                                  class="deleteform"
                                                                  action="{{ route('activitytypes.destroy', $type->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                {{-- pakai class delete-confirm supaya ikut script global --}}
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="fa fa-trash-alt text-danger"></i>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    @else
                                                        {{-- EDIT & DELETE disabled kalau sudah dipakai --}}
                                                        <div class="me-2" title="Sudah dipakai di activities, tidak bisa diubah">
                                                            <i class="fa fa-edit text-muted"></i>
                                                        </div>
                                                        <div title="Sudah dipakai di activities, tidak bisa dihapus">
                                                            <i class="fa fa-trash-alt text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="float: right;">
                            {{ $activityTypes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal create & edit --}}
<x-modal-form id="mdlCreateActivityType" size="" show="loadCreateActivityType" title="Tambah Activity Type" />
<x-modal-form id="mdlEditActivityType" size="" show="loadEditActivityType" title="Edit Activity Type" />

@endsection

@push('myscript')
<script>
    $(function() {
        // Buka modal create
        $("#btnCreateActivityType").click(function(e) {
            e.preventDefault();
            $('#mdlCreateActivityType').modal("show");
            $("#loadCreateActivityType").load("{{ route('activitytypes.create') }}");
        });

        // Buka modal edit
        $(".editActivityType").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $('#mdlEditActivityType').modal("show");
            $("#loadEditActivityType").load("/activity-types/" + id + "/edit");
        });

        // PERHATIAN:
        // Tidak ada handler delete di sini.
        // Untuk konfirmasi & submit delete, gunakan script global
        // yang sudah ada di layout untuk .delete-confirm / .deleteform.
    });
</script>
@endpush
