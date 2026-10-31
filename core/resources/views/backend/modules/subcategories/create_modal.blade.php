<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
  <h5 class="modal-title">Add Sub-Category</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">
  
  <form id="branchEditForm"
        action="{{ route('subcategory.subcategories.store') }}"
        method="post"
        data-ajax="true">
    @csrf
  

    <div class="row g-16">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required
               value=""  id="nameInputSubCat">
        <div class="invalid-feedback d-block name-error" style="display:none">

        </div>
      </div>

       <div class="col-md-6 mb-16">
         <label class="form-label text-sm mb-8">Category</label>
    <select name="category_id" id="division-dropdown" class="form-control">
    <option value="">--Select Category--</option>
    @foreach ($categories as $category)
        
    <option value="{{$category->id}}">{{$category->name}}</option>
    @endforeach
  </select>
      </div>

      <div class="col-md-6 mb-20 mt-10">
        <label class="form-label text-sm mb-8">Slug <span class="text-danger">*</span></label>
        <input type="text" name="slug" value="" class="form-control radius-8"
               placeholder="e.g. DHA-MAIN" required id="slugInputSubCat">
        <div class="invalid-feedback d-block code-error" style="display:none"></div>

      </div> 

     
      <hr class="col-md-11 my-3 m-auto">

      {{-- image input div --}}
      <div class="row">
         <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Icon Image</label>
        <div id="icon"></div>
        {{-- <input type="file" name="icon" class="form-control radius-8 p-4" placeholder="" required > --}}
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Image</label>
        <div id="meta_image"></div>
        {{-- <input type="file" name="meta_image" class="form-control radius-8 p-4" placeholder="" required > --}}
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      </div>

      {{-- meta info --}}
       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Title </label>
        <input type="text" name="meta_title" class="form-control radius-8" placeholder="e.g. Dhaka Main"  >
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>


       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Keywords </label>
        <input type="text" name="meta_keywords" class="form-control radius-8" placeholder=""  >
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

       <div class="col-md-12 mb-20">
        <label class="form-label text-sm mb-8">Meta description </label>
        <textarea type="textarea" name="meta_description" rows="3" class="form-control radius-8" placeholder=""  ></textarea>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

{{-- active toggle button --}}
      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive" checked>
          <label class="form-check-label" for="branchIsActive">Enable this category</label>
        </div>

        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>

    </div>
    

    {{-- actions --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-12 p-3">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
              data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>

<script>
document.getElementById('nameInputSubCat').addEventListener('input', function() {
    document.getElementById('slugInputSubCat').value = slugify(this.value);
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
</script>
