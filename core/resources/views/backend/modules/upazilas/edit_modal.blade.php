<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-edit">
  <h5 class="modal-title">Edit Branch</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">
  <form id="branchEditForm"
        action="{{ route('upazila.upazilas.update', $upazila->id) }}"
        method="post"
        data-ajax="true">
    @csrf
    @method('PUT') 

    <div class="row g-16">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Upazila Name <span class="text-danger">*</span></label>
        <input type="text" name="upazila_name" class="form-control radius-8" required
               value="{{ $upazila->upazila_name }}">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Bangla Name <span class="text-danger">*</span></label>
        <input type="text" name="upazila_bn_name" value="{{ $upazila->upazila_bn_name }}" class="form-control radius-8"
               placeholder="e.g. DHA-MAIN" >
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">District</label>
            <select name="upazila_district_id" id="division-dropdown" class="form-control">
    <option value="{{$current_district->district_id}}" selected>{{$current_district->district_name}}</option>
    @foreach ($districts as $district)
        
    <option value="{{$district->district_id}}">{{$district->district_name}}</option>
    @endforeach
 
</select>
        <div class="invalid-feedback d-block phone-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">URL</label>
        <input type="text" name="upazila_url" class="form-control radius-8" value="{{ $upazila->url }}"
               placeholder="example.com">
        <div class="invalid-feedback d-block email-error" style="display:none"></div>
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
