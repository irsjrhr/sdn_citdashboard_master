@extends('layouts.app')
@section('titlepage', 'General Approval')

@section('content')
@section('navigasi')
    <span>General Approval</span>
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Approval</h5>
        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalCreateApproval">
            + Tambah Approval
        </button>
    </div>
    <div class="card-body">
        <table class="table">
            @foreach($approvals as $a)
            <tr>
                <td>{{ $a->approval_no }}</td>
                <td>{{ $a->subject }}</td>
                <td>
                    @include('sidia.approval.partials.status-badge',['status'=>$a->status])
                </td>
                <td>
                    <a href="{{ route('approval.show',$a->approval_no) }}" class="btn btn-sm btn-info">
                        Detail
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@include('sidia.approval.modal')
@endsection

@push('myscript')
<script>
let currentStep = 1;
let approvers = [];

function nextStep() {
    document.getElementById('step-1').classList.add('d-none');
    document.getElementById('step-2').classList.remove('d-none');

    document.getElementById('btnNext').classList.add('d-none');
    document.getElementById('btnBack').classList.remove('d-none');
    document.getElementById('btnSubmit').classList.remove('d-none');
}

function backStep() {
    document.getElementById('step-2').classList.add('d-none');
    document.getElementById('step-1').classList.remove('d-none');

    document.getElementById('btnBack').classList.add('d-none');
    document.getElementById('btnSubmit').classList.add('d-none');
    document.getElementById('btnNext').classList.remove('d-none');
}

function addApprover() {
    let userId   = $('#approverUser').val();
    let userName = $('#approverUser option:selected').text();
    let role     = $('#approverRole').val().trim();

    if (!userId || !role) {
    Swal.fire({
        icon: 'warning',
        title: 'Data belum lengkap',
        text: 'User dan Role wajib diisi',
        confirmButtonText: 'OK'
    });
    return;
}

    // 🔒 CEK DUPLIKAT
    let exists = approvers.some(a => a.user_id == userId);

    if (exists) {
        Swal.fire({
            icon: 'warning',
            title: 'Data approver duplikat',
            text: 'User ini sudah ditambahkan sebagai approver',
            confirmButtonText: 'OK'
        });
        return;
    }

    approvers.push({ user_id: userId, role: role });

    $('#approverTable tbody').append(`
        <tr data-user-id="${userId}">
            <td>${approvers.length}</td>
            <td>${userName}</td>
            <td>${role}</td>
            <td>
                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="removeApprover('${userId}')">
                    🗑
                </button>
            </td>
        </tr>
    `);

    // reset role input
    $('#approverRole').val('');
}

function removeApprover(userId) {
    // hapus dari array
    approvers = approvers.filter(a => a.user_id != userId);

    // hapus dari table
    $('#approverTable tbody tr[data-user-id="' + userId + '"]').remove();

    // re-numbering
    $('#approverTable tbody tr').each(function(index){
        $(this).find('td:first').text(index + 1);
    });
}
</script>

<script>
$(document).on('change', 'select[name="category_code"]', function () {

    let selectedText = $(this)
        .find('option:selected')
        .text()
        .toLowerCase();

    // 🔎 cek apakah category PPI
    let isPpi = selectedText.includes('ppi');

    if (isPpi) {
        $('#ppiTypeWrapper').removeClass('d-none');
        $('#amountWrapper').removeClass('d-none');

        // init select2 ppi (sekali saja)
        if (!$('.select-ppi').hasClass('select2-hidden-accessible')) {
            $('.select-ppi').select2({
                dropdownParent: $('#modalCreateApproval'),
                placeholder: 'Pilih PPI Type',
                allowClear: true
            });
        }
    } else {
        // hide & reset
        $('#ppiTypeWrapper').addClass('d-none');
        $('#amountWrapper').addClass('d-none');

        $('select[name="ppi_code"]').val(null).trigger('change');
        $('input[name="amount"]').val('');
    }
});
</script>

<script>
$('#modalCreateApproval').on('shown.bs.modal', function () {
    $('#ppiTypeWrapper, #amountWrapper').addClass('d-none');
    $('select[name="ppi_code"]').val(null).trigger('change');
    $('input[name="amount"]').val('');
});
</script>

<script>
$(document).on('submit', '#formCreateApproval', function(e){
    e.preventDefault();

    let $btn = $('#btnSubmit');

    // 🔒 cegah double submit
    if ($btn.prop('disabled')) {
        return;
    }

    if (approvers.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Approver belum dipilih',
            text: 'Minimal 1 approver harus ditambahkan'
        });
        return;
    }

    $btn.prop('disabled', true).text('Saving...');

    let formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('approvers', JSON.stringify(approvers));

    $.ajax({
        url: "{{ route('approval.store') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message
            }).then(() => {
                $('#modalCreateApproval').modal('hide');
                location.reload();
            });
        },
        error: function (xhr) {
            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg
            });

            // 🔓 enable lagi kalau gagal
            $btn.prop('disabled', false).text('Submit');
        }
    });
});
</script>

<script>
$('#modalCreateApproval').on('shown.bs.modal', function () {
    $('.select-branch').select2({
        dropdownParent: $('#modalCreateApproval'),
        placeholder: 'Pilih Cabang',
        allowClear: true
    });
});
</script>

<script>
$('#modalCreateApproval').on('shown.bs.modal', function () {
    $('.select-category').select2({
        dropdownParent: $('#modalCreateApproval'),
        placeholder: 'Pilih Kategori',
        allowClear: true
    });
});
</script>

<script>
$('#modalCreateApproval').on('shown.bs.modal', function () {
    $('.select-user').select2({
        dropdownParent: $('#modalCreateApproval'),
        placeholder: 'Pilih Approver',
        allowClear: true
    });
});
</script>

<script>
$('#modalCreateApproval').on('hidden.bs.modal', function () {
    // reset step
    $('#step-1').removeClass('d-none');
    $('#step-2').addClass('d-none');

    $('#btnNext').removeClass('d-none');
    $('#btnBack').addClass('d-none');
    $('#btnSubmit').addClass('d-none').prop('disabled', false).text('Submit');

    // reset form
    $('#formCreateApproval')[0].reset();

    // reset approver
    approvers = [];
    $('#approverTable tbody').empty();

    // reset select2
    $('.select2').val(null).trigger('change');

    // hide ppi
    $('#ppiTypeWrapper, #amountWrapper').addClass('d-none');
});
</script>
@endpush