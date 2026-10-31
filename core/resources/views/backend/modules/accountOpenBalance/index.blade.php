@extends('backend.layouts.master')

@section('meta')
    <title>Opening Balances</title>

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Opening Balances</h6>
            <p class="text-muted m-0">Set branch & fiscal year wise opening balances</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="hover-text-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Opening Balances</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('opening-balances.save') }}">
                @csrf

                <div class="row g-3 mb-4">

                    {{-- Branch --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Branch</label>
                        <select id="branchSelect" name="branch_id" class="form-control" required>
                            <option value="">-- Select Branch --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fiscal Year --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fiscal Year</label>
                        <select id="fiscalYearSelect" name="fiscal_year_id" class="form-control" required>
                            <option value="">-- Select Fiscal Year --</option>
                            @foreach ($fiscalYears as $fy)
                                <option value="{{ $fy->id }}">
                                    {{ $fy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Accounts table --}}
                <div id="accountsWrapper" class="mt-3" style="display:none;">
                    <div class="table-responsive" style="width:70%">
                        <table class="table bordered-table">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Opening Balance</th>
                                </tr>
                            </thead>
                            <tbody id="accountsBody">
                                {{-- injected by JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-success">
                            Save Opening Balances
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
@endsection
@section('script')
    <script>
        const loadUrlTemplate = "{{ route('opening-balances.accounts', ['branch' => ':b', 'fiscalYear' => ':f']) }}";

        function tryLoadAccounts() {
            let branchId = $('#branchSelect').val();
            let fyId = $('#fiscalYearSelect').val();

            $('#accountsBody').empty();
            $('#accountsWrapper').hide();

            if (!branchId || !fyId) return;

            let url = loadUrlTemplate
                .replace(':b', branchId)
                .replace(':f', fyId);

            $.get(url, function(rows) {

                if (!rows.length) {
                    $('#accountsBody').html(
                        '<tr><td colspan="2" class="text-muted">No accounts assigned to this branch.</td></tr>'
                    );
                    $('#accountsWrapper').show();
                    return;
                }

                rows.forEach(function(r) {
                    $('#accountsBody').append(`
                    <tr>
                        <td>${r.name}</td>
                       <td>
                        <input type="number"
                            step="0.01"
                            name="balances[${r.id}]"
                            class="form-control opening-input"
                            data-account-id="${r.id}"
                            value="${r.amount}">
                        <small class="text-danger d-none balance-error">
                            Opening balance cannot be negative
                        </small>
                    </td>

                    </tr>
                `);
                });

                $('#accountsWrapper').show();
            });
        }

        $('#branchSelect, #fiscalYearSelect').on('change', tryLoadAccounts);

        // live validation (typing / blur)
        $(document).on('input', '.opening-input', function() {

            let val = parseFloat($(this).val());
            let error = $(this).siblings('.balance-error');

            if (!isNaN(val) && val < 0) {
                $(this).addClass('is-invalid');
                error.removeClass('d-none');
            } else {
                $(this).removeClass('is-invalid');
                error.addClass('d-none');
            }
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
