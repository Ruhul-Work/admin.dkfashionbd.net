@extends('backend.layouts.master')

@section('meta')
    <title>Edit brand </title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Brand Management</h6>
            <p class="fw-semibold mb-0">Edit Brand</p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Brand</li>
        </ul>

        {{-- Button on a new line (right aligned) --}}
        <div class="text-end w-100">
            <a href="{{ route('brand.brands.index') }}" class="btn btn-secondary bg-dark "><i data-feather="arrow-left"
                    class="me-2"></i>Back to
                Brands</a>
        </div>
    </div>

    <form action="{{ route('brand.brands.update', $brand->id) }}" id="edit_brand" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card py-3">
            <div class="card-body add-product">
                <div class="accordion-card-one accordion mb-6" id="accordionExample">
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingOne">
                            <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                aria-controls="collapseOne">
                                <div class="addproduct-icon">
                                    <h6><i data-feather="info" class="add-info"></i><span>Basic Information</span></h6>
                                    <a href="javascript:void(0);"><i data-feather="chevron-down"
                                            class="chevron-down-add"></i></a>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <div class="row">


                                    <div class="col-lg-5 col-sm-6 col-12">
                                        <div class="mb-3 add-product">
                                            <label class="form-label">Name <span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $brand->name }}">

                                        </div>

                                    </div>
                                    <div class="col-lg-5 col-sm-6 col-12">
                                        <div class="mb-3 add-product">
                                            <label class="form-label">Slug<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ $brand->slug }}">
                                        </div>
                                    </div>

                                    


                                </div>

                                <div class="row mt-3 gap-1">


                                    <!-- Image -->
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 add-product">
                                            <label class="form-label">Image <span class="star-sign">*</span></label>
                                            <div class="form-group">
                                                <div id="brand_image">
                                                    <!-- image upload/preview here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Status -->
                                     <div class="row">
                                        <div class="col-12 mb-8">
                                            <label class="form-label text-sm mb-8">Active?</label>
                                            <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                                <input type="hidden" name="is_active" value="0">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                    value="1" id="brandIsActive" {{$brand->is_active?'checked':''}}>
                                                <label class="form-check-label" for="categoryIsActive">Enable this
                                                    Brand</label>
                                            </div>
                                            <div class="invalid-feedback d-block is_active-error" style="display:none">
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-card-one accordion mt-3" id="accordionExample2">
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingTwo">
                            <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                aria-controls="collapseTwo">
                                <div class="text-editor add-list">
                                    <div class="addproduct-icon list icon">
                                        <h6><i data-feather="life-buoy" class="add-info"></i><span>Meta Section</span></h6>
                                        <a href="javascript:void(0);"><i data-feather="chevron-down"
                                                class="chevron-down-add"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionExample2">
                            <div class="accordion-body">

                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                        aria-labelledby="pills-home-tab">
                                        <div class="row">


                                            <div class="col-lg-5 col-sm-6 col-12">
                                                <div class=" add-product">
                                                    <label class="form-label">Meta Title</label>
                                                    <input type="text" class="form-control" name="meta_title"
                                                        value="{{ $brand->meta_title }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-5 col-sm-6 col-12">
                                                <div class=" add-product">
                                                    <label class="form-label">Meta Keywords</label>
                                                    <input type="text" class="form-control" name="meta_keywords"
                                                        value="{{ $brand->meta_keywords }}">
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <!-- Meta Image -->
                                                <div class="col-lg-3 col-sm-12">
                                                    <label class="form-label">Meta Image</label>
                                                    <div class="form-group">
                                                        <div class="row" id="meta_image">
                                                            <!-- your image upload/input content here -->
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Meta Description -->
                                                <div class="col-lg-3 col-sm-12 m-2">
                                                    <label class="form-label">Meta Description</label>
                                                    <textarea rows="7" cols="8" class="form-control h-full" name="meta_description"
                                                        placeholder="Enter text here">{{ $brand->meta_description }}</textarea>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-12 mt-3">
            <div class="btn-addproduct mb-4">
                <button type="submit" class="btn btn-primary text-white ">Save All</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <style>
        .star-sign {
            color: red;
            font-weight: bold;
        }
    </style>
    <script src="{{ asset('theme/admin/assets/plugins/fileupload/spartan-multi-image-picker-min.js') }}"
        type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            function generateSlug(name) {

                var pattern = /[a-zA-Z0-9\u0980-\u09FF]+/g;

                return name.toLowerCase().match(pattern).join('_');
            }

            @if (!empty($brand->image))



                var previousImageUrl = "{{ image($brand->image) }}"; // Blade variable
                if (previousImageUrl) {
                    $("#brand_image").append(
                        '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                        '<img src="' + previousImageUrl +
                        '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                        '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;">✕</button>' +
                        '</div>'
                    );
                }

                // When user clicks cancel on previous image
                $(document).on('click', '.btn-cancel-old', function() {
                    $(this).closest('.existing-image-wrapper').remove(); // Remove preview
                    $('#remove_old_image').val(1); // Mark for backend deletion
                });
            @endif



            @if (!empty($brand->meta_image))


               var previousImageUrl = "{{ image($brand->meta_image) }}"; // Blade variable
                if (previousImageUrl) {
                    $("#meta_image").append(
                        '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                        '<img src="' + previousImageUrl +
                        '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                        '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;">✕</button>' +
                        '</div>'
                    );
                }

                // When user clicks cancel on previous image
                $(document).on('click', '.btn-cancel-old', function() {
                    $(this).closest('.existing-image-wrapper').remove(); // Remove preview
                    $('#remove_old_image').val(1); // Mark for backend deletion
                });
            @endif

            // Event listener for name field
            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = name ? generateSlug(name) : null; // Generate slug only if name is not empty
                $('#slug').val(slug);
            });


            $("#brand_image").spartanMultiImagePicker({
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



        });
    </script>
@endsection
