<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
    <h5 class="modal-title">Add Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24" id="categoryCreateModal">
  <form id="categoryCreateForm"
        action="{{ route('category.categories.store') }}"
        method="post"
        data-ajax="true"
        enctype="multipart/form-data">
    @csrf

    {{-- Row 1: name + slug --}}
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control radius-8" required id="nameInput" placeholder="e.g. Dhaka Main">
        <div class="invalid-feedback d-block name-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Slug <span class="text-danger">*</span></label>
        <input type="text" name="slug" class="form-control radius-8" required id="slugInput" placeholder="">
        <div class="invalid-feedback d-block slug-error" style="display:none"></div>
      </div>
    </div>

    {{-- Row 2: category type + meta title --}}
    <div class="row">
      {{-- <div class="col-md-6 mb-16">
        <label class="form-label text-sm mb-8">Category Type</label>
        <select name="category_type_id" id="categoryTypeSelect" class="form-control js-category-type-select"></select>
        <div class="invalid-feedback d-block category_type_id-error" style="display:none"></div>
      </div> --}}

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Title</label>
        <input type="text" name="meta_title" class="form-control radius-8" placeholder="e.g. Dhaka Main">
        <div class="invalid-feedback d-block meta_title-error" style="display:none"></div>
      </div>
    </div>

    {{-- Row 3: images --}}
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Icon Image</label>
        <div id="icon"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Image</label>
        <div id="meta_image"></div>
      </div>
    </div>

    {{-- Row 4: meta keywords + description --}}
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta Keywords</label>
        <input type="text" name="meta_keywords" class="form-control radius-8" placeholder="">
        <div class="invalid-feedback d-block meta_keywords-error" style="display:none"></div>
      </div>

      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Meta description</label>
        <textarea name="meta_description" rows="5" class="form-control radius-8" placeholder=""></textarea>
        <div class="invalid-feedback d-block meta_description-error" style="display:none"></div>
      </div>
    </div>

    <hr class="my-3">

    {{-- Row 5: active toggle --}}
    <div class="row">
      <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="categoryIsActive" checked>
          <label class="form-check-label" for="categoryIsActive">Enable this category</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div>

    {{-- ✅ buttons inside form --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>


{{-- slugify --}}

<script>
    document.getElementById('nameInput').addEventListener('input', function() {
        document.getElementById('slugInput').value = slugify(this.value);
    });



// select2 init function
 function initCategorySelect($el, $modal) {
    // prevent double init
    if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');

    $el.select2({
      dropdownParent: $modal,
      placeholder: 'Select category type',
      allowClear: true,
      width: '100%',
      ajax: {
        url: "{{ route('category.types.select2')}}",
        dataType: 'json',
        delay: 200,
        data: params => ({ q: params.term || '' }),
        processResults: data => data 
      }
    });
  }

  (function(){
    const $form  = $('#categoryCreateForm');
    const $modal = $form.closest('.modal');              // current AjaxModal
    const $sel   = $form.find('#categoryTypeSelect');    // your select

    // init select2 now (partial already loaded)
    initCategorySelect($sel, $modal);

    // create modal হলে clear করো, edit হলে প্রিসিলেক্টেড অপশনই থাকবে
    const hasPreselected = !!$sel.find('option[selected]').length || !!$sel.val();
    if (!hasPreselected) {
      $sel.val(null).trigger('change');
    }
    // (optional) 
    $form.find('.name-error,.slug-error,.category_type_id-error').hide().text('');
  })();



  
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
