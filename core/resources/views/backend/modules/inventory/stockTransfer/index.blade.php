@extends('backend.layouts.master')
@section('meta')
    <title>Stock Transfers</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Stock Transfers</h6>
            <p class="text-muted m-0">Move stock between warehouses</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium"><a href="{{ route('backend.dashboard') }}"
                    class="d-flex align-items-center gap-1 hover-text-primary"><iconify-icon
                        icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a></li>
            <li>-</li>
            <li class="fw-medium">Transfers</li>
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
                {{-- <button class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 AjaxModal"
        data-ajax-modal="{{ route('inventory.transfers.create') }}"
        data-size="lg"
        data-onsuccess="TransfersIndex.onSaved">
        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon> New Transfer
      </button> --}}
                <a href="{{ route('inventory.transfers.create') }}"
                    class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8" data-size="lg">
                    <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>New Transfer
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="transfersTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:150px">Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th>Status</th>
                        <th>By</th>
                        <th>Note</th>
                        <th style="width:90px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('inventory.transfers.list') }}";

        window.TransfersIndex = {
            onSaved(res) {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                window.Swal && Swal.fire({
                    icon: 'success',
                    title: res?.msg || 'Saved',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        };

        $(document).on('click', '.btn-transfer-post', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const url = $btn.data('url');
            if (!url) return;

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to post this transfer? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, post it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);

                $.post(url)
                    .done(function(res) {
                        if (res.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Posted!',
                                text: res.msg || 'Transfer posted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // reload datatable if needed
                            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: res.msg || 'Something went wrong while posting.'
                            });
                        }
                    })
                    .fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseText || 'Server error occurred.'
                        });
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });

        $(document).on('click', '.btn-transfer-delete', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const url = $btn.data('url');
            if (!url) return;

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this transfer? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.msg || 'Transfer deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // reload datatable if needed
                            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: res.msg || 'Something went wrong while deleting.'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseText || 'Server error occurred.'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
