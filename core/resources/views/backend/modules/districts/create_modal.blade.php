<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
  <h5 class="modal-title">Add District</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
  <form id="branchCreateForm" action="{{ route('district.districts.store') }}" method="post" data-ajax="true">
    @csrf
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">District Name <span class="text-danger">*</span></label>
        <input type="text" name="district_name" class="form-control radius-8" placeholder="e.g. Dhaka " required>
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Bangla Name <span class="text-danger">*</span></label>
        <input type="text" name="district_bn_name" class="form-control radius-8" placeholder="e.g. DHA-MAIN" required>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Division</label>
    <select name="district_division_id" id="division-dropdown" class="form-control">
    <option value="">-- Select Division --</option>
    @foreach ($divisions as $division)
        
    <option value="{{$division->id}}">{{$division->name}}</option>
    @endforeach
 
</select>

        <div class="invalid-feedback d-block phone-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Latitude</label>
        <input type="text" name="district_lat" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
      </div>

       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Longitude</label>
        <input type="text" name="district_lon" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">URL</label>
        <input type="text" name="district_url" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
      </div>

      {{-- <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive" checked>
          <label class="form-check-label" for="branchIsActive">Enable this branch</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div> --}}

    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>