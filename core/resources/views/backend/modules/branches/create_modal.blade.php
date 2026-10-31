<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
  <h5 class="modal-title">Add Branch</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
  <form id="branchCreateForm" action="{{ route('org.branches.store') }}" method="post" data-ajax="true">
    @csrf
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" placeholder="e.g. Dhaka Main" required>
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control radius-8" placeholder="e.g. DHA-MAIN" required>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Phone</label>
        <input type="text" name="phone" class="form-control radius-8" placeholder="+8801XXXXXXXXX">
        <div class="invalid-feedback d-block phone-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Email</label>
        <input type="email" name="email" class="form-control radius-8" placeholder="branch@example.com">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
      </div>

      <div class="col-12 mb-20">
        <label class="form-label text-sm mb-8">Address</label>
        <textarea name="address" class="form-control radius-8" rows="2" placeholder="Street, City, ZIP"></textarea>
        <div class="invalid-feedback d-block address-error" style="display:none"></div>
      </div>

      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive" checked>
          <label class="form-check-label" for="branchIsActive">Enable this branch</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>