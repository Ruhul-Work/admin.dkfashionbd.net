<form data-ajax="true" aria-hidden="false"
      method="POST"
      action="{{ route('pos.sales.payments.store', $sale->id) }}">

    @csrf

    {{-- ================= MODAL HEADER ================= --}}
    <div class="modal-header">
        <h6 class="modal-title">Receive Payment</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    {{-- ================= MODAL BODY ================= --}}
    <div class="modal-body">

        {{-- Invoice Info --}}
        <div class="mb-3">
            <label class="form-label text-muted">Invoice</label>
            <input type="text"
                   class="form-control form-control-sm"
                   value="{{ $sale->invoice_no }}"
                   disabled>
        </div>

        {{-- Due Summary --}}
        <div class="alert alert-light border mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Due Amount</span>
                <strong class="fs-6">{{ number_format($sale->due_amount, 2) }}</strong>
            </div>
        </div>

        <div class="mb-3">
            <small class="text-muted">
                Remaining Due:
                <strong id="remainingDueText">
                    {{ number_format($sale->due_amount, 2) }}
                </strong>
            </small>
        </div>

        <hr>

        {{-- Payment Row --}}
        <div class="row g-2 align-items-end">

            <div class="col-md-6">
                <label class="form-label">Payment Method</label>
                <select name="payments[0][method]"
                        class="form-control form-control-sm">
                    @foreach ($paymentTypes as $pt)
                        <option value="{{ $pt->slug }}">
                            {{ $pt->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Amount</label>
                <input type="number"
                       step="0.01"
                       name="payments[0][amount]"
                       class="form-control form-control-sm"
                       value="{{ $sale->due_amount }}"
                       required>
            </div>

        </div>

    </div>

    {{-- ================= MODAL FOOTER ================= --}}
    <div class="modal-footer">
        <button type="button"
                class="btn btn-outline-secondary btn-sm"
                data-bs-dismiss="modal">
            Cancel
        </button>

        <button type="submit"
                class="btn btn-success btn-sm">
            Receive Payment
        </button>
    </div>
</form>

{{-- ================= JS ================= --}}
<script>
    // Callback after successful save
    window.posSalePaymentIndex = {
        onSaved: function (res) {
            if (window.$ && $.fn.DataTable) {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            }

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Received',
                    text: res?.message || 'Payment received successfully',
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        }
    };

    // Remaining due live calculation
    (function () {
        const due = parseFloat({{ $sale->due_amount }});
        const amountInput = document.querySelector('[name="payments[0][amount]"]');
        const remainingText = document.getElementById('remainingDueText');

        if (!amountInput || !remainingText) return;

        amountInput.addEventListener('input', function () {
            let paid = parseFloat(this.value || 0);

            if (paid > due) {
                paid = due;
                this.value = due.toFixed(2);
            }

            const remaining = (due - paid).toFixed(2);
            remainingText.textContent = remaining;
        });
    })();
    
</script>
