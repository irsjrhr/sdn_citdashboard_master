<form action="{{ route('activitytypes.store') }}" method="POST" id="formCreateActivityType">
    @csrf
    <div class="mb-3">
        <label for="name_create" class="form-label">Activity Type Name</label>
        <input type="text"
               name="name"
               id="name_create"
               class="form-control"
               required
               autocomplete="off">
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
    </div>
</form>
