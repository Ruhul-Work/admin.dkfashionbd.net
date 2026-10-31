<form data-ajax="true" method="POST" action="{{ route('business-journals.records.store') }}">
    @csrf

    <div class="modal-header">
        <h6 class="modal-title d-flex align-items-center gap-2">
            <iconify-icon icon="mdi:table-plus" class="text-success text-xl"></iconify-icon>
            New Journal Record
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body p-24">

        <div class="row g-16">

            {{-- Journal --}}
            <div class="col-12">
                <label class="form-label fw-semibold" for="bjr_journal">
                    Journal <span class="text-danger">*</span>
                </label>
                <select id="bjr_journal" name="business_journal_id" class="form-select form-select-sm" required>
                    <option value="">— Select Journal —</option>
                    @foreach($journals as $j)
                        <option value="{{ $j->id }}">
                            {{ $j->name }}
                            @if($j->category) ({{ $j->category }}) @endif
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block business_journal_id-error text-danger small"></div>
            </div>

            {{-- Payment Type --}}
            <div class="col-12">
                <label class="form-label fw-semibold d-block">
                    Payment Type <span class="text-danger">*</span>
                </label>
                <div class="d-flex gap-20 mt-4">
                    <div class="form-check d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="payment_type"
                               id="pt_incoming" value="Incoming" checked required style="margin-top: 0;">
                        <label class="form-check-label fw-medium text-success d-inline-flex align-items-center gap-1" for="pt_incoming">
                            <iconify-icon icon="mdi:arrow-down-circle-outline"></iconify-icon>
                            Incoming
                        </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="payment_type"
                               id="pt_outgoing" value="Outgoing" required style="margin-top: 0;">
                        <label class="form-check-label fw-medium text-danger d-inline-flex align-items-center gap-1" for="pt_outgoing">
                            <iconify-icon icon="mdi:arrow-up-circle-outline"></iconify-icon>
                            Outgoing
                        </label>
                    </div>
                </div>
                <div class="invalid-feedback d-block payment_type-error text-danger small"></div>
            </div>

            {{-- Date --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="bjr_date">
                    Date <span class="text-danger">*</span>
                </label>
                <input type="date" id="bjr_date" name="date"
                       class="form-control form-control-sm"
                       value="{{ now()->toDateString() }}" required>
                <div class="invalid-feedback d-block date-error text-danger small"></div>
            </div>

            {{-- Amount --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="bjr_amount">
                    Amount (৳) <span class="text-danger">*</span>
                </label>
                <input type="number" id="bjr_amount" name="amount" step="0.01" min="0.01"
                       class="form-control form-control-sm" placeholder="0.00" required>
                <div class="invalid-feedback d-block amount-error text-danger small"></div>
            </div>

            {{-- Account --}}
            <div class="col-12">
                <label class="form-label fw-semibold" for="bjr_account">
                    Account <span class="text-danger">*</span>
                </label>
                <select id="bjr_account" name="account_id" class="form-select form-select-sm" required>
                    <option value="">— Select Account —</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block account_id-error text-danger small"></div>
            </div>

            {{-- Narration --}}
            <div class="col-12">
                <label class="form-label fw-semibold" for="bjr_narration">
                    Narration <small class="text-muted">(Optional)</small>
                </label>
                <textarea id="bjr_narration" name="narration" rows="2"
                          class="form-control form-control-sm"
                          placeholder="Additional notes or description…"></textarea>
                <div class="invalid-feedback d-block narration-error text-danger small"></div>
            </div>

        </div>{{-- /.row --}}

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" data-bs-dismiss="modal">
            <iconify-icon icon="mdi:close"></iconify-icon>Close
        </button>
        <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1">
            <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
            Save Changes
        </button>
    </div>

</form>
