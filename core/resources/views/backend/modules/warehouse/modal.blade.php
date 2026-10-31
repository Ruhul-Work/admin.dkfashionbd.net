<div class="modal-header py-16 px-24 border-0">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body p-24">
  <form id="warehouseForm" action="{{ $action }}" method="post" data-ajax="true">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="row">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required
               value="{{ $warehouse->name ?? '' }}">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-8">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control radius-8" required maxlength="32"
               value="{{ $warehouse->code ?? '' }}">
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>



      {{-- <div class="col-md-10 mb-16">
        <label class="form-label text-sm mb-8">Branch</label>
        <select name="branch_id" class="form-control radius-8  js-branch-select">
          @if(!empty($warehouse?->branch_id))
            <option value="{{ $warehouse->branch_id }}" selected>{{ optional($warehouse->branch)->name }}</option>
          @endif
        </select>
        <div class="invalid-feedback d-block branch_id-error" style="display:none"></div>
      </div> --}}

      <div class="col-md-10 mb-16">
        <label class="form-label text-sm mb-8">Branch</label>

        <select name="branch_id"  
                class="form-control js-s2-ajax"
                data-url="{{ route('org.branches.select2') }}"
                data-init-id="{{ $warehouse?->branch_id ?? '' }}"       
                data-init-text="{{ $warehouse?->branch?->name ?? '' }}"  
                placeholder="Select branch"></select>

        <div class="invalid-feedback d-block branch_id-error" style="display:none"></div>
    </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-8">Type</label>
        <select name="type" class="form-select radius-8">
          @php $type = $warehouse->type ?? 'store'; @endphp
          <option value="store"   @selected($type==='store')>Store</option>
          <option value="showroom"@selected($type==='showroom')>Showroom</option>
          <option value="returns" @selected($type==='returns')>Returns</option>
          <option value="virtual" @selected($type==='virtual')>Virtual</option>
        </select>
      </div>

      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-8">Contact Phone</label>
        <input type="text" name="phone" class="form-control radius-8" value="{{ $warehouse->phone ?? '' }}">
      </div>

      <div class="col-md-12 mb-16">
        <label class="form-label text-sm mb-8">Email</label>
        <input type="email" name="email" class="form-control radius-8" value="{{ $warehouse->email ?? '' }}">
      </div>

      <div class="col-12 mb-16">
        <label class="form-label text-sm mb-8">Address</label>
        <input type="text" name="address" class="form-control radius-8" value="{{ $warehouse->address ?? '' }}">
      </div>

      <div class="col-12 mb-8">
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_default" value="0">
          <input class="form-check-input" type="checkbox" name="is_default" value="1" id="whDefault"
                 {{ (string)old('is_default', (int)($warehouse->is_default ?? 0)) === '1' ? 'checked' : '' }}>
          <label class="form-check-label" for="whDefault">Default for this branch</label>
        </div>
        <div class="form-switch switch-success d-flex align-items-center gap-3 mt-2">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="whActive"
                 {{ (string)old('is_active', (int)($warehouse->is_active ?? 1)) === '1' ? 'checked' : '' }}>
          <label class="form-check-label" for="whActive">Active</label>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 mt-12">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>
            
 

<script>

  window.WarehousesIndex = {
  onLoad: function($modal){
    S2.auto($modal); // js-s2-ajax
  },
  onSaved: function(res){
    $('.AjaxDataTable').DataTable().ajax.reload(null,false);
    window.Swal && Swal.fire({icon:'success', title: res?.msg || 'Saved', timer:1000, showConfirmButton:false});
  }
};
</script>
