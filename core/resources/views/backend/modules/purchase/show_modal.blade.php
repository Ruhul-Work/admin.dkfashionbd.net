{{-- Modal Header --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Purchase Order: {{ $order->po_number }}
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- Modal Body --}}
<div class="modal-body">

    {{-- Top Info --}}
    <div class="row mb-3 text-sm">
        <div class="col-md-6">
            <strong>Supplier:</strong> {{ $order->supplier->name }} <br>
            <strong>Phone:</strong> {{ $order->supplier->phone ?? '-' }} <br>
            <strong>Email:</strong> {{ $order->supplier->email ?? '-' }}
        </div>

        <div class="col-md-6 text-end">
            <span class="badge bg-info">{{ ucfirst($order->status) }}</span><br>
            <strong>Payment:</strong>
            <span class="badge
                {{ $order->payment_status === 'paid' ? 'bg-success' :
                   ($order->payment_status === 'partially_paid' ? 'bg-warning' : 'bg-secondary') }}">
                {{ $order->payment_status }}
            </span><br>
            <strong>Date:</strong> {{ optional($order->order_date)->format('d M Y') }}
        </div>
    </div>

    {{-- Items --}}
    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Cost</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $it)
                    <tr>
                        <td>{{ $it->product->name ?? '—' }}</td>
                        <td class="text-center">{{ $it->quantity }}</td>
                        <td class="text-end">{{ number_format($it->unit_cost, 2) }}</td>
                        <td class="text-end">{{ number_format($it->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div class="row text-sm">
        <div class="col-md-6">
            <strong>Subtotal</strong><br>
            <strong>Shipping</strong><br>
            <strong>Discount</strong><br>
            <strong>Total</strong><br>
            <strong class="text-success">Paid</strong><br>
            <strong class="text-danger">Outstanding</strong>
        </div>

        <div class="col-md-6 text-end fw-semibold">
            {{ number_format($order->subtotal, 2) }}<br>
            {{ number_format($order->shipping_amount, 2) }}<br>
            {{ number_format($order->subtotal + $order->shipping_amount - $order->total_amount, 2) }}<br>
            {{ number_format($order->total_amount, 2) }}<br>
            <span class="text-success">{{ number_format($paid, 2) }}</span><br>
            <span class="text-danger">{{ number_format($outstanding, 2) }}</span>
        </div>
    </div>

    {{-- Payments --}}
    @if ($order->payments->count())
        <hr>
        <h6 class="text-sm fw-semibold mb-2">Payments</h6>
        <table class="table table-sm table-borderless text-sm">
            @foreach ($order->payments as $p)
                <tr>
                    <td>{{ optional($p->payment_date)->format('d-m-Y') }}</td>
                    <td>{{ $p->method }}</td>
                    <td class="text-end">{{ number_format($p->amount, 2) }}</td>
                    <td class="text-end">{{ $p->reference }}</td>
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

    <button
        class="btn btn-warning btn-sm AjaxModal"
        data-size="md"
        data-onsuccess="purchasePayIndex.onSaved"
        data-ajax-modal="{{ route('purchase.orders.payment.modal', $order->id) }}">
        Add Payment
    </button>
</div>
