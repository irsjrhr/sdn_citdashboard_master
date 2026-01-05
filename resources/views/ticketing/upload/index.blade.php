@extends('layouts.app')
@section('titlepage', 'Ticketing Upload')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center">
    <span>Ticketing Upload</span>
    <a href="{{ route('ticketing.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>
@endsection

{{-- Background Wrapper --}}
<div class="position-relative" style="min-height: 100vh; background: url('{{ asset('assets/img/backgrounds/ticketing-bg.jpg') }}') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>

    <div class="d-flex justify-content-center align-items-center position-relative z-1" style="min-height: 100vh;">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 position-relative">
                <button
                    type="button"
                    class="btn btn-sm position-absolute top-0 end-0 m-3"
                    data-bs-toggle="modal"
                    data-bs-target="#uploadHistoryModal"
                    title="Upload History"
                >
                    <i class="ti ti-history"></i>
                </button>


                <div class="card-body text-center p-5 bg-white bg-opacity-90 rounded-3">

                    <h5 class="fw-bold text-primary mb-3">
                        <i class="ti ti-upload"></i> Upload Ticketing Data
                    </h5>
                    <p class="text-muted small mb-4">
                        Upload a CSV or Excel file to import new ticket records.  
                        Accepted formats: <code>.csv</code>, <code>.xlsx</code>, <code>.xls</code>
                    </p>

                    {{-- Upload Form --}}
                    <form action="{{ route('ticketing.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        {{-- Drag & Drop Area --}}
                        <label for="fileInput" id="dropZone"
                            class="border border-2 border-dashed rounded-3 p-5 d-flex flex-column align-items-center justify-content-center text-muted bg-light bg-opacity-50"
                            style="cursor: pointer; transition: all .2s ease;">
                            <i class="ti ti-file-upload mb-2" style="font-size: 40px;"></i>
                            <span class="fw-semibold">Drag & drop your file here</span>
                            <small class="text-muted">or click to select a file</small>
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.xlsx,.xls" required>
                        </label>

                        {{-- File Preview --}}
                        <div class="table-responsive mt-4 d-none" id="previewWrapper">
                            <table class="table table-sm table-bordered align-middle small" id="previewTable"></table>
                        </div>

                        {{-- Selected File Name --}}
                        <p class="mt-3 text-secondary small" id="fileName"></p>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-center align-items-center gap-2 mt-3">
                            <a href="{{ route('download.ticketing.upload.template') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-download me-1"></i> Download Template
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="ti ti-cloud-upload me-1"></i> Upload File
                            </button>
                        </div>
                    </form>

                    {{-- Feedback --}}
                    @if(session('success'))
                        <div class="alert alert-success mt-4 small mb-0">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="alert alert-danger mt-4 small mb-0">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload History Modal -->
<div class="modal fade" id="uploadHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-history me-1"></i> Upload History
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Replace with real data --}}
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>File Name</th>
                            <th>Uploaded By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($uploadHistory as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->uploaded_date)->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $item->filename }}</td>
                                <td>{{ $item->uploaded_by }}</td>
                                <td>
                                    @php
                                        $status = $item->status ?? 'Pending';
                                        $badgeClass = match ($status) {
                                            'Success' => 'bg-success',
                                            'Failed'  => 'bg-danger',
                                            default   => 'bg-warning'
                                        };
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No upload history found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@push('myscript')
<script src="{{ asset('assets/js/pages/ticketing/ticketing-upload.js') }}"></script>
@endpush
