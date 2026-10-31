@extends('backend.layouts.master')

@section('meta')
    <title>Edit Coupon</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Coupon Management</h6>
            <p class="fw-semibold mb-0">Edit coupon</p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Coupon</li>
        </ul>

        {{-- Button on a new line (right aligned) --}}
        <div class="text-end w-100 ">
            <a href="{{ route('coupon.index') }}" class="btn btn-secondary bg-dark "><i data-feather="arrow-left"
                    class="me-2"></i>Back to
                Coupons</a>
        </div>
    </div>
    <form action="" id="editCoupon" method="post">
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


                            <div class="col-lg-4 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Title <span class="star-sign">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ $coupon->title }}">

                                </div>

                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Code<span class="star-sign">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $coupon->code }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Coupon Type<span class="star-sign">*</span></label>
                                    <select class="form-control" id="coupon_type" name="coupon_type">
                                        <option value="">Select Coupon Type</option>
                                        <option value="product" {{ $coupon->coupon_type == 'product' ? 'selected' : '' }}>Product</option>
                                        <option value="bill" {{ $coupon->coupon_type == 'bill' ? 'selected' : '' }}>Bill</option>
                                        <option value="user" {{ $coupon->coupon_type == 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Discount Type<span class="star-sign">*</span></label>
                                    <select class="form-control" id="discount_type" name="discount_type">
                                        <option value="">Select Discount Type</option>
                                        <option value="flat" {{ $coupon->discount_type == 'flat' ? 'selected' : '' }}>Fixed</option>
                                        <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Discount</label>
                                    <input type="text" class="form-control" id="discount" name="discount"
                                        placeholder="optional" value="{{ $coupon->discount }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Min Order</label>
                                    <input type="number" class="form-control" id="min_buy" name="min_buy"
                                        placeholder="optional" value="{{ $coupon->min_buy }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Max Discount<span class="star-sign">*</span></label>
                                    <input type="number" class="form-control" min="0" id="max_discount"
                                        name="max_discount" value="{{ $coupon->max_discount }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Max Use<span class="star-sign">*</span></label>
                                    <input type="number" class="form-control" min="0" id="individual_max_use"
                                        name="individual_max_use" value="{{ $coupon->individual_max_use }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Start Date<span class="star-sign">*</span></label>
                                    <input type="date" class="form-control" min="0" id="start_date"
                                        name="start_date" value="{{ $coupon->start_date }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">End Date<span class="star-sign">*</span></label>
                                    <input type="date" class="form-control" min="0" id="end_date"
                                        name="end_date"   value="{{ $coupon->end_date }}">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-12 mb-8">
                                    <label class="form-label text-sm mb-8">Active?</label>
                                    <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="categoryIsActive" {{ $coupon->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="categoryIsActive">Enable this
                                            coupon</label>
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

            function generateCouponCode() {
                const title = $('#title').val();

                const formattedTitle = title
                    .trim()
                    .toUpperCase()
                    .replace(/\s+/g, '-');

                const timestamp = Date.now();

                return `${formattedTitle}-${timestamp}`;
            }

            $('#title').on('input', function() {
                const couponCode = generateCouponCode();
                $('#code').val(couponCode);
            });


            $('#editCoupon').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally
                var formData = new FormData(this);

                // Make AJAX request
                $.ajax({
                    url: '{{ route('coupon.update', $coupon->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href =
                                    '{{ route('coupon.index') }}';
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Parse the JSON response from the server
                        try {
                            var responseObj = JSON.parse(xhr.responseText);
                            var errorMessages = responseObj.errors ? Object.values(responseObj
                                .errors).flat() : [responseObj.message];
                            var errorMessageHTML = '<ul>' + errorMessages.map(errorMessage =>
                                '<li>' + errorMessage + '</li>').join('') + '</ul>';

                            // Show error messages using SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errorMessageHTML,
                            });
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                            // Show default error message using SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred while processing your request. Please try again later.',
                            });
                        }
                    }

                });
            });


        });
    </script>
@endsection
