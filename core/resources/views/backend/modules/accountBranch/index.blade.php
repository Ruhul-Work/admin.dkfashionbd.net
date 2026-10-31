@extends('backend.layouts.master')

@section('meta')
    <title>Branch Account Assignment</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Branch Accounts</h6>
            <p class="text-muted m-0">Assign accounts to branches</p>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="hover-text-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Branch Accounts</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('branch-accounts.assign') }}">
                @csrf

                {{-- Branch Select --}}
                <div class="mb-4 col-md-4">
                    <label class="form-label fw-semibold">Select Branch</label>
                    <select name="branch_id" id="branchSelect" class="form-control" required>
                        <option value="">-- Select Branch --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Accounts List --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-3">Accounts</label>

                    <div class="row">
                        @foreach ($accounts as $account)
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center gap-2">

                                    {{-- Assign --}}
                                    <input class="form-check-input account-checkbox" type="checkbox" name="account_ids[]"
                                        value="{{ $account->id }}" id="acc{{ $account->id }}">

                                    {{-- Default --}}
                                    <input class="form-check-input account-default-radio" type="radio"
                                        name="default_account_id" value="{{ $account->id }}" id="def{{ $account->id }}"
                                        disabled>

                                    <label class="form-check-label mb-0" for="acc{{ $account->id }}">
                                        {{ $account->name }}
                                        <span class="text-muted">({{ $account->type?->name }})</span>
                                    </label>

                                    <small class="text-danger  ms-1 default-label d-none" id="lbl{{ $account->id }}">
                                        Default
                                    </small>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        Save Assignment
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('script')
    <script>
        const loadAssignedAccountsUrl = "{{ route('branch-accounts.assigned', ':id') }}";

        $('#branchSelect').on('change', function() {

            let branchId = $(this).val();

            // reset all
            $('.account-checkbox').prop('checked', false);

            if (!branchId) return;

            let url = loadAssignedAccountsUrl.replace(':id', branchId);

            $.get(url, function(res) {

                $('.account-checkbox').prop('checked', false);
                $('.account-default-radio').prop('checked', false).prop('disabled', true);

                if (res.account_ids) {
                    res.account_ids.forEach(function(id) {
                        $('#acc' + id).prop('checked', true);
                        $('#def' + id).prop('disabled', false);
                    });
                }

                if (res.default_account_id) {
                    $('#def' + res.default_account_id).prop('checked', true);
                }
            });
        });

        // Enable / disable default radio based on checkbox
        $(document).on('change', '.account-checkbox', function() {

            let accountId = $(this).val();
            let radio = $('#def' + accountId);

            if ($(this).is(':checked')) {
                radio.prop('disabled', false);
            } else {
                radio.prop('checked', false)
                    .prop('disabled', true);
                $('#lbl' + accountId).addClass('d-none');
            }
        });

        // When default radio changes, toggle label
        $(document).on('change', '.account-default-radio', function() {

            $('.default-label').addClass('d-none'); // hide all

            if ($(this).is(':checked')) {
                let id = $(this).val();
                $('#lbl' + id).removeClass('d-none');
            }
        });
      
        // Form submit validation
        $('form').on('submit', function(e) {
            $('.account-default-radio:checked').prop('disabled', false);
            if ($('.account-checkbox:checked').length > 0 &&
                !$('.account-default-radio:checked').length) {

                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Default account required',
                    text: 'Please select one default account'
                });
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
