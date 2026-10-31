@extends('backend.layouts.master')

@section('meta')
    <title>Assign Coupon Products</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Coupon {{$coupon->coupon_type==='user'?'User':'Product'}} Management</h6>
            <p class="fw-semibold mb-0">Add coupon {{$coupon->coupon_type==='user'?'User':'Product'}} </p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Coupon Product</li>
        </ul>

        {{-- Button on a new line (right aligned) --}}
        <div class="text-end w-100 ">
            <a href="{{ route('coupon.index') }}" class="btn btn-secondary bg-dark "><i data-feather="arrow-left"
                    class="me-2"></i>Back to
                Coupons</a>
        </div>
    </div>
    <form action="" id="addCouponProducts" method="post">
        @csrf


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

                        <div class="mb-3 add-product">
                            <label class="form-label"> {{$coupon->coupon_type==='user'?'Users':'Products'}} <span class="star-sign">*</span></label>
                            <select class="js-s2-ajax" name="{{$coupon->coupon_type==='user'?'user_id[]':'product_id[]'}}" id="product"
                                data-url="{{$coupon->coupon_type==='product'?route('product.parents.select2'):route('customer.select2') }}" data-placeholder="{{$coupon->coupon_type==='user'?'Select Users':'Select Products'}}" multiple>
                               
                            </select>

                        </div>



                    </div>

                </div>
            </div>
        </div>
         <div class="col-lg-12 mt-3 mx-1">
            <div class="btn-addproduct mb-4 ">
                <button type="submit" class="btn btn-primary ">Save All</button>
            </div>
        </div>
        </div>

       
    </form>

<div class="p-3">
    
    <div class="card basic-data-table ">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Datatables</h5>
            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

                @perm('coupon.store')
                    <a class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 " href="{{ route('coupon.create') }}">

                        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>Add coupon
                    </a>
                @endperm
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="branchesTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                        <th>Coupon Name</th>
                  
                        <th>{{$coupon->coupon_type==='product'?'Product':'User'}}</th>
                        <th style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    
    

</div> 
@endsection


@section('script')
    <script>
        var DATATABLE_URL = "{{ route('coupon.allCouponAssociates', $coupon->id) }}";

        window.BranchesIndex = {
            onSaved: function(res) {
                // DataTable current page reload
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                // Optional: extra toast (আপনার গ্লোবালে আগেই toast দেওয়া আছে, এটা না দিলেও হবে)
                if (window.Swal) Swal.fire({
                    icon: 'success',
                    title: 'Created',
                    text: res?.msg || 'Saved',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        };


        $(document).on('click', '.btn-coupon-delete', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            const doDelete = () => {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        // DataTable রিলোড (পজিশন ধরে)
                        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: res?.msg || 'Deleted',
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const msg = xhr.responseJSON?.msg || 'Cannot delete this coupon.';
                            Swal && Swal.fire({
                                icon: 'warning',
                                title: 'Blocked',
                                text: msg
                            });
                        } else if (xhr.status === 403) {
                            Swal && Swal.fire({
                                icon: 'warning',
                                title: 'Forbidden',
                                text: xhr.responseJSON?.message || 'Permission denied'
                            });
                        } else {
                            Swal && Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Delete failed'
                            });
                        }
                    }
                });
            };

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete coupon?',
                    text: 'This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    confirmButtonColor: '#d33'
                }).then(r => {
                    if (r.isConfirmed) doDelete();
                });
            } else {
                if (confirm('Delete this product from coupon?')) doDelete();
            }
        });
    </script>

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

            $('#addCouponProducts').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally
                var formData = new FormData(this);

                // Make AJAX request
                $.ajax({
                    url: '{{ route('coupon.storeCouponProducts', $coupon->id) }}',
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
                               $('.AjaxDataTable').DataTable().ajax.reload(null, false);
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
        
     window.S2.auto();

    </script>
@endsection



