@extends('backend.layouts.master')

@section('meta')
    <title>Offers List</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Offers List</h6>
            <p class="text-muted m-0">Manage all offers</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">
                <a href="{{ route('offers.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="mdi:gift" class="menu-icon"></iconify-icon>
                    Offers
                </a>
            </li>
        </ul>
    </div>

    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Offers</h5>

            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

                <a href="{{ route('offers.create') }}" class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                    Add Offer
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="offersTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                     
                        <th>Name</th>
                        <th>Type</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        DATATABLE_URL = "{{ route('offers.ajax') }}";

        $(document).on("click", '.changeStatus', function(e) {
            e.preventDefault();

            var offerId = $(this).data('offer-id');
            var currentStatus = $(this).text().trim() == 'Active' ? 1 : 0;
            var newStatus = currentStatus == 1 ? 0 : 1;

            $.ajax({
                url: '{{ route('offers.updateStatus') }}',
                type: 'POST',
                data: {
                    id: offerId,
                    status: newStatus,
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        $('.AjaxDataTable').DataTable().ajax.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        title: "Error!",
                        text: "An error occurred.",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        $(document).on("click", '.delete-btn', function(e) {
            e.preventDefault();

            var url = $(this).attr('href');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                $('.AjaxDataTable').DataTable().ajax.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred.",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
