<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
  <h5 class="modal-title">Edit Product-type</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
  <form id="branchCreateForm" action="{{ route('product-type.product-types.update', $productType->id) }}" method="post" data-ajax="true" >
        @method('PUT') 
    @csrf
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" placeholder="e.g. Cloth" value="{{$productType->name}}" required>
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control radius-8" placeholder=""  value="{{$productType->code}}" required >
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Sort <span class="text-danger">*</span></label>
        <input type="number" name="sort" class="form-control radius-8" placeholder=""  value="{{$productType->sort}}">
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>



{{-- active toggle button --}}
      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive" {{($productType->is_active?'checked':'')}}>
          <label class="form-check-label" for="branchIsActive">Enable this Product-type</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
    </div>
  </form>
</div>

{{-- <script>
document.getElementById('nameInput').addEventListener('input', function() {
    document.getElementById('slugInput').value = slugify(this.value);
});

 $("#icon").spartanMultiImagePicker({
            fieldName: 'icon',
            maxCount: 1,
            rowHeight: '200px',
            groupClassName: 'col',
            maxFileSize: '',
            dropFileLabel: "Drop Here",
            onExtensionErr: function(index, file) {
                console.log(index, file, 'extension err');
                alert('Please only input png or jpg type file')
            },
            onSizeErr: function(index, file) {
                console.log(index, file, 'file size too big');
                alert('File size too big max:250KB');
            }
        });

        $("#meta_image").spartanMultiImagePicker({
            fieldName: 'meta_image',
            maxCount: 1,
            rowHeight: '200px',
            groupClassName: 'col',
            maxFileSize: '',
            dropFileLabel: "Drop Here",
            onExtensionErr: function(index, file) {
                console.log(index, file, 'extension err');
                alert('Please only input png or jpg type file')
            },
            onSizeErr: function(index, file) {
                console.log(index, file, 'file size too big');
                alert('File size too big max:250KB');
            }
        });

</script> --}}