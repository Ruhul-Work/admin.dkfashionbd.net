<div class="modal-header py-16 px-24 border-0">
    <h5 class="modal-title">Create New Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body modal-lg p-24">
    <form method="POST" action="{{ route('accounts.store') }}" data-ajax="true">

        @csrf

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Account Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Account Type</label>
                <select name="account_type_id" class="form-control" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Currency</label>
                <input type="text" class="form-control" value="BDT" disabled>
            </div>


           

            {{-- <div class="col-md-6">
                <label class="form-label">Opening Balance</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control">
                <small class="text-muted">Negative value is allowed</small>
            </div> --}}

            <div class="col-md-6">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Bank Account Number</label>
                <input type="text" name="bank_account_no" class="form-control">
            </div>

            
             <div class="col-md-6 mb-8">
                <label class="form-label text-sm mb-8">Allow Negative Balance ?</label>
                <div class="form-switch switch-purple d-flex align-items-center gap-3">
                    <input type="hidden" name="allow_negative" value="0">
                    <input class="form-check-input" type="checkbox" name="allow_negative" value="1">
                    <label class="form-check-label">Allow</label>
                </div>
                <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Bank Account Details</label>
                <textarea name="bank_details" class="form-control"></textarea>
            </div>

           
            

        </div>

        <div class="text-end mt-4">
            <button class="btn btn-primary">Save Account</button>
        </div>
    </form>
</div>
