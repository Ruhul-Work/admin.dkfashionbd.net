@extends('backend.layouts.master')

@section('meta')
    <title>Employee Ledger</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Employee Management</h6>
            <p class="text-muted m-0">Employee salary ledger</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Employee Ledger</li>
        </ul>
    </div>

    <div class="card mb-24">
        <div class="card-body">
            <form id="ledgerFilterForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-sm">Employee</label>
                    <select name="employee_id" id="ledgerEmployee" class="form-select radius-8" required>
                        <option value="">Select employee</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm">Start Date</label>
                    <input type="date" name="start_date" id="ledgerStart" class="form-control radius-8">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm">End Date</label>
                    <input type="date" name="end_date" id="ledgerEnd" class="form-control radius-8">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-24">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Total Assigned Salary</h6>
                    <h4 class="mb-0 fw-bold" id="totalAssigned">0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Total Paid Salary</h6>
                    <h4 class="mb-0 fw-bold" id="totalPaid">0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Current Balance</h6>
                    <h4 class="mb-0 fw-bold" id="currentBalance">0.00</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card basic-data-table">
        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0" id="ledgerTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Direction</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let ledgerTable = null;

        function initLedgerTable() {
            ledgerTable = $('#ledgerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employee.ledger.list') }}",
                    type: 'POST',
                    data: function(d) {
                        d.employee_id = $('#ledgerEmployee').val();
                        d.start_date = $('#ledgerStart').val();
                        d.end_date = $('#ledgerEnd').val();
                        d._token = '{{ csrf_token() }}';
                    },
                    dataSrc: function(json) {
                        if (json.summary) {
                            $('#totalAssigned').text(json.summary.assigned);
                            $('#totalPaid').text(json.summary.paid);
                            $('#currentBalance').text(json.summary.balance);
                        }
                        return json.data;
                    }
                },
                columns: [
                    { data: 0 },
                    { data: 1 },
                    { data: 2 },
                    { data: 3 },
                    { data: 4 },
                    { data: 5 },
                ]
            });
        }

        $(document).ready(function() {
            initLedgerTable();
            $('#ledgerFilterForm').on('submit', function(e) {
                e.preventDefault();
                ledgerTable.ajax.reload();
            });
        });
    </script>
@endsection