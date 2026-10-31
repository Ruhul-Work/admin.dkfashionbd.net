@extends('backend.layouts.master')

@section('meta')
    <title>Accounts</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Accounts</h6>
            <p class="text-muted m-0">Chart of Accounts</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Accounts</li>
        </ul>
    </div>

    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Accounts</h5>

            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

                @perm('accounts.store')
                    <button class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2 AjaxModal"
                        data-ajax-modal="{{ route('accounts.createModal') }}" data-size="lg"
                        data-onsuccess="AccountsIndex.onSaved">
                        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                        Add Account
                    </button>
                @endperm
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="accountsTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Account Name</th>
                        <th>Type</th>
                        <th>Bank Name</th>
                        <th>Bank Acc. No.</th>
                        <th>Details</th>
                        <th>Balance</th>
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
        var DATATABLE_URL = "{{ route('accounts.list.ajax') }}";

        window.AccountsIndex = {
            onSaved: function() {
                if ($.fn.dataTable) {
                    $('#accountsTable').DataTable().ajax.reload(null, false);
                }
            }
        };

        // delete / disable account
        $(document).on('click', '.btn-account-del', function(e) {
            e.preventDefault();

            let url = $(this).data('url');

            Swal.fire({
                icon: 'warning',
                title: 'Disable this account?',
                text: 'This account will no longer be usable',
                showCancelButton: true,
                confirmButtonText: 'Yes, disable'
            }).then(res => {
                if (res.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).done(function() {
                        $('#accountsTable').DataTable().ajax.reload(null, false);
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to disable account'
                        });
                    });
                }
            });
        });

        // load account balance
        window.ACCOUNT_BALANCE_URL = "{{ route('accounts.balance', ':id') }}";

        $(document).on('click', '.btn-load-balance', function() {

            let accountId = $(this).data('account-id');
            let url = window.ACCOUNT_BALANCE_URL.replace(':id', accountId);

            let balanceSpan = $('.account-balance[data-id="' + accountId + '"]');
            balanceSpan.html('<i class="mdi mdi-loading mdi-spin"></i>');

            $.get(url, function(res) {
                balanceSpan.text(res.balance);
            }).fail(function(xhr) {

                balanceSpan.text('ERR');

                let msg = 'Could not load balance';

                if (xhr.status === 403) {
                    msg = 'This account is not assigned to your branch';
                } else if (xhr.status === 404) {
                    msg = 'Account not found';
                } else if (xhr.status === 422) {
                    msg = 'Please select a branch to calculate balance';
                } else if (xhr.status === 500) {
                    msg = 'Server error while calculating balance';
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: msg,
                    });
                }
            });
        });
    </script>
@endsection
