<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-edit">
  <h5 class="modal-title">Edit Branch</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">
  <form id="branchEditForm"
        action="{{ route('org.branches.update', $branch->id) }}"
        method="post"
        data-ajax="true">
    @csrf
    @method('PUT') 

    <div class="row g-16">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required
               value="{{ $branch->name }}">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" value="{{ $branch->code }}" class="form-control radius-8"
               placeholder="e.g. DHA-MAIN" required>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Phone</label>
        <input type="text" name="phone" class="form-control radius-8" value="{{ $branch->phone }}">
        <div class="invalid-feedback d-block phone-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Email</label>
        <input type="email" name="email" class="form-control radius-8" value="{{ $branch->email }}"
               placeholder="branch@example.com">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
      </div>

      <div class="col-12 mb-16">
        <label class="form-label text-sm mb-6">Address</label>
        <textarea name="address" class="form-control radius-8" rows="2">{{ $branch->address }}</textarea>
        <div class="invalid-feedback d-block address-error" style="display:none"></div>
      </div>

      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive"
                 {{ (string)old('is_active', (int)$branch->is_active) === '1' ? 'checked' : '' }}>
          <label class="form-check-label" for="branchIsActive">Enable this branch</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div>

    {{-- actions --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-12 p-3">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
              data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
    </div>
  </form>
</div>
