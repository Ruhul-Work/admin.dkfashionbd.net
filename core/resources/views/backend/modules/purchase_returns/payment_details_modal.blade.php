{{-- ================= MODAL HEADER ================= --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Purchase Return Payment Details
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- ================= MODAL BODY ================= --}}
<div class="modal-body text-sm">

    {{-- Supplier Info --}}
    <div class="mb-3">
        <strong>Supplier:</strong> {{ $payment->supplier->name ?? '—' }} <br>
        <strong>Payment Date:</strong>
        {{ optional($payment->created_at)->format('d M Y') }}
    </div>

    <hr>

    {{-- Payment Info --}}
    <div class="row mb-2">
        <div class="col-6">Payment Method</div>
        <div class="col-6 text-end fw-semibold">
            {{ ucfirst($payment->method ?? '-') }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">Reference</div>
        <div class="col-6 text-end fw-semibold">
            {{ $payment->reference ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6 text-success fw-semibold">Amount Refunded</div>
        <div class="col-6 text-end text-success fw-bold">
            {{ number_format($payment->amount, 2) }}
        </div>
    </div>

    @if ($payment->notes)
        <hr>
        <div>
            <strong>Notes:</strong><br>
            {{ $payment->notes }}
        </div>
    @endif

</div>

{{-- ================= MODAL FOOTER ================= --}}
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
        Close
    </button>
</div>
