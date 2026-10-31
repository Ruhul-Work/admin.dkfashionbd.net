@extends('backend.layouts.master')

@section('meta')
    <title>Account Transfers</title>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Account Transfers</h6>
            <p class="text-muted m-0">Transfer money between accounts</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="hover-text-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Account Transfers</li>
        </ul>
    </div>

    {{-- Card --}}
    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Transfer List</h5>

            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">

                {{-- Search --}}
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                {{-- Top buttons (export etc if you use) --}}
                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

                {{-- Create Transfer --}}
                @perm('account-transfers.create')
                    <button class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2 AjaxModal"
                        data-ajax-modal="{{ route('account-transfers.create') }}" data-size="md"
                        data-onsuccess="AccountTransferIndex.onSaved">
                        <iconify-icon icon="mdi:bank-transfer" class="text-xl"></iconify-icon>
                        New Transfer
                    </button>
                @endperm

            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="accountTransferTable"
                style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">SL</th>
                        <th>Voucher No</th>
                        <th>From Account</th>
                        <th>To Account</th>
                        <th class="text-end">Amount</th>
                        <th>Branch</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th style="width:100px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@section('script')
    <script>

        // Ajax source
        var DATATABLE_URL = "{{ route('account-transfers.list.ajax') }}";

        // After save reload table
        window.AccountTransferIndex = {
            onSaved: function(res) {
                if ($.fn.dataTable) {
                    $('#accountTransferTable').DataTable().ajax.reload(null, false);
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res?.msg || 'Account transfer completed',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            }
        };
    </script>
@endsection
