<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
    <h5 class="modal-title">Add Supplier</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24" id="supplierCreateModal">
  <form id="supplierCreateForm"
        action="{{ route('supplier.store') }}"
        method="post"
        data-ajax="true"
        enctype="multipart/form-data">
    @csrf

    {{-- Row 1: name + slug --}}
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required id="nameInput" placeholder="e.g. Royal Publication">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control radius-8"  placeholder="">
        <div class="invalid-feedback d-block slug-error" style="display:none"></div>
      </div>
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block slug-error" style="display:none"></div>
      </div>
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Address <span class="text-danger">*</span></label>
        <input type="text" name="address" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block slug-error" style="display:none"></div>
      </div>
    </div>

    

    {{-- ✅ buttons inside form --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" id="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>

<script>
  $(document).on('click','#submit',function(){
    setTimeout(() => {
      loadSupplier();
    }, 500);
  })
</script>


