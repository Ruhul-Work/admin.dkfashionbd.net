{{-- ================= MODAL HEADER ================= --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Payment Details
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- ================= MODAL BODY ================= --}}
<div class="modal-body text-sm">

    {{-- Invoice + Amount Highlight --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted small">Invoice</div>
            <div class="fw-semibold">
                {{ optional($payment->sale)->invoice_no ?? '—' }}
            </div>
        </div>

        <div class="text-end">
            <div class="text-muted small">Paid Amount</div>
            <div class="fs-6 fw-bold text-success">
                {{ number_format($payment->amount, 2) }}
            </div>
        </div>
    </div>

    <hr class="my-2">

    {{-- Details Grid --}}
    <div class="row g-2">

        <div class="col-md-6">
            <div class="text-muted small">Payment Method</div>
            <div class="fw-medium">
                {{ ucfirst($payment->payment_type) }}
            </div>
        </div>

        <div class="col-md-6 text-end">
            <div class="text-muted small">Paid At</div>
            <div class="fw-medium">
                {{ optional($payment->paid_at)->format('Y-m-d H:i') ?? '—' }}
            </div>
        </div>

        <div class="col-md-6 mt-2">
            <div class="text-muted small">Received By</div>
            <div class="fw-medium">
                {{ $payment->received_by ?? '—' }}
            </div>
        </div>

        @if ($payment->reference)
            <div class="col-md-6 mt-2 text-end">
                <div class="text-muted small">Reference</div>
                <div class="fw-medium">
                    {{ $payment->reference }}
                </div>
            </div>
        @endif

    </div>

    {{-- Optional Note --}}
    @if ($payment->note)
        <hr class="my-2">
        <div>
            <div class="text-muted small">Note</div>
            <div class="fst-italic">
                {{ $payment->note }}
            </div>
        </div>
    @endif

</div>

{{-- ================= MODAL FOOTER ================= --}}
<div class="modal-footer">
    <button type="button"
            class="btn btn-secondary btn-sm"
            data-bs-dismiss="modal">
        Close
    </button>
</div>
