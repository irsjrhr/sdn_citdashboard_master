<form action="{{ route('activitytypes.update', $activityType->id) }}"
      method="POST"
      id="formEditActivityType">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name_edit" class="form-label">Activity Type Name</label>
        <input type="text"
               name="name"
               id="name_edit"
               class="form-control"
               value="{{ $activityType->name }}"
               required
               autocomplete="off">
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
    </div>
</form>
