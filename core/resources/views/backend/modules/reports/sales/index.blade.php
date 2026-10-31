@extends('backend.layouts.master')

@section('meta')
    <title>Sales Report</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Sales Report</h6>
            <p class="text-muted">Filtered sales listing</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Sales Report</li>
        </ul>
    </div>

    {{-- Filters --}}
    <div class="card mb-3 shadow-lg">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1"><iconify-icon icon="solar:filter-linear" class="menu-icon"></iconify-icon> Filter</h6>
                </div>

                {{-- From date --}}
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>

                {{-- To date --}}
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>

                {{-- Warehouse --}}
                <div class="col-md-4">
                    <label class="form-label">Warehouse</label>
                    <select id="warehouseFilter" class="form-control-sm js-s2-ajax"
                        data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="All Warehouses">
                    </select>
                </div>

                {{-- Button --}}
                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1" id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="menu-icon text-lg"></iconify-icon> search
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- 📊 Sales Summary --}}
    <div class="row mb-3" id="salesSummary" style="display:none">

        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-primary-50 border-primary border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Sales</p>
                    <h5 class="mb-0 fw-semibold text-primary" id="sumTotal">0.00</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-success-50 border-success border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Paid Amount</p>
                    <h5 class="mb-0 fw-semibold text-success" id="sumPaid">0.00</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-danger-50 border-danger border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Due Amount</p>
                    <h5 class="mb-0 fw-semibold text-danger" id="sumDue">0.00</h5>
                </div>
            </div>
        </div>

    </div>

    <div class="card basic-data-table shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Datatables</h5>
            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

            </div>
        </div>
        <div class="card-body">
            <table class="table bordered-table AjaxDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>User</th>
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
        var DATATABLE_URL = "{{ route('reports.sales.list.ajax') }}";
        var showingError = false;

        window.S2?.auto();

        // Handle DataTable errors
        $.fn.dataTable.ext.errMode = function(settings, tn, message) {
            if (!showingError) {
                showingError = true;
                Swal.fire({ icon: 'error', title: 'Error', text: 'Please select a branch first' }).then(() => {
                    showingError = false;
                });
            }
        };

        // Load sales summary
        function loadSalesSummary() {
            $.get("{{ route('reports.sales.summary') }}", {
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val(),
                warehouse_id: $('#warehouseFilter').val()
            }, function(res) {
                $('#sumTotal').text(res.total.toFixed(2));
                $('#sumPaid').text(res.paid.toFixed(2));
                $('#sumDue').text(res.due.toFixed(2));

                $('#salesSummary').slideDown(150);
            }).fail(function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.msg || 'Please select a branch first' });
                }
            });
        }

        // Filter button click
        $('#btnFilter').on('click', function() {
            // Validate dates are provided
            if (!$('#fromDate').val() || !$('#toDate').val()) {
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Please select both From and To dates' });
                return false;
            }

            var warehouseId = $('#warehouseFilter').val() || '';
            let url = DATATABLE_URL +
                '?start_date=' + $('#fromDate').val() +
                '&end_date=' + $('#toDate').val() +
                '&warehouse_id=' + warehouseId;

            loadSalesSummary();

            var table = $('.AjaxDataTable').DataTable();
            table.ajax.url(url).load();
        });

    </script>
@endsection
