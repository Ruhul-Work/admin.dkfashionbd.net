@extends('backend.layouts.master')

@section('meta')
    <title>Expense Report</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Expense Report</h6>
            <p class="text-muted">Filtered expense listing</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Expense Report</li>
        </ul>
    </div>

    {{-- Filters --}}
    <div class="card mb-3 shadow-lg">
        <div class="card-body">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-12">
                    <h6 class="card-title mb-0 d-flex align-items-center gap-1"><iconify-icon icon="solar:filter-linear" class="menu-icon"></iconify-icon> Filter</h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" id="fromDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" id="toDate" class="form-control form-control-sm">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select id="categoryFilter" class="form-control-sm js-s2-ajax"
                        data-url="{{ route('expenseCategories.select2') }}" data-placeholder="All Categories">
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1" id="btnFilter">
                        <iconify-icon icon="material-symbols:search" class="menu-icon text-lg"></iconify-icon> search
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row mb-3" id="expenseSummary" style="display:none">
        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-warning-50 border-warning border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Draft Expenses</p>
                    <h5 class="mb-0 fw-semibold text-warning" id="sumDraft">0.00</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-success-50 border-success border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Posted Expenses</p>
                    <h5 class="mb-0 fw-semibold text-success" id="sumPosted">0.00</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start bg-primary-50 border-primary border-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Expenses</p>
                    <h5 class="mb-0 fw-semibold text-primary" id="sumTotal">0.00</h5>
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
            <table id="expenseReportTable" class="table bordered-table AjaxDataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Expense</th>
                        <th>Reference</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
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
    var DATATABLE_URL = "{{ route('reports.expenses.list.ajax') }}";
    var expenseTable = null;
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

    function loadExpenseSummary() {
        $.get("{{ route('reports.expenses.summary') }}", {
            from_date: $('#fromDate').val(),
            to_date: $('#toDate').val(),
            category_id: $('#categoryFilter').val()
        }, function(res) {
            $('#sumDraft').text(res.draft.toFixed(2));
            $('#sumPosted').text(res.posted.toFixed(2));
            $('#sumTotal').text(res.total.toFixed(2));
            $('#expenseSummary').slideDown(150);
        }).fail(function(xhr) {
            if (xhr.status === 422) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.msg || 'Please select a branch first' });
            }
        });
    }

    // function initExpenseTable() {
    //     if (expenseTable === null) {
    //         expenseTable = $('#expenseReportTable').DataTable({
    //             processing: true,
    //             serverSide: true,
    //             ajax: {
    //                 url: DATATABLE_URL,
    //                 type: 'POST',
    //                 data: function(d) {
    //                     d.from_date = $('#fromDate').val();
    //                     d.to_date = $('#toDate').val();
    //                     d.category_id = $('#categoryFilter').val();
    //                     d._token = '{{ csrf_token() }}';
    //                 }
    //             },
    //             order: [[0, 'desc']],
    //             lengthMenu: [10, 25, 50, 100]
    //         });
    //     }
    // }

    $('#btnFilter').on('click', function() {
        // Validate dates are provided
        if (!$('#fromDate').val() || !$('#toDate').val()) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'Please select both From and To dates' });
            return false;
        }

        var categoryId = $('#categoryFilter').val() || '';
        var url = DATATABLE_URL + '?from_date=' + $('#fromDate').val() + '&to_date=' + $('#toDate').val() + '&category_id=' + categoryId;

        loadExpenseSummary();
        
        var table = $('.AjaxDataTable').DataTable();
        table.ajax.url(url).load();
    });
</script>
@endsection
