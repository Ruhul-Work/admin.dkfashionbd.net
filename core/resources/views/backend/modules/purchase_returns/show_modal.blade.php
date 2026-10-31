{{-- Modal Header --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Purchase Return: {{ $order->return_number }}
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
            <span class="badge bg-{{ $order->total_amount > $order->paid_amount ? 'info' : 'success' }}">
                {{ $order->total_amount > $order->paid_amount ? 'Partially Paid' : 'Paid' }}
            </span><br>
            <strong>Date:</strong> {{ optional($order->created_at)->format('d M Y') }}
        </div>
    </div>

    {{-- Return Items --}}
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
            + {{ number_format($order->shipping_charge, 2) }}<br>
            - {{ number_format($order->discount, 2) }}<br>
            {{ number_format($order->total_amount, 2) }}<br>
            <span class="text-success">{{ number_format($order->paid_amount, 2) }}</span><br>
            <span class="text-danger">{{ number_format($order->due_amount, 2) }}</span>
        </div>
    </div>

    {{-- Payments --}}
    @if ($order->purchaseReturnPayment->count())
        <hr>
        <h6 class="text-sm fw-semibold mb-2">Payments</h6>

        <table class="table table-sm table-borderless text-sm">
            @foreach ($order->purchaseReturnPayment as $p)
                <tr>
                    <td>{{ optional($p->created_at)->format('d-m-Y') }}</td>
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
        data-onsuccess="purchaseReturnPayIndex.onSaved"
        data-ajax-modal="{{ route('purchase.return.payment.modal', $order->id) }}">
        Add Payment
    </button>
</div>

<script>
    window.purchaseReturnPayIndex = {
        onSaved: function () {
            $('.AjaxDataTable').DataTable().ajax.reload(null, false);

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Added',
                    timer: 1200,
                    showConfirmButton: false
                });
            }

            $('#AjaxModal').modal('hide');
        }
    };
</script>
