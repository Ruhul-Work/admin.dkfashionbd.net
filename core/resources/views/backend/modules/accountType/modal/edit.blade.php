<div class="modal-header py-16 px-24 border-0">
    <h5 class="modal-title">Edit Account Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body modal-lg p-24">
<form method="POST"
      action="{{ route('account-types.update', $type->id) }}"
      data-ajax="true">

    @csrf
    @method('POST')

    <div class="mb-3">
        <label class="form-label">Type Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ $type->name }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Code (optional)</label>
        <input type="text"
               name="code"
               class="form-control"
               value="{{ $type->code }}">
    </div>

    <div class="text-end">
        <button class="btn btn-primary">Update</button>
    </div>
</form>
</div>
