<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-edit">
  <h5 class="modal-title">Edit Subcategory</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">

  <form id="branchEditForm"
        action="{{ route('subcategory.subcategories.update', $subcategory->id) }}"
        method="post"
        data-ajax="true">
    @csrf
    @method('PUT') 

    <div class="row g-16">
      <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required
               value="{{ $subcategory->name }}" id="nameInputSubCat">
        <div class="invalid-feedback d-block name-error" style="display:none">

        </div>
      </div>

       <div class="col-md-6 mb-16">
         <label class="form-label text-sm mb-8">Category</label>
    <select name="category_id" id="division-dropdown" class="form-control">
    <option value="{{$current_category->id}}">{{$current_category->name}}</option>
    @foreach ($categories as $category)
        
    <option value="{{$category->id}}">{{$category->name}}</option>
    @endforeach
  </select>
      </div>

      <div class="col-md-6 mb-20 mt-10">
        <label class="form-label text-sm mb-8">Slug <span class="text-danger">*</span></label>
        <input type="text" name="slug" value="{{ $subcategory->slug }}" class="form-control radius-8"
               placeholder="e.g. DHA-MAIN" required id="slugInputSubCat">
        <div class="invalid-feedback d-block code-error" style="display:none"></div>

        
      </div>

      
      <hr class="col-md-11 my-3 m-auto">

      {{-- image input div --}}
      <div class="row">
         <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Icon Image</label>
        <div id="icon"></div>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Image</label>
        <div id="meta_image"></div>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>

      </div>
       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Title </label>
        <input type="text" name="meta_title" class="form-control radius-8" value="{{$subcategory->meta_title?$subcategory->meta_title:''}}">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Keywords </label>
        <input type="text" name="meta_keywords" class="form-control radius-8" value="{{$subcategory->meta_title?$subcategory->meta_keywords:''}}" >
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>
       
       <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta description </label>
        <textarea type="textarea" name="meta_description" rows="5" class="form-control radius-8"  >{{$subcategory->meta_title?$subcategory->meta_description:''}}</textarea>
        <div class="invalid-feedback d-block code-error" style="display:none"></div>
      </div>


      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="branchIsActive"
                 {{ (string)old('is_active', (int)$subcategory->is_active) === '1' ? 'checked' : '' }}>
          <label class="form-check-label" for="branchIsActive">Enable this category</label>
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
<script>
document.getElementById('nameInputSubCat').addEventListener('input', function() {
    document.getElementById('slugInputSubCat').value = slugify(this.value);
});


 @if(!empty($subcategory->icon)) 


    // Add it manually
    $("#icon").append(
        '<div class="img_" style="position:relative; display:inline-block; margin:5px;">' +
            '<img src="{{ image($subcategory->icon) }}" class="img-responsive" style="height:200px; width:auto;">' +
            '</div>'
    );
@endif

 @if(!empty($subcategory->meta_image)) 


    // Add it manually
    $("#meta_image").append(
        '<div class="img_" style="position:relative; display:inline-block; margin:5px;">' +
            '<img src="{{ image($subcategory->meta_image) }}" class="img-responsive" style="height:200px; width:auto;">' +
            '</div>'
    );
@endif


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