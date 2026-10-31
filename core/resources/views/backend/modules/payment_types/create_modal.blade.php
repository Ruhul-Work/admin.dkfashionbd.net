<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
    <h5 class="modal-title">Add Payment Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24" id="categoryCreateModal">
  <form id="categoryCreateForm"
        action="{{ route('paymentTypes.store') }}"
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

   

    {{-- Row 3: images --}}
    <div class="row">
      <div class="col-md-6 mb-20">
        <label class="form-label text-sm mb-8">Icon Image</label>
        <div id="icon"></div>
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
        fieldName: 'image',
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
