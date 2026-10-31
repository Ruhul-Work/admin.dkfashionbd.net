<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Refund Payment
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST"
      action="{{ route('pos.saleReturns.payment.store', $saleReturn->id) }}"
      class="AjaxForm" data-ajax="true"
      data-onsuccess="saleReturnPayIndex.onSaved">

    @csrf

    <div class="modal-body">

        {{-- Summary --}}
        <div class="mb-3 text-sm">
            <div class="d-flex justify-content-between">
                <strong>Total Refund</strong>
                <strong>{{ number_format($saleReturn->total_refund, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between text-success">
                <strong>Already Refunded</strong>
                <strong>{{ number_format($paid, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between text-danger">
                <strong>Due Refund</strong>
                <strong>{{ number_format($due, 2) }}</strong>
            </div>
        </div>

        <hr>

        {{-- Payment --}}
        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_type_id" class="form-control form-control-sm" required>
                <option value="">-- Select Method --</option>
                @foreach ($paymentTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Refund Amount</label>
            <input type="number"
                   name="amount"
                   class="form-control form-control-sm"
                   step="0.01"
                   min="0.01"
                   max="{{ $due }}"
                   value="{{ $due }}"
                   required>
            <small class="text-muted">
                Max refundable: {{ number_format($due, 2) }}
            </small>
        </div>

        <div class="mb-2">
            <label class="form-label">Note (optional)</label>
            <textarea name="notes"
                      rows="2"
                      class="form-control form-control-sm"></textarea>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary btn-sm"
                data-bs-dismiss="modal">
            Cancel
        </button>

        <button type="submit"
                class="btn btn-danger btn-sm">
            Save Refund
        </button>
    </div>

</form>
