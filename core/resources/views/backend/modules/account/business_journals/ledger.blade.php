@extends('backend.layouts.master')

@section('meta')
    <title>Business Journal Ledger</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Business Journal Ledger</h6>
            <p class="text-muted m-0">Journal-wise incoming and outgoing balance flow</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Business Journal Ledger</li>
        </ul>
    </div>

    <ul class="nav nav-tabs mb-0 border-bottom">
        <li class="nav-item">
            <a class="nav-link fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.index') }}">
                <iconify-icon icon="mdi:book-open-variant"></iconify-icon>
                Journal List
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.records.index') }}">
                <iconify-icon icon="mdi:table-edit"></iconify-icon>
                Journal Records
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.ledger.index') }}">
                <iconify-icon icon="mdi:chart-box-outline"></iconify-icon>
                Ledger
            </a>
        </li>
    </ul>

    <div class="card mb-3 shadow-lg rounded-top-0">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:filter-linear" class="menu-icon"></iconify-icon>
                        Filter
                    </h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Business Journal</label>
                    <select id="journalFilter" class="form-control form-control-sm">
                        <option value="">Select Journal</option>
                        @foreach ($journals as $journal)
                            <option value="{{ $journal->id }}">{{ $journal->name }}{{ $journal->category ? ' - ' . $journal->category : '' }}</option>
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
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1" id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="text-lg"></iconify-icon>
                        Load
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3 d-none" id="ledgerSummary">
        <div class="col-md-3">
            <div class="card border-0 bg-warning-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Opening Balance</p>
                    <h6 class="mb-0 text-dark" id="sumOpening">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-success-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Incoming</p>
                    <h6 class="mb-0 text-success" id="sumIncoming">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-danger-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Total Outgoing</p>
                    <h6 class="mb-0 text-danger" id="sumOutgoing">0.00</h6>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-primary-50">
                <div class="card-body">
                    <p class="mb-1 text-muted">Closing Balance</p>
                    <h6 class="mb-0 text-primary" id="sumClosing">0.00</h6>
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
                        <th>Date</th>
                        <th>Journal</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Narration</th>
                        <th class="text-end">Amount</th>
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
        var DATATABLE_URL = "{{ route('business-journals.ledger.list.ajax') }}";

        $('#btnFilter').on('click', function() {
            let journalId = $('#journalFilter').val();
            let fromDate = $('#fromDate').val();
            let toDate = $('#toDate').val();

            if (!journalId) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select a business journal'
                });
                return;
            }

            if (!fromDate || !toDate) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select both From and To date'
                });
                return;
            }

            let url = DATATABLE_URL +
                '?business_journal_id=' + (journalId ?? '') +
                '&from_date=' + (fromDate ?? '') +
                '&to_date=' + (toDate ?? '');

            $('.AjaxDataTable')
                .DataTable()
                .ajax
                .url(url)
                .load();

            loadLedgerSummary();
        });

        function loadLedgerSummary() {
            let journalId = $('#journalFilter').val();

            if (!journalId) {
                $('#ledgerSummary').addClass('d-none');
                return;
            }

            $.get("{{ route('business-journals.ledger.summary') }}", {
                business_journal_id: journalId,
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val()
            }, function(res) {
                $('#sumOpening').text(res.opening.toFixed(2));
                $('#sumIncoming').text(res.total_incoming.toFixed(2));
                $('#sumOutgoing').text(res.total_outgoing.toFixed(2));
                $('#sumClosing').text(res.closing.toFixed(2));
                $('#ledgerSummary').removeClass('d-none');
            });
        }
    </script>
@endsection
