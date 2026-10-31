@extends('backend.layouts.master')

@section('meta')
    <title>Coupons</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Coupon List</h6>
            <p class="m-0">Manage Countries</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Coupons</li>
        </ul>
    </div>

    <div class="card basic-data-table">
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
                        <th>Coupon Code</th>
                        <th>Coupon Type</th>
                        <th>Discount Type</th>
                        <th>Discount Value</th>
                        <th>Minimum Order </th>
                        <th>Max Discount Amount</th>
                        <th>Max Usage</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>

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
        var DATATABLE_URL = "{{ route('coupon.list.ajax') }}";

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
                if (confirm('Delete this coupon?')) doDelete();
            }
        });
    </script>
@endsection
