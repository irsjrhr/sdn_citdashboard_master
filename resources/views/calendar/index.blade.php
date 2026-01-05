@extends('layouts.app')
@section('titlepage', 'Activity Calendar')

@section('content')
@section('navigasi')
    <span>Activity Calendar</span>
@endsection

<div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch">
        <input class="form-check-input"
               type="checkbox"
               id="filterMyOnly"
               checked>
        <label class="form-check-label" for="filterMyOnly">
            Only My Calendar
        </label>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label mb-1">Filter Jabatan</label>
        <select id="filterJabatan" class="form-select form-select-sm">
            <option value="">-- Semua Jabatan --</option>
            @foreach ($jabatanList as $jabatan)
                <option value="{{ $jabatan->kode_jabatan }}">
                    {{ $jabatan->nama_jabatan }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label mb-1">Filter Karyawan</label>
        <select id="filterKaryawan" class="form-select form-select-sm">
            <option value="">-- Semua Karyawan --</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-2"></div>

            <div class="card-body">
                {{-- FullCalendar CSS --}}
                <link rel="stylesheet"
                      href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

                <style>
                    #calendar {
                        max-width: 1200px;
                        margin: 0 auto;
                    }

                    .fc {
                        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                        font-size: 12px;
                    }

                    .fc-header-toolbar {
                        margin-bottom: 10px !important;
                    }

                    .fc-toolbar-chunk .fc-button {
                        border-radius: 4px !important;
                        border: none;
                        box-shadow: none;
                        padding: 4px 10px;
                        font-size: 11px;
                    }

                    .fc-toolbar-chunk .fc-button-primary {
                        background: #f3f2f1;
                        color: #323130;
                    }

                    .fc-toolbar-chunk .fc-button-primary:not(:disabled):hover {
                        background: #e1dfdd;
                    }

                    .fc-toolbar-chunk .fc-button-primary.fc-button-active {
                        background: #0078d4;
                        color: #fff;
                    }

                    .fc-toolbar-title {
                        font-size: 18px;
                        font-weight: 500;
                        color: #323130;
                    }

                    .fc-theme-standard .fc-scrollgrid {
                        border: 1px solid #e1dfdd;
                    }

                    .fc-theme-standard td,
                    .fc-theme-standard th {
                        border: 1px solid #e1dfdd;
                    }

                    .fc-col-header-cell-cushion {
                        padding: 4px 0;
                        font-size: 11px;
                        color: #605e5c;
                    }

                    .fc-daygrid-day-number {
                        font-size: 11px;
                        padding: 3px 4px;
                        color: #605e5c;
                    }

                    .fc-day-sun .fc-daygrid-day-frame {
                        background-color: #ffe3e3 !important;
                    }

                    .fc-day-today .fc-daygrid-day-frame {
                        background-color: #fff8c5 !important;
                    }

                    .fc-daygrid-event {
                        border-radius: 2px;
                        padding: 0 2px;
                        font-size: 11px;
                        border: none;
                    }

                    .fc-daygrid-event-dot {
                        display: none;
                    }

                    .fc-event-main {
                        padding: 1px 2px;
                    }
                </style>

                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: NEW ACTIVITY --}}
