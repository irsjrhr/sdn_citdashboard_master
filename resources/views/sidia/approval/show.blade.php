@extends('layouts.app')

@section('titlepage', 'Approval Detail')

@section('content')
@section('navigasi')
    <span>Approval / {{ $approval->approval_no }}</span>
@endsection

<div class="card">
    <div class="card-body">

        {{-- HEADER --}}
        <h5 class="mb-1">{{ $approval->subject }}</h5>
        <p class="text-muted mb-3">{{ $approval->description }}</p>

        <hr>

        {{-- INFO --}}
        <p>
            <strong>Status:</strong>
            @include('sidia.approval.partials.status-badge', [
                'status' => $approval->status
            ])
        </p>

        <p>
            <strong>Cabang:</strong>
            {{ $approval->cabang->nama_cabang ?? '-' }}
        </p>

        <p>
            <strong>Kategori:</strong>
            {{ $approval->category->category_name ?? '-' }}
        </p>

        <hr>

        {{-- ================= ACTION APPROVAL ================= --}}
        @if(in_array($approval->status, [1,5]) && $canApprove)

            <form method="POST" id="approvalForm">
                @csrf

                {{-- COMMENT --}}
                <div class="mb-3">
                    <label class="form-label">
                        <strong>Comment</strong>
                    </label>
                    <textarea name="comment"
                              class="form-control"
                              rows="3"
                              placeholder="Isi komentar (wajib)"
                              required></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-success"
                            onclick="submitApproval('approve')">
                        Approve
                    </button>

                    <button type="button"
                            class="btn btn-danger"
                            onclick="submitApproval('reject')">
                        Reject
                    </button>

                    <button type="button"
                            class="btn btn-warning"
                            onclick="submitApproval('inquiry')">
                        Inquiry
                    </button>
                </div>
            </form>

        @else
            <span class="badge bg-secondary">
                Approval sudah selesai atau bukan giliran Anda
            </span>
        @endif

        {{-- ================= APPROVAL DETAIL & HISTORY ================= --}}
        <hr>
        <h6 class="mb-3">Approval Flow</h6>

        {{-- ===== CREATOR ===== --}}
        <div class="border rounded p-3 mb-3 bg-light">
            <div class="d-flex justify-content-between">
                <strong>
                    {{ $approval->creator->name ?? $approval->created_by }}
                    <span class="badge bg-primary ms-2">Creator</span>
                </strong>
                <small class="text-muted">
                    {{ $approval->created_at->format('d M Y H:i') }}
                </small>
            </div>

            <p class="mb-0 mt-2 text-muted">
                {{ $approval->description }}
            </p>
        </div>

        {{-- ===== APPROVER STEPS ===== --}}
        @foreach($approval->approvers->sortBy('approval_order') as $approver)
            <div class="border rounded p-3 mb-3">

                <div class="d-flex justify-content-between align-items-center">
                    <strong>
                        Step {{ $approver->approval_order }} —
                        {{ $approver->user->name ?? '-' }}

                        <span class="badge bg-secondary ms-2">
                            {{ $approver->role }}
                        </span>
                    </strong>

                    <small class="text-muted">
                        @if($approver->approved_at)
                            {{ \Carbon\Carbon::parse($approver->approved_at)->format('d M Y H:i') }}
                        @else
                            —
                        @endif
                    </small>
                </div>

                {{-- COMMENT DARI THREAD --}}
                @php
                    $thread = $approval->threads
                        ->firstWhere('approver_id', $approver->user_id);
                @endphp

                @if($thread)
                    @foreach($thread->messages->sortBy('created_at') as $message)
                        <div class="mt-2 ps-2 border-start">
                            <p class="mb-1">
                                {{ $message->message }}
                            </p>
                            <small class="text-muted">
                                {{ $message->created_at->format('d M Y H:i') }}
                            </small>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mt-2 mb-0 fst-italic">
                        Belum ada komentar
                    </p>
                @endif
            </div>
        @endforeach

    </div>
</div>
@endsection

<script>
window.submitApproval = function(action) {
    const form = document.getElementById('approvalForm');
    if (!form) {
        console.error('approvalForm not found');
        return;
    }

    let url = '';

    switch(action) {
        case 'approve':
            url = "{{ route('approval.approve', $approval) }}";
            break;
        case 'reject':
            url = "{{ route('approval.reject', $approval) }}";
            break;
        case 'inquiry':
            url = "{{ route('approval.inquiry', $approval) }}";
            break;
    }

    console.log('Submitting to:', url);
    form.action = url;
    form.submit();
}
</script>

