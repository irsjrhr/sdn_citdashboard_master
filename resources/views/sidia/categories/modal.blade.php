<!-- Delete Confirmation Modal -->
<div class="modal fade"
     id="confirmationModal{{ $item->category_code }}"
     tabindex="-1"
     aria-labelledby="confirmationModalLabel{{ $item->category_code }}"
     aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmationModalLabel{{ $item->category_code }}">
          Hapus {{ $title }}?
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- BODY --}}
      <div class="modal-body">
        <div class="row mb-2">
          <div class="col-5 fw-semibold">Kode Kategori</div>
          <div class="col-7">: {{ $item->category_code }}</div>
        </div>

        <div class="row">
          <div class="col-5 fw-semibold">Nama Kategori</div>
          <div class="col-7">: {{ $item->category_name ?? '-' }}</div>
        </div>

        <hr>
        <p class="text-danger mb-0">
          Data yang dihapus <strong>tidak dapat dikembalikan</strong>.
        </p>
      </div>

      {{-- FOOTER --}}
      <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary btn-sm"
                data-bs-dismiss="modal">
          <i class="ti ti-x me-1"></i> Tidak
        </button>

        <form action="{{ route('categories.destroy', $item->category_code) }}"
              method="POST">
          @csrf
          @method('DELETE')
          <button class="btn btn-danger btn-sm">
            <i class="ti ti-trash me-1"></i> Ya, Hapus
          </button>
        </form>
      </div>

    </div>
  </div>
</div>
