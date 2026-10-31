<div class="modal-header py-16 px-24 border-0" data-modal-key="btype-create">
  <h5 class="modal-title">Add Type</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
  <form action="{{ route('org.btypes.store') }}" method="post" data-ajax="true">
    @csrf
    <div class="mb-16">
      <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control radius-8" required placeholder="e.g. Pharmacy">
      <div class="invalid-feedback d-block name-error" style="display:none"></div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 mt-12">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-36 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-40 py-12 radius-8">Save</button>
    </div>
  </form>
</div>
