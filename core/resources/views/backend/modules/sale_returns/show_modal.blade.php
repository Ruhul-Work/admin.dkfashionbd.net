{{-- Modal Header --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Sale Return Details
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- Modal Body --}}
<div class="modal-body">

    {{-- 🔹 Basic Info --}}
    <div class="row mb-3 text-sm">
        <div class="col-md-6">
            <strong>Invoice:</strong> {{ $saleReturn->sale->invoice_no }} <br>
            <strong>Customer:</strong> {{ $saleReturn->customer?->name ?? 'Walk In' }} <br>
            <strong>Return Date:</strong> {{ $saleReturn->return_date }}
        </div>
        <div class="col-md-6 text-end">
            <span class="badge bg-info">
                Status: {{ ucfirst($saleReturn->status) }}
            </span>
        </div>
    </div>

    {{-- 🔹 Return Items --}}
    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered">
            <thead class="table-light text-sm">
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Refund</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($saleReturn->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end text-danger">
                            {{ number_format($item->discount_amount, 2) }}
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($item->refund_amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 🔹 Refund Summary --}}
    @php
        $paid = $saleReturn->payments->sum('amount');
        $due  = $saleReturn->total_refund - $paid;
    @endphp

    <div class="row text-sm">
        <div class="col-md-6">
            <strong>Total Refund:</strong>
        </div>
        <div class="col-md-6 text-end fw-semibold">
            {{ number_format($saleReturn->total_refund, 2) }}
        </div>

        <div class="col-md-6 text-success">
            <strong>Refunded:</strong>
        </div>
        <div class="col-md-6 text-end text-success fw-semibold">
            {{ number_format($paid, 2) }}
        </div>

        <div class="col-md-6 text-danger">
            <strong>Due:</strong>
        </div>
        <div class="col-md-6 text-end text-danger fw-semibold">
            {{ number_format($due, 2) }}
        </div>
    </div>

    {{-- 🔹 Payments (optional but recommended) --}}
    @if ($saleReturn->payments->count())
        <hr>
        <h6 class="text-sm fw-semibold mb-2">Refund Payments</h6>

        <table class="table table-sm table-borderless text-sm">
            @foreach ($saleReturn->payments as $pay)
                <tr>
                    <td>{{ $pay->paymentType?->name ?? '-' }}</td>
                    <td>{{ $pay->payment_date }}</td>
                    <td class="text-end">{{ number_format($pay->amount, 2) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

</div>

{{-- Modal Footer --}}
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
        Close
    </button>

    @if ($due > 0)
        <button
            class="btn btn-danger btn-sm AjaxModal"
            data-size="md"
            data-ajax-modal="{{ route('pos.saleReturns.payment.modal', $saleReturn->id) }}" data-onsuccess="saleReturnPayIndex.onSaved">
            Receive Refund
        </button>
    @endif
</div>

<script>
     window.SaleReturnIndex = {
            onReload: function() {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            }
        };

        window.saleReturnPayIndex = {
            onSaved: function() {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Refund Added',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }

                $('#AjaxModal').modal('hide');
            }
        };

</script>