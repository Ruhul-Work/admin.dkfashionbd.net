{{-- resources/views/backend/account/account_transfers/create.blade.php --}}

<form data-ajax="true"
      method="POST"
      action="{{ route('account-transfers.store') }}">

    @csrf

    <div class="modal-header">
        <h6 class="modal-title">Account Transfer</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body  p-24">

        <div class="mb-3">
            <label class="form-label">Transfer Date</label>
            <input type="date" name="transfer_date"
                   class="form-control form-control-sm"
                   value="{{ now()->toDateString() }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">From Account (This Branch)</label>
            <select name="from_account_id" class="form-control form-control-sm" required>
                <option value="">-- Select --</option>
                @foreach($fromAccounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">To Account (Same or Different Branch)</label>
            <select name="to_branch_account_id" class="form-control form-control-sm" required>
                <option value="">-- Select --</option>
                @foreach($toAccounts as $acc)
                    <option value="{{ $acc->branch_account_id }}">
                        {{ $acc->account_name }}
                        @if((int) $acc->branch_id === (int) current_branch_id())
                            (This Branch)
                        @else
                            ({{ $acc->branch_name }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div> 

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01"
                   name="amount"
                   class="form-control form-control-sm" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" rows="2"
                      class="form-control form-control-sm"></textarea>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success btn-sm d-flex">Transfer<iconify-icon icon="mdi:swap-horizontal" class="menu-icon text-lg"></iconify-icon></button>
    </div>
</form>
