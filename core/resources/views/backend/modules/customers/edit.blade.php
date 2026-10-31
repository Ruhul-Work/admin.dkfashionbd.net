@extends('backend.layouts.master')

@section('meta')
    <title>Update Customer</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Customer Management</h6>
            <p class="fw-semibold mb-0">Update customer</p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Customer</li>
        </ul>

        {{-- Button on a new line (right aligned) --}}
        <div class="text-end w-100 ">
            <a href="{{ route('customer.index') }}" class="btn btn-secondary bg-dark "><i data-feather="arrow-left"
                    class="me-2"></i>Back to
                customers</a>
        </div>
    </div>
    <form action="{{ route('customer.update', $customer->id) }}" id="createcustomer" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <div class="accordion-card-one accordion mb-6" id="accordionExample">
            <div class="accordion-item">
                <div class="accordion-header" id="headingOne">
                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-controls="collapseOne">
                        <div class="addproduct-icon">
                            <h6><i data-feather="info" class="add-info"></i><span>Basic Information</span></h6>
                            <a href="javascript:void(0);"><i data-feather="chevron-down" class="chevron-down-add"></i></a>
                        </div>
                    </div>
                </div>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">

                        <div class="row">


                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Name <span class="star-sign">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $customer->name }}">

                                </div>

                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Slug<span class="star-sign">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug"
                                        value="{{ $customer->slug }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Email<span class="star-sign">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $customer->email }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Phone<span class="star-sign">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ $customer->phone }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Alternate Phone</label>
                                    <input type="text" class="form-control" id="alternate_phone" name="alternate_phone"
                                        value="{{ $customer->alternate_phone ?? null }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Date of Birth<span class="star-sign">*</span></label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                                        value="{{ $customer->birth_date ?? '' }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Postal Code<span class="star-sign">*</span></label>
                                    <input type="number" class="form-control" min="0" id="address"
                                        name="postal_code" value="{{ $customer->postal_code }}">
                                </div>
                            </div>

                            
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Image</label>

                                    <div class="form-group">
                                        <div class="row" id="customer_image">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Address<span class="star-sign">*</span></label>
                                    <textarea type="text" class="form-control" rows="3" id="address" name="address">{{ $customer->address }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-8">
                                    <label class="form-label text-sm mb-8">Active?</label>
                                    <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                        <input type="hidden" name="is_active" value="0"
                                            {{ $customer->is_active === 0 ? 'checked' : '' }}>
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="categoryIsActive" {{ $customer->is_active === 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="categoryIsActive">Enable this
                                            customer</label>
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


        <div class="col-lg-12 mt-3">
            <div class="btn-addproduct mb-4">
                <button type="submit" class="btn btn-primary ">Save All</button>
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

            // Event listener for name field
            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = name ? generateSlug(name) : null; // Generate slug only if name is not empty
                $('#slug').val(slug);
            });

            // $('#createSubcategory').submit(function(e) {
            //     e.preventDefault(); // Prevent the form from submitting normally
            //     var formData = new FormData(this);

            //     // Make AJAX request
            //     $.ajax({
            //         url: '{{ route('customer.store') }}',
            //         method: 'POST',
            //         data: formData,
            //         processData: false,
            //         contentType: false,
            //         success: function(response) {
            //             Swal.fire({
            //                 icon: 'success',
            //                 title: 'Success!',
            //                 text: response.message,
            //             }).then((result) => {
            //                 if (result.isConfirmed) {
            //                     window.location.href =
            //                         '{{ route('customer.index') }}';
            //                 }
            //             });
            //         },
            //         error: function(xhr, status, error) {
            //             // Parse the JSON response from the server
            //             try {
            //                 var responseObj = JSON.parse(xhr.responseText);
            //                 var errorMessages = responseObj.errors ? Object.values(responseObj
            //                     .errors).flat() : [responseObj.message];
            //                 var errorMessageHTML = '<ul>' + errorMessages.map(errorMessage =>
            //                     '<li>' + errorMessage + '</li>').join('') + '</ul>';

            //                 // Show error messages using SweetAlert
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Error!',
            //                     html: errorMessageHTML,
            //                 });
            //             } catch (e) {
            //                 console.error('Error parsing JSON response:', e);
            //                 // Show default error message using SweetAlert
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Error!',
            //                     text: 'An error occurred while processing your request. Please try again later.',
            //                 });
            //             }
            //         }

            //     });
            // });


            @if (!empty($customer->image))



                var previousImageUrl = "{{ image($customer->image) }}"; // Blade variable
                if (previousImageUrl) {
                    $("#customer_image").append(
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

            $("#customer_image").spartanMultiImagePicker({
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





        });
    </script>
@endsection