<div class="modal fade" id="createActivityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <form class="modal-content" method="POST" action="{{ route('activities.store') }}">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">New Activity</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Title</label>
                <input name="title" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Activity Type</label>
                <select name="activity_type_id" class="form-select form-select-sm" required>
                    <option value="">Pilih Activity Type</option>
                    @foreach ($activityTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="is_focus_create" name="is_focus" value="1">
                <label class="form-check-label" for="is_focus_create">Focus activity</label>
            </div>

            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Start</label>
                <input name="start" type="datetime-local" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label">End</label>
                <input name="end" type="datetime-local" class="form-control form-control-sm">
            </div>

            <div class="mb-3">
                <label>Repetition</label>
                <select name="repeat_type" class="form-select">
                    <option value="">Tidak diulang</option>
                    <option value="daily">Harian (sampai akhir minggu)</option>
                    <option value="weekly">Mingguan (sampai akhir bulan)</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Location</label>
                <input name="location" class="form-control form-control-sm">
            </div>
        </div>

        <div class="modal-footer py-2">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
    </form>
  </div>
</div>

{{-- MODAL: EDIT + REALISASI --}}
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="editActivityForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Activity</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="id">

            <div class="mb-2">
                <label class="form-label">Title</label>
                <input name="title" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Activity Type</label>
                <select name="activity_type_id" class="form-select form-select-sm" required>
                    <option value="">Pilih Activity Type</option>
                    @foreach ($activityTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="is_focus_edit" name="is_focus" value="1">
                <label class="form-check-label" for="is_focus_edit">Focus activity</label>
            </div>

            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Start</label>
                <input name="start" type="datetime-local" class="form-control form-control-sm" required>
            </div>

            <div class="mb-2">
                <label class="form-label">End</label>
                <input name="end" type="datetime-local" class="form-control form-control-sm">
            </div>

            <div class="mb-2">
                <label class="form-label">Location</label>
                <input name="location" class="form-control form-control-sm">
            </div>

            <hr>

            <h6>Realisasi</h6>

            <div class="mb-2">
                <label class="form-label">Status Realisasi</label>
                <select name="realization_status" id="realization_status" class="form-select form-select-sm">
                    <option value="">Belum diisi</option>
                    <option value="realized">Terealisasi</option>
                    <option value="not_realized">Tidak terealisasi</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label" id="label_realization_note">Keterangan</label>
                <textarea name="realization_note" id="realization_note" class="form-control form-control-sm" rows="2"></textarea>
                <small class="text-muted" id="realization_hint">
                    Isi keterangan realisasi / alasan jika mengubah status realisasi.
                </small>
            </div>

        </div>

        <div class="modal-footer justify-content-between py-2">
            <button type="button" id="btnDeleteActivity" class="btn btn-sm btn-outline-danger">
                Delete
            </button>
            <div>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </div>
        </div>
      </form>

      <form id="deleteActivityForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
      </form>
    </div>
  </div>
</div>

@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
/* =========================
   GLOBAL MODAL CLEANUP
   ========================= */
document.addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl    = document.getElementById('calendar');
    const createModalEl = document.getElementById('createActivityModal');
    const editModalEl   = document.getElementById('editActivityModal');

    const createModal = new bootstrap.Modal(createModalEl);

    editModalEl.addEventListener('hidden.bs.modal', function () {
        const instance = bootstrap.Modal.getInstance(editModalEl);
        if (instance) {
            instance.dispose();
        }
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'id',
        slotMinTime: '06:00:00',
        slotMaxTime: '23:00:00',
        scrollTime: '06:00:00',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            const jabatan  = document.getElementById('filterJabatan').value;
            const karyawan = document.getElementById('filterKaryawan').value;
            const myOnly   = document.getElementById('filterMyOnly').checked ? 1 : 0;

            fetch("{{ route('activities.index') }}?" + new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
                jabatan: jabatan,
                karyawan: karyawan,
                my_only: myOnly
            }))
            .then(res => res.json())
            .then(data => successCallback(data))
            .catch(err => failureCallback(err));
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

        eventContent: function(arg) {
            const props = arg.event.extendedProps;

            // selain list → default
            if (!arg.view.type.startsWith('list')) {
                return { html: arg.event.title || '' };
            }

            const title = arg.event.title || '';
            const desc  = props.description || '';
            const loc   = props.location || '';

            const status = props.realizationStatus || '';
            const note   = props.realizationNote || '';
            const atIso  = props.realizationAt || '';

            // ===== LEFT SIDE =====
            let leftHtml = `<div><strong>${title}</strong></div>`;

            if (desc) {
                leftHtml += `<div style="font-size:11px;color:#605e5c;">${desc.replace(/\r?\n/g, '<br>')}</div>`;
            }

            if (loc) {
                leftHtml += `<div style="font-size:11px;color:#605e5c;">@ ${loc}</div>`;
            }

            // ===== RIGHT SIDE (REALISASI) =====
            let rightHtml = '';

            if (status) {
                let statusLabel = '';
                let statusColor = '';

                if (status === 'realized') {
                    statusLabel = 'Terealisasi';
                    statusColor = '#107c10';
                } else if (status === 'not_realized') {
                    statusLabel = 'Tidak terealisasi';
                    statusColor = '#a80000';
                }

                let atText = '';
                if (atIso) {
                    const d = new Date(atIso);
                    const pad = n => n.toString().padStart(2, '0');
                    atText =
                        pad(d.getDate()) + '/' +
                        pad(d.getMonth() + 1) + '/' +
                        d.getFullYear() + ' ' +
                        pad(d.getHours()) + ':' +
                        pad(d.getMinutes());
                }

                rightHtml += `
                    <div style="text-align:right;font-size:11px;">
                        <div style="color:${statusColor};font-weight:500;">
                            ${statusLabel}
                        </div>
                        <div style="color:#605e5c;">
                            ${atText}
                        </div>
                    </div>
                `;

                if (note) {
                    rightHtml += `
                        <div style="margin-top:4px;font-size:11px;color:#605e5c;text-align:right;">
                            ${note.replace(/\r?\n/g, '<br>')}
                        </div>
                    `;
                }
            }

            // ===== FINAL WRAPPER =====
            const html = `
                <div style="display:flex;justify-content:space-between;gap:16px;">
                    <div style="flex:1;">
                        ${leftHtml}
                    </div>
                    <div style="min-width:160px;">
                        ${rightHtml}
                    </div>
                </div>
            `;

            return { html };
        },


        dateClick: function(info) {
            if (calendar.view.type === 'dayGridMonth') {
                return;
            }

            resetCreateForm();

            const startInput = createModalEl.querySelector('input[name="start"]');
            startInput.value = info.dateStr + 'T09:00';

            createModal.show();
        },

        eventClick: function(info) {
            setLockedState(false);

            const event = info.event;
            const props = event.extendedProps;
            const isOwner = props.isOwner === true;

            const editForm   = document.getElementById('editActivityForm');
            const deleteForm = document.getElementById('deleteActivityForm');
            const updateUrl  = '/activities/' + event.id;

            editForm.action   = updateUrl;
            deleteForm.action = updateUrl;

            editModalEl.querySelector('input[name="id"]').value = event.id;
            editModalEl.querySelector('input[name="title"]').value = event.title || '';
            editModalEl.querySelector('textarea[name="description"]').value = props.description || '';
            editModalEl.querySelector('input[name="location"]').value = props.location || '';

            const typeSelect = editModalEl.querySelector('select[name="activity_type_id"]');
            typeSelect.value = props.activityTypeId || '';

            const focusCheck = document.getElementById('is_focus_edit');
            focusCheck.checked = !!props.isFocus;

            const statusSelect = document.getElementById('realization_status');
            const noteTextarea = document.getElementById('realization_note');

            statusSelect.value = props.realizationStatus || '';
            noteTextarea.value = props.realizationNote || '';

            updateRealizationUI();

            function isoToLocal(iso) {
                if (!iso) return '';
                const d = new Date(iso);
                const pad = n => n.toString().padStart(2, '0');
                return d.getFullYear() + '-' +
                       pad(d.getMonth()+1) + '-' +
                       pad(d.getDate()) + 'T' +
                       pad(d.getHours()) + ':' +
                       pad(d.getMinutes());
            }

            editModalEl.querySelector('input[name="start"]').value = isoToLocal(event.start);
            editModalEl.querySelector('input[name="end"]').value   = isoToLocal(event.end);

            const alreadyRealized = !!props.realizationStatus;
            // setLockedState(alreadyRealized);
            setLockedState(!isOwner || alreadyRealized);

            document.getElementById('btnDeleteActivity').onclick = function () {
                if (!isOwner) {
                    alert('Anda tidak berhak menghapus aktivitas ini.');
                    return;
                }
                if (alreadyRealized) return;
                if (confirm('Delete this activity?')) {
                    deleteForm.submit();
                }
            };

            // 🔥 DISPOSE dulu kalau ada instance lama
            const oldInstance = bootstrap.Modal.getInstance(editModalEl);
            if (oldInstance) {
                oldInstance.dispose();
            }

            // 🔥 BUAT INSTANCE BARU (fresh)
            const editModal = new bootstrap.Modal(editModalEl, {
                backdrop: true,
                keyboard: true
            });

            editModal.show();
        }
    });

    calendar.render();

    // ===== Toggle: Only My Calendar =====
    const filterMyOnly = document.getElementById('filterMyOnly');

    if (filterMyOnly && !filterMyOnly.dataset.bound) {
        filterMyOnly.dataset.bound = '1';

        filterMyOnly.addEventListener('change', function () {
            reloadCalendar(calendar);
        });
    }

    function reloadCalendar(calendar) {
        // 🔥 tutup modal kalau masih kebuka
        document.querySelectorAll('.modal.show').forEach(modalEl => {
            const instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();
        });

        calendar.removeAllEvents();
        calendar.refetchEvents();
    }

    function resetCreateForm() {
        createModalEl.querySelector('form').reset();
        const typeSelect = createModalEl.querySelector('select[name="activity_type_id"]');
        if (typeSelect) {
            typeSelect.value = '';
        }
        document.getElementById('is_focus_create').checked = false;
    }

    function updateRealizationUI() {
        const statusSelect = document.getElementById('realization_status');
        const label        = document.getElementById('label_realization_note');
        const hint         = document.getElementById('realization_hint');

        if (statusSelect.value === 'realized') {
            label.textContent = 'Keterangan realisasi';
            hint.textContent  = 'Jelaskan hasil realisasi (apa yang dikerjakan).';
        } else if (statusSelect.value === 'not_realized') {
            label.textContent = 'Alasan tidak terealisasi';
            hint.textContent  = 'Jelaskan alasan aktivitas tidak terealisasi.';
        } else {
            label.textContent = 'Keterangan';
            hint.textContent  = 'Isi keterangan realisasi / alasan jika mengubah status realisasi.';
        }
    }

    function setLockedState(locked) {
        const selectors = [
            'input[name="title"]',
            'select[name="activity_type_id"]',
            '#is_focus_edit',
            'textarea[name="description"]',
            'input[name="start"]',
            'input[name="end"]',
            'input[name="location"]',
            '#realization_status',
            '#realization_note'
        ];

        selectors.forEach(selector => {
            const els = editModalEl.querySelectorAll(selector);
            els.forEach(el => {
                if (locked) {
                    el.setAttribute('disabled', 'disabled');
                } else {
                    el.removeAttribute('disabled');
                }
            });
        });

        const btnUpdate = editModalEl.querySelector('button[type="submit"]');
        const btnDelete = document.getElementById('btnDeleteActivity');

        if (locked) {
            if (btnUpdate) btnUpdate.classList.add('d-none');
            if (btnDelete) btnDelete.classList.add('d-none');
        } else {
            if (btnUpdate) btnUpdate.classList.remove('d-none');
            if (btnDelete) btnDelete.classList.remove('d-none');
        }
    }

    editModalEl.querySelector('#realization_status').addEventListener('change', updateRealizationUI);

    const filterJabatan = document.getElementById('filterJabatan');
    const filterKaryawan = document.getElementById('filterKaryawan');

    if (!filterJabatan.dataset.bound) {
        filterJabatan.dataset.bound = '1';

        filterJabatan.addEventListener('change', function () {
            const jabatan = this.value;
            const karyawanSelect = document.getElementById('filterKaryawan');

            // 🔥 reset TOTAL
            karyawanSelect.innerHTML = '<option value="">-- Semua Karyawan --</option>';

            if (!jabatan) {
                reloadCalendar(calendar);
                return;
            }

            fetch('/ajax/karyawan-by-jabatan?jabatan=' + jabatan)
                .then(res => res.json())
                .then(data => {
                    data.forEach(row => {
                        const opt = document.createElement('option');
                        opt.value = row.nik;
                        opt.textContent = row.nama;
                        karyawanSelect.appendChild(opt);
                    });
                });

            reloadCalendar(calendar);
        });
    }
    if (!filterKaryawan.dataset.bound) {
    filterKaryawan.dataset.bound = '1';

    filterKaryawan.addEventListener('change', function () {
        console.log('Karyawan dipilih:', this.value); // debug
        reloadCalendar(calendar);
    });
}
});
</script>
@endpush
