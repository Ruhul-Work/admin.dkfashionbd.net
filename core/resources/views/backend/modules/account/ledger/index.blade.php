@extends('backend.layouts.master')

@section('meta')
    <title>Account Ledger</title>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Account Ledger</h6>
            <p class="text-muted mb-0">Journal based running balance</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Account Ledger</li>
        </ul>
    </div>

    {{-- Filter --}}
    <div class="card mb-3 shadow-lg">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1"><iconify-icon icon="solar:filter-linear"
                            class="menu-icon"></iconify-icon> Filter</h6>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account</label>
                    <select id="accountFilter" class="form-control form-control-sm">
                        <option value="">Select Account</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                        id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="text-lg"></iconify-icon>
                        Load
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= Account Ledger Summary ================= --}}
    {{-- <div class="card mb-3 shadow-sm" id="accountSummary" style="display:none;">
        <div class="card-body py-3"> --}}
    <div class="row g-3 mb-3" id="accountSummary" style="display:none;">

        {{-- Opening Balance --}}
        <div class="col-md-3">
            <div class="card border-0 bg-warning-50">
                <div class="card-body">
                    <p class="mb-1 text-muted small">Opening Balance</p>
                    <h6 class="mb-0 fw-semibold text-dark">
                        <span id="accOpening">0.00</span>
                    </h6>
                </div>
            </div>
        </div>

        {{-- Total Debit --}}
        <div class="col-md-3">
            <div class="card border-0 bg-success-50">
                <div class="card-body">
                    <p class="mb-1 text-muted small">Total Debit</p>
                    <h6 class="mb-0 fw-semibold text-success">
                        <span id="accDebit">0.00</span>
                    </h6>
                </div>
            </div>
        </div>

        {{-- Total Credit --}}
        <div class="col-md-3">
            <div class="card border-0 bg-danger-50">
                <div class="card-body">
                    <p class="mb-1 text-muted small">Total Credit</p>
                    <h6 class="mb-0 fw-semibold text-danger">
                        <span id="accCredit">0.00</span>
                    </h6>
                </div>
            </div>
        </div>

        {{-- Closing Balance --}}
        <div class="col-md-3">
            <div class="card border-0 bg-primary-50">
                <div class="card-body">
                    <p class="mb-1 text-muted small">Closing Balance</p>
                    <h6 class="mb-0 fw-semibold text-primary">
                        <span id="accClosing">0.00</span>
                    </h6>
                </div>
            </div>
        </div>

    </div>
    {{-- </div>
    </div> --}}
    {{-- ================= END Account Ledger Summary ================= --}}

    {{-- Ledger Table --}}
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
                        <th width="60">SL</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        DATATABLE_URL = "{{ route('accounts.ledger.list.ajax') }}";

        $('#btnFilter').on('click', function() {

            if (!$('#accountFilter').val()) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select an account'
                });
                return;
            }

            if (!$('#fromDate').val() || !$('#toDate').val()) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select both From and To date'
                });
                return;
            }

            let url = DATATABLE_URL +
                '?account_id=' + ($('#accountFilter').val() ?? '') +
                '&from_date=' + ($('#fromDate').val() ?? '') +
                '&to_date=' + ($('#toDate').val() ?? '');

            $('.AjaxDataTable')
                .DataTable()
                .ajax
                .url(url)
                .load();

            loadAccountSummary();

        });

        function loadAccountSummary() {
            $.get("{{ route('accounts.ledger.summary') }}", {
                account_id: $('#accountFilter').val(),
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val()
            }, function(res) {

                $('#accOpening').text(res.opening.toFixed(2));
                $('#accDebit').text(res.total_debit.toFixed(2));
                $('#accCredit').text(res.total_credit.toFixed(2));
                $('#accClosing').text(res.closing.toFixed(2));

                $('#accountSummary').show();
            });
        }
        // $('#accountFilter, #fromDate, #toDate').on('change', function() {
        //     loadAccountSummary();
        //     $('.AjaxDataTable').DataTable().ajax.reload();
        // });
    </script>
@endsection
