@extends('backend.layouts.master')

@section('meta')
    <title>Stock Ledger</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Stock Ledger</h6>
            <p class="text-muted">Product-wise stock movement history</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Stock Ledger</li>
        </ul>
    </div>

    {{-- Filters --}}
    <div class="card mb-3 shadow-lg">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1"><iconify-icon icon="solar:filter-linear"
                            class="menu-icon"></iconify-icon> Filter</h6>
                </div>

                {{-- Product --}}
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select id="productFilter" class="form-control-sm  js-s2-ajax" data-url="{{ route('product.select2') }}"
                        data-placeholder="Select Product">
                    </select>
                </div>

                {{-- Warehouse --}}
                <div class="col-md-3">
                    <label class="form-label">Warehouse</label>
                    <select id="warehouseFilter" class="form-control-sm js-s2-ajax"
                        data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="All Warehouses">
                    </select>
                </div>

                {{-- From date --}}
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>

                {{-- To date --}}
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>

                {{-- Button --}}
                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                        id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="menu-icon text-lg"></iconify-icon> search
                    </button>
                </div>

            </div>
        </div>
    </div>
    {{-- 📊 Stock Summary --}}
    <div class="row mb-3" id="stockSummary" style="display:none">

        <div class="col-md-3">
            <div class="card shadow-sm border-start bg-warning-50 border-primary border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Opening Stock</p>
                    <h5 class="mb-0 fw-semibold text-warning" id="sumOpening">0.00</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start bg-success-50 border-success border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Stock In</p>
                    <h5 class="mb-0 fw-semibold text-success" id="sumIn">0.00</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start bg-danger-50 border-danger border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Stock Out</p>
                    <h5 class="mb-0 fw-semibold text-danger" id="sumOut">0.00</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start bg-primary-50 border-primary  border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Closing Stock</p>
                    <h5 class="mb-0 fw-semibold text-primary" id="sumClosing">0.00</h5>
                </div>
            </div>
        </div>

    </div>


    {{-- Table --}}
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
                        <th>Date</th>
                        <th>Product</th>
                        <th>Warehouse</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th class="text-end">IN</th>
                        <th class="text-end">OUT</th>
                        <th class="text-end">Stock Qty</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('inventory.reports.stock-ledger.list.ajax') }}";

        window.S2.auto();

        $('#btnFilter').on('click', function() {
            let url = DATATABLE_URL +
                '?product_id=' + ($('#productFilter').val() ?? '') +
                '&warehouse_id=' + ($('#warehouseFilter').val() ?? '') +
                '&from_date=' + ($('#fromDate').val() ?? '') +
                '&to_date=' + ($('#toDate').val() ?? '');

            $('.AjaxDataTable').DataTable().ajax.url(url).load();
        });

        // Load stock summary
        function loadStockSummary() {

            $.get("{{ route('inventory.reports.stock-ledger.summary') }}", {
                product_id: $('#productFilter').val(),
                warehouse_id: $('#warehouseFilter').val(),
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val()
            }, function(res) {

                $('#sumOpening').text(res.opening.toFixed(2));
                $('#sumIn').text(res.in.toFixed(2));
                $('#sumOut').text(res.out.toFixed(2));
                $('#sumClosing').text(res.closing.toFixed(2));

                $('#stockSummary').slideDown(150);
            });
        }

        // Load button
        $('#btnFilter').on('click', function() {

            if (!$('#productFilter').val()) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Select product first'
                });
                return;
            }

            loadStockSummary();

            $('.AjaxDataTable').DataTable().ajax.reload();
        });
    </script>
@endsection
