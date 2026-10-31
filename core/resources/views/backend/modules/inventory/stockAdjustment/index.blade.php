@extends('backend.layouts.master')
@section('meta')
    <title>Stock Adjustments</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Stock Adjustments</h6>
            <p class="text-muted m-0">Increase/Decrease stock with reason</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium"><a href="{{ route('backend.dashboard') }}"
                    class="d-flex align-items-center gap-1 hover-text-primary"><iconify-icon
                        icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a></li>
            <li>-</li>
            <li class="fw-medium">Adjustments</li>
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
                <a href="{{ route('inventory.adjustments.create') }}"
                    class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8" data-size="lg">
                    <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>New Adjustment
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="adjustTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">SL</th>
                        <th>Reference</th>
                        <th style="width:160px">Date</th>
                        <th>Warehouse</th>
                        <th>Branch</th>
                        <th style="width:90px" class="text-center">Items</th>
                        <th style="width:130px" class="text-end">Qty (+ / -)</th>
                        <th style="width:110px">Status</th>
                        <th>By</th>
                        <th>Note</th>
                        <th style="width:220px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('inventory.adjustments.list') }}";


        window.AdjustmentsIndex = {
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


        // ensure CSRF var exists
        const CSRF = "{{ csrf_token() }}";

        // POST (publish) adjustment with SweetAlert confirm + feedback
        $(document).on('click', '.btn-adjust-post', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            if (!url) return;

            // SweetAlert confirm
            Swal.fire({
                title: 'Post this adjustment?',
                text: 'This will update stock and create ledger entries.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Post',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);
                $.post(url, {
                        _token: CSRF
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Posted',
                            timer: 900,
                            showConfirmButton: false
                        });
                        if (window.adjustmentsTable) {
                            window.adjustmentsTable.ajax.reload(null, false);
                        } else {
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .fail(function(xhr) {
                        const msg = xhr?.responseJSON?.msg || 'Post failed';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });

        // CANCEL adjustment with SweetAlert confirm + feedback
        $(document).on('click', '.btn-adjust-cancel', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            if (!url) return;

            Swal.fire({
                title: 'Cancel this posted adjustment?',
                text: 'This will reverse or mark the adjustment as cancelled depending on policy.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'Keep',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);
                $.post(url, {
                        _token: CSRF
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Cancelled',
                            timer: 900,
                            showConfirmButton: false
                        });
                        if (window.adjustmentsTable) {
                            window.adjustmentsTable.ajax.reload(null, false);
                        } else {
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .fail(function(xhr) {
                        const msg = xhr?.responseJSON?.msg || 'Cancel failed';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });

        // DELETE draft adjustment with SweetAlert confirm + feedback
        $(document).on('click', '.btn-adjust-delete', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            if (!url) return;

            Swal.fire({
                title: 'Delete this draft?',
                text: 'This will permanently delete the adjustment and its lines. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);
                $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: CSRF
                        }
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Deleted',
                            timer: 900,
                            showConfirmButton: false
                        });
                        if (window.adjustmentsTable) {
                            window.adjustmentsTable.ajax.reload(null, false);
                        } else {
                            setTimeout(() => location.href =
                                "{{ route('inventory.adjustments.index') }}", 700);
                        }
                    })
                    .fail(function(xhr) {
                        const msg = xhr?.responseJSON?.msg || 'Delete failed';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });
    </script>
@endsection
