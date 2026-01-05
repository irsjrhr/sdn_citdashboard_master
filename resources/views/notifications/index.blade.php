@extends('layouts.app')

@section('titlepage', 'Notifications')

@section('content')
@section('navigasi')
    <span>Notifications</span>
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Notification List</h5>

        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-primary">
                Tandai semua dibaca
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        <ul class="list-group list-group-flush">

            @forelse($notifications as $notif)
                <li class="list-group-item
                    {{ is_null($notif->read_at) ? 'bg-light' : '' }}">

                    <a href="{{ route('notifications.show', $notif->id) }}"
                       class="d-flex text-decoration-none text-dark">

                        <div class="me-3">
                            <span class="badge
                                {{ is_null($notif->read_at) ? 'bg-danger' : 'bg-secondary' }}">
                                {{ is_null($notif->read_at) ? 'NEW' : 'READ' }}
                            </span>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $notif->data['approval_no'] ?? '-' }}
                            </div>

                            <div>
                                {{ $notif->data['subject'] ?? 'Tanpa Subject' }}
                            </div>

                            <small class="text-muted">
                                {{ $notif->data['message'] ?? '' }}
                                • {{ $notif->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </a>
                </li>
            @empty
                <li class="list-group-item text-center text-muted py-4">
                    Tidak ada notifikasi
                </li>
            @endforelse

        </ul>
    </div>
</div>
@endsection
