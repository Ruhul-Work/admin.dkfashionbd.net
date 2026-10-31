<div class="modal-header py-16 px-24 border-0">
  <h5 class="modal-title">Add Fiscal Year</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
  <form id="fiscalYearCreateForm" action="{{ route('fiscal-years.store') }}" method="post" data-ajax="true">
    @csrf
    <div class="row">
      <div class="col-md-12 mb-20">
        <label class="form-label text-sm mb-8">Fiscal Year Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" placeholder="e.g. FY 2025-26" required>
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Start Date <span class="text-danger">*</span></label>
        <input type="date" name="start_date" class="form-control radius-8" required>
        <div class="invalid-feedback d-block start_date-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">End Date <span class="text-danger">*</span></label>
        <input type="date" name="end_date" class="form-control radius-8" required>
        <div class="invalid-feedback d-block end_date-error" style="display:none"></div>
      </div>

      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="fiscalYearIsActive" checked>
          <label class="form-check-label" for="fiscalYearIsActive">Enable this fiscal year</label>
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
