@extends('backend.layouts.master')

@section('meta')
    <title>Customer Ledger</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Customer Ledger</h6>
            <p class="m-0">Customer-wise sales, payments & returns</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Customer Ledger</li>
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
                <div class="col-md-4 " style="margin-top:10px !important;">
                    <label class="form-label">Customer</label>
                    <select class=" js-s2-ajax"  id="customerFilter"
                        data-url="{{ route('customer.select2') }}" data-placeholder="Select Customer">
                    </select>
                </div>

                <div class="col-md-3 ">
                    <label class="form-label">From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 ">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                        id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="menu-icon text-lg"></iconify-icon> search
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- 🔹 Customer Summary --}}
    <div id="customerSummary" class="row g-3 mb-3 d-none">

        <div class="col-md-3">
            <div class="card border-0 bg-primary-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Sale</p>
                    <h6 class="mb-0 text-primary" id="sumSale">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-success-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Paid</p>
                    <h6 class="mb-0 text-success" id="sumPaid">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-warning-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Return</p>
                    <h6 class="mb-0 text-warning" id="sumReturn">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-danger-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Balance</p>
                    <h6 class="mb-0 text-danger" id="sumBalance">0.00</h6>
                </div>
            </div>
        </div>

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

            </div>
        </div>

        <div class="card-body">
            {{-- 📊 Ledger Table --}}
            <table class="table bordered-table AjaxDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('customerLedger.list.ajax') }}";

        $('#btnFilter, #customerFilter').on('click', function() {

            let customerId = $('#customerFilter').val();
            let fromDate = $('#fromDate').val();
            let toDate = $('#toDate').val();
            if (!customerId) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select a customer'
                });
                return;
            }

            // Date validation (pair wise)
            if (!$('#fromDate').val() || !$('#toDate').val()) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select both From and To date'
                });
                return;
            }

            let url = DATATABLE_URL +
                '?customer_id=' + ($('#customerFilter').val() ?? '') +
                '&from_date=' + ($('#fromDate').val() ?? '') +
                '&to_date=' + ($('#toDate').val() ?? '');

            $('.AjaxDataTable')
                .DataTable()
                .ajax
                .url(url)
                .load();

            loadCustomerSummary();
        });

        S2.auto();

        function loadCustomerSummary() {

            let customerId = $('#customerFilter').val();
            if (!customerId) {
                $('#customerSummary').addClass('d-none');
                return;
            }

            $.get("{{ route('customerLedger.summary') }}", {
                customer_id: customerId,
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val()
            }, function(res) {

                $('#sumSale').text(res.total_sale.toFixed(2));
                $('#sumPaid').text(res.total_paid.toFixed(2));
                $('#sumReturn').text(res.total_return.toFixed(2));
                $('#sumBalance').text(res.balance.toFixed(2));

                $('#customerSummary').removeClass('d-none');
            });
        }

        // $('#customerFilter, #fromDate, #toDate').on('change', function() {
        //     loadCustomerSummary();
        //     $('.AjaxDataTable').DataTable().ajax.reload();
        // });
    </script>
@endsection
