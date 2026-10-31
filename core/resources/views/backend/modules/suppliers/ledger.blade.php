@extends('backend.layouts.master')

@section('meta')
    <title>Supplier</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Suppliers Ledger</h6>
            <p class=" m-0">Transaction details for suppliers</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Supplier Ledger</li>
        </ul>
    </div>

    {{-- 🔎 Filter --}}
    <div class="card mb-3 shadow-lg">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1"><iconify-icon icon="solar:filter-linear"
                            class="menu-icon"></iconify-icon> Filter</h6>
                </div>

                {{-- Supplier --}}
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select class=" form-control-sm js-s2-ajax " id="supplier" data-url="{{ route('supplier.select2') }}"
                        data-placeholder="Select Supplier">
                    </select>
                </div>

                {{-- Branch --}}
                {{-- <div class="col-md-2 ">
                    <select class="form-control-sm js-s2-ajax" id="branch" data-url="{{ route('org.branches.select2') }}"
                        data-placeholder="Branch (Optional)">
                    </select>
                </div> --}}

                {{-- From Date --}}
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" placeholder="From date">
                </div>

                {{-- To Date --}}
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" placeholder="To date">
                </div>

                {{-- Search --}}
                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                        id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="menu-icon text-lg"></iconify-icon>
                        Search
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- 🔹 Supplier Summary --}}
    <div id="supplierSummary" class="row g-3 mb-3 d-none">

        <div class="col-md-3">
            <div class="card border-0 bg-primary-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Purchase</p>
                    <h6 id="sumPurchase">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-success-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Paid</p>
                    <h6 id="sumPaid">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-warning-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Return Received</p>
                    <h6 id="sumReturn">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-danger-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Balance</p>
                    <h6 id="sumBalance">0.00</h6>
                </div>
            </div>
        </div>

    </div>


    <div>

        <div class="card basic-data-table mt-3">

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
                <table class="table bordered-table AjaxDataTable w-100">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>Transaction Type</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>

                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.S2.auto();
        // $(document).ready(function() {
        //     $('#btn-load').on('click', function() {
        //         loadLedger();
        //     });
        //     console.log('ledger page loaded');
        // });

        var DATATABLE_URL = "{{ route('supplier.ledger.list') }}";

        $('#btnFilter').on('click', function() {

            let url = DATATABLE_URL +
                '?supplier_id=' + ($('#supplier').val() ?? '') +
                '&branch_id=' + ($('#branch').val() ?? '') +
                '&from_date=' + ($('#start_date').val() ?? '') +
                '&to_date=' + ($('#end_date').val() ?? '');

            $('.AjaxDataTable')
                .DataTable()
                .ajax
                .url(url)
                .load();
            loadSupplierSummary();
        });



        function loadSupplierSummary() {

            let supplierId = $('#supplier').val();
            if (!supplierId) {
                $('#supplierSummary').addClass('d-none');
                return;
            }

            $.get("{{ route('supplier.ledger.summary') }}", {
                supplier_id: supplierId,
                branch_id: $('#branch').val(),
                from_date: $('#start_date').val(),
                to_date: $('#end_date').val()
            }, function(res) {

                $('#sumPurchase').text(res.total_purchase.toFixed(2));
                $('#sumPaid').text(res.total_paid.toFixed(2));
                $('#sumReturn').text(res.total_return.toFixed(2));
                $('#sumBalance').text(res.balance.toFixed(2));

                $('#supplierSummary').removeClass('d-none');
            });
        }


        // $('#supplier, #branch, #start_date, #end_date').on('change', function() {
        //     loadSupplierSummary();
        //     $('.AjaxDataTable').DataTable().ajax.reload();
        // });
    </script>
@endsection
