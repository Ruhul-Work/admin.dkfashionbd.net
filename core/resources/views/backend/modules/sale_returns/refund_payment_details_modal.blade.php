{{-- ================= MODAL HEADER ================= --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Refund Payment Details
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- ================= MODAL BODY ================= --}}
<div class="modal-body text-sm">

    {{-- Return Ref + Amount --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="text-muted small">Sale Return</div>
            <div class="fw-semibold">
                SALE-REFUND-{{ optional($payment->saleReturn)->id ?? '—' }}
            </div>
        </div>

        <div class="text-end">
            <div class="text-muted small">Refunded Amount</div>
            <div class="fs-6 fw-bold text-danger">
                {{ number_format($payment->amount, 2) }}
            </div>
        </div>
    </div>

    <hr class="my-2">

    {{-- Details --}}
    <div class="row g-2">

        <div class="col-md-6">
            <div class="text-muted small">Payment Method</div>
            <div class="fw-medium">
              {{ $payment->paymentType->name ?? ucfirst($payment->payment_type) }}
            </div>
        </div>

        <div class="col-md-6 text-end">
            <div class="text-muted small">Paid At</div>
            <div class="fw-medium">
                {{ optional($payment->payment_date)->format('Y-m-d H:i') ?? '—' }}
            </div>
        </div>

        <div class="col-md-6 mt-2">
            <div class="text-muted small">Processed By</div>
            <div class="fw-medium">
                {{ $payment->createdBy->name ?? '—' }}
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
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
        Close
    </button>
</div>
