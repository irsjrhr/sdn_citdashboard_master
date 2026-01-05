@extends('layouts.app')

@section('titlepage', 'Notification Detail')

@section('content')
@section('navigasi')
    <span>Notifications / Detail</span>
@endsection

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Notification Detail</h5>
    </div>

    <div class="card-body">

        {{-- Status Notif --}}
        <div class="mb-3">
            <span class="badge {{ $notification->read_at ? 'bg-secondary' : 'bg-danger' }}">
                {{ $notification->read_at ? 'READ' : 'NEW' }}
            </span>

            @if($approval)
                @if($approval->status == 3)
                    <span class="badge bg-success ms-2">Sudah Disetujui</span>
                @elseif($approval->status == 4)
                    <span class="badge bg-danger ms-2">Ditolak</span>
                @elseif($approval->status == 5)
                    <span class="badge bg-warning ms-2">Inquiry</span>
                @else
                    <span class="badge bg-info ms-2">Menunggu Approval</span>
                @endif
            @endif
        </div>

        <h6 class="fw-bold mb-2">
            {{ $notification->data['message'] ?? 'Notification' }}
        </h6>

        @if($approval)
            <p class="mb-1">
                <strong>Approval No:</strong> {{ $approval->approval_no }}
            </p>
            <p class="mb-3">
                <strong>Subject:</strong> {{ $approval->subject }}
            </p>
        @endif

        <p class="text-muted">
            Diterima {{ $notification->created_at->diffForHumans() }}
        </p>

        <hr>

        <div class="d-flex gap-2">
            <a href="{{ route('notifications.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

            {{-- TOMBOL BUKA APPROVAL --}}
            @if($approval && in_array($approval->status, [1,5]))
                <a href="{{ route('approval.show', $approval->approval_no) }}"
                   class="btn btn-primary">
                    Buka Approval
                </a>
            @endif
        </div>

    </div>
</div>
@endsection
