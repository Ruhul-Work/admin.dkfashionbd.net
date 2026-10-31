@extends('backend.layouts.master')

@section('meta')
    <title>Sale Return List</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Sale Return List</h6>
            <p class="m-0">Manage returned sales & refunds</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Sale Returns</li>
        </ul>
    </div>

    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Sale Return Datatable</h5>

            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">

                {{-- Search --}}
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                {{-- Common table buttons --}}
                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="saleReturnTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>Return Ref</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Return Date</th>
                        <th class="text-end">Refund</th>
                        <th class="text-end">Total Refunded</th>
                        <th class="text-end">Total Due</th>
                        <th>Status</th>
                        <th>Created At</th>
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
        /* ======================================================
         | Datatable URL
         ====================================================== */
        var DATATABLE_URL = "{{ route('pos.saleReturns.list.ajax') }}";



        /*| Optional JS hooks (future use) */
        window.SaleReturnIndex = {
            onReload: function() {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            }
        };

        window.saleReturnPayIndex = {
            onSaved: function() {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Refund Added',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }

                $('#AjaxModal').modal('hide');
            }
        };

        /* ======================================================
         |  delete (future use)
         ====================================================== */
        $(document).on('click', '.btn-sale-return-delete', function(e) {
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
                        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                        Swal && Swal.fire({
                            icon: 'success',
                            title: res?.msg || 'Deleted',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal && Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Delete failed'
                        });
                    }
                });
            };

            Swal.fire({
                icon: 'warning',
                title: 'Delete sale return?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                confirmButtonColor: '#d33'
            }).then(r => {
                if (r.isConfirmed) doDelete();
            });
        });
    </script>
@endsection
