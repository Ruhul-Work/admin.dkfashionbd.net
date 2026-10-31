<div class="modal-header py-16 px-24 border-0">
    <h5 class="modal-title">Edit Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body modal-lg p-24">
    <form method="POST" action="{{ route('accounts.update', $account->id) }}" data-ajax="true">

        @csrf
        @method('POST')

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Account Name</label>
                <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Account Type</label>
                <select name="account_type_id" class="form-control js-account-type" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" @selected($account->account_type_id == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Opening Balance --}}
            {{-- <div class="col-md-6">
                <label class="form-label">Opening Balance</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control"
                    value="{{ $account->openingBalances->first()?->amount ?? 0 }}">
            </div> --}}


            {{-- Bank Fields --}}
            <div class="col-md-6 bank-fields">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="{{ $account->bank_name }}">
            </div>

            <div class="col-md-6 bank-fields">
                <label class="form-label">Bank Account Number</label>
                <input type="text" name="bank_account_no" class="form-control"
                    value="{{ $account->bank_account_no }}">
            </div>



            <div class="col-md-12 bank-fields">
                <label class="form-label">Bank Details</label>
                <textarea name="bank_details" class="form-control">{{ $account->bank_details }}</textarea>
            </div>

            {{-- Allow Negative --}}
            <div class="col-md-6 mb-8">
                <label class="form-label text-sm mb-8">Allow Negative Balance ?</label>
                <div class="form-switch switch-purple d-flex align-items-center gap-3">
                    <input type="hidden" name="allow_negative" value="0">
                    <input class="form-check-input" type="checkbox" name="allow_negative" value="1"
                        @checked($account->allow_negative)>
                    <label class="form-check-label">Allow</label>
                </div>
                <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
            </div>


        </div>

        <div class="text-end mt-4">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<script>
    (function() {

        function toggleBankFields() {
            let typeText = $('.js-account-type option:selected').text().toLowerCase();

            if (typeText.includes('bank') || typeText.includes('cash') === false) {
                $('.bank-fields').show();
            } else {
                $('.bank-fields').hide();
            }
        }

        toggleBankFields();
        $('.js-account-type').on('change', toggleBankFields);

    })();
</script>
