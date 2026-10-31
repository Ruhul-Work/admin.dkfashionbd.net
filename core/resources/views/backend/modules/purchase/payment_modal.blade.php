
@php
    // compute paid/outstanding safely (order may already have helper attributes)
    $paid = isset($order) ? (float) ($order->payments->sum('amount') ?? 0) : 0.0;
    $total = isset($order) ? (float) ($order->total_amount ?? ($order->subtotal + $order->shipping_amount ?? 0)) : 0.0;
    $outstanding = max(0.0, round($total - $paid, 2));
@endphp


 
      <form id="purchase-payment-form" data-ajax="true" method="POST" action="{{ route('purchase.orders.payments.store', $order->id) }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Add Payment — {{ $order->po_number ?? 'PO' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="modal_order_id" name="order_id" value="{{ $order->id }}">

          <div class="mb-2">
            <label class="form-label">Outstanding</label>
            <div><strong id="modal-outstanding">{{ number_format($outstanding, 2) }}</strong> <small class="text-muted">BDT</small></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="payment-amount">Amount <span class="text-danger">*</span></label>
            <input
              id="payment-amount"
              name="amount"
              type="number"
              step="0.01"
              min="0.01"
              max="{{ $outstanding }}"
              class="form-control"
              value="{{ number_format($outstanding, 2, '.', '') }}"
              required
            >
            <div class="invalid-feedback" id="error-amount"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="payment-method">Method</label>
            <select id="payment-method" name="method" class="form-control">
              <option value="cash">Cash</option>
              <option value="bkash">Bkash</option>
              <option value="card">Card</option>
              <option value="bank">Bank Transfer</option>
            </select>
            <div class="invalid-feedback" id="error-method"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="payment-reference">Reference</label>
            <input id="payment-reference" name="reference" type="text" class="form-control">
            <div class="invalid-feedback" id="error-reference"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="payment-note">Note</label>
            <textarea id="payment-note" name="notes" class="form-control" rows="2"></textarea>
            <div class="invalid-feedback" id="error-notes"></div>
          </div>

          {{-- optional: payment_date (defaults to now) --}}
          <input type="hidden" name="payment_date" value="{{ now()->toDateTimeString() }}">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm rounded-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="purchase-payment-submit" class="btn btn-dark btn-sm rounded-4">
            <span id="purchase-payment-submit-text">Submit</span>
            <span id="purchase-payment-submit-spinner" style="display:none;"> &nbsp; <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
          </button>
        </div>
      </form>
    

{{-- Inline script to handle AJAX submit and UX — safe to include in partial (it binds only to this modal's form) --}}
<script>
    window.purchasePayment = {
        onSaved: function() {
            // Reload payments table
            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            window.location.reload();
            // Optionally, update outstanding amount in the main order view
        }
    };
</script>
