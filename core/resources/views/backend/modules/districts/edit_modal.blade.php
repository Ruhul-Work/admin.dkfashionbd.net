<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-edit">
  <h5 class="modal-title">Edit District</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">
  <form id="branchEditForm"
        action="{{ route('district.districts.update', $district->district_id) }}"
        method="post"
        data-ajax="true">
    @csrf
    @method('PUT') 

    <div class="row g-16">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">District Name <span class="text-danger">*</span></label>
        <input type="text" name="district_name" class="form-control radius-8" required
               value="{{ $district->district_name }}">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Bangla Name <span class="text-danger">*</span></label>
        <input type="text" name="district_bn_name" value="{{ $district->district_bn_name }}" class="form-control radius-8"
               placeholder="e.g. DHA-MAIN" >
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Division</label>
        <select name="district_division_id" id="division-dropdown" class="form-control">
    <option value="{{$current_division->id}}">{{$current_division->name}}</option>
    @foreach ($divisions as $division)
        
    <option value="{{$division->id}}">{{$division->name}}</option>
    @endforeach
 
</select>
        <div class="invalid-feedback d-block phone-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Latitude</label>
        <input type="text" name="district_lat" class="form-control radius-8" value="{{ $district->district_lat }}"
               placeholder="">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>

      </div>
        <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Longitude</label>
        <input type="text" name="district_lon" class="form-control radius-8" value="{{ $district->district_lon }}"
               placeholder="">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
        
      </div>

      <div class="col-12 mb-16">
        <label class="form-label text-sm mb-6">URL</label>
        <textarea name="district_url" class="form-control radius-8" rows="2">{{ $district->district_url }}</textarea>
        <div class="invalid-feedback d-block address-error" style="display:none"></div>
      </div>

      {{-- <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive"
                 {{ (string)old('is_active', (int)$branch->is_active) === '1' ? 'checked' : '' }}>
          <label class="form-check-label" for="branchIsActive">Enable this branch</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div> --}}

    {{-- actions --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-12 p-3">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
              data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
    </div>
  </form>
</div>
