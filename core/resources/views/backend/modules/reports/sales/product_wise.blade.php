@extends('backend.layouts.master')

@section('meta')
    <title>Sales Report</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Product Wise Sales Report</h6>
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
                <div class="col-md-3">
                    <label class="form-label">Sale Type</label>
                    <select name="" id="sale_type" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="retail">Retail</option>
                        <option value="e-commerce">E-commerce</option>
                    </select>
                </div>
                

                

                {{-- Warehouse --}}
                <div class="col-md-3">
                    <label class="form-label">Warehouse</label>
                    <select id="warehouseFilter" class="form-control-sm js-s2-ajax"
                        data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="Select Warehouses">
                        
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select id="product" class="form-control-sm js-s2-ajax"
                        data-url="{{ route('product.parents.select2') }}" data-placeholder="Select Product">
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
    <div class="row mb-3 g-4" id="salesSummary" style="display:none">

        <div class="col-md-6">
            <div class="card shadow-sm border-start bg-primary-50 border-primary border-4">
                <div class="card-body">
                    <p class="text-dark mb-1 fw-semibold">Costing Details</p>
                    <div class="mb-0  text-primary col-md-12 row" id="sumTotal">0.00</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-start bg-success-50 border-success border-4">
                <div class="card-body">
                    <p class="text-dark mb-1 fw-semibold">Price Calculation</p>
                    <div class="mb-0  text-success col-md-12 row" id="sumPaid">0.00</div>
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
                        <th>Variant</th>
                        <th>Purchased Quantity</th>
                        <th>Stock</th>
                        <th>Stock Value</th>
                        <th>Sale QTY</th>
                        <th>Sale Value</th>
                        <th>Profit</th>
                        {{-- <th>Payment</th>
                        <th>Date</th>
                        <th>User</th> --}}
                      
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('reports.product-wise-sales.list.ajax') }}";
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
            $.get("{{ route('reports.product-wise-sales.summary') }}", {
                product_id: $('#product').val(),
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val(),
                warehouse_id: $('#warehouseFilter').val(), 
                sale_type: $('#sale_type').val(),
                source: $('#source').val()
            }, function(res) { 
                $('#sumTotal').html("<p class='col-md-4 py-0'>Purchased: - </p>"+"<p class='col-md-4'>Bag: -</p>"+"<p class='col-md-4'>Delivery: -</p>"+"<p class='col-md-4'>Transport: -</p>"+"<p class='col-md-4'>Total Cost: "+res.cost+"</p>");
                $('#sumPaid').html("<p class='col-md-4 py-0'>MRP: "+res.mrp+"</p>"+"<p class='col-md-4'>Discount (avg): "+res.discount+"</p>"+"<p class='col-md-4'>Sale Value (avg): "+res.sale_value+"</p>"+"<p class='col-md-4'>Profit: "+res.profit+"</p>"+"<p class='col-md-4'>Profit Margin: "+res.profit_percent.toFixed(2)+"%</p>");
                // $('#sumDue').text(res.due.toFixed(2));
                // $('#quantity').text(res.quantity);

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
            var sale_type = $('#sale_type').val() || '';
            var source = $('#source').val() || '';
            var product_id = $('#product').val() || '';
            let url = DATATABLE_URL +
                '?from_date=' + $('#fromDate').val() +
                '&to_date=' + $('#toDate').val() +
                '&product_id=' + product_id +
                '&warehouse_id=' + warehouseId +
                '&sale_type=' + sale_type +
                '&source=' + source;

            loadSalesSummary();

            var table = $('.AjaxDataTable').DataTable();
            table.ajax.url(url).load();
        });

    </script>
@endsection
