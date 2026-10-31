@extends('backend.layouts.master')

@section('meta')
    <title>Daily Payment Summary</title>
@endsection

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-0">Daily Payment Summary</h6>
        <p class="m-0">Method wise daily collection</p>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Summary List</h5>
        <div class="actions-bar d-flex align-items-center gap-2">
            <div id="tableSearch" class="search-input"></div>
        </div>
    </div>

    <div class="card-body">
        <table class="table bordered-table table-scroll mb-0 AjaxDataTable"
               id="dailyPaymentTable"
               style="width:100%">
            <thead>
                <tr>
                    <th width="60">S.L</th>
                    <th>Date</th>
                    <th class="">Cash</th>
                    <th class="">bKash</th>
                    <th class="">Card</th>
                    <th class="">Total</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@endsection

@section('script')
<script>
    var DATATABLE_URL = "{{ route('pos.reports.daily-payment.list.ajax') }}";

    window.DailyPaymentIndex = {
        onSettlementDone: function () {
            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
        }
    };
</script>
@endsection
