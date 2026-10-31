<div class="modal-header bg-light d-flex justify-content-between align-items-center">
    <div>
        <h6 class="modal-title mb-0">Sale Details</h6>
       
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary" onclick="printSaleDetails()">
            <iconify-icon icon="mdi:printer-outline"></iconify-icon>
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
</div>

<div class="modal-body text-dark" id="salePrintArea">

    {{-- ===== Top Info ===== --}}
    <div class="row mb-3">
        <div class="col-6">
            <div><strong>Customer:</strong></div>
            <div>{{ $sale->customer?->name ?? 'Walk In Customer' }}</div>
            <div class="text-muted small">
                Sold by: {{ $sale->user?->name ?? '-' }}
            </div>
            <div>
               {{-- <strong>Invoice No:</strong> {{ $sale->invoice_no }} --}}
                <small class="text-muted"><strong>Invoice No#</strong>{{ $sale->invoice_no }}</small>
            </div>
        </div>

        <div class="col-6 text-end mb-3">
            <div>
                <strong> {{ config('app.name') }}</strong><br>
                {{ $sale->branch?->name }}<br>
                Phone: {{ $sale->branch?->phone ?? '-' }}<br>
            </div>
            <div><strong>Status:</strong>
                <span
                    class="badge 
                    {{ $sale->status === 'delivered' ? 'bg-success' : 'bg-warning text-dark' }}">
                    {{ strtoupper($sale->status) }}
                </span>
            </div>
            <div class="text-muted small">
                {{ $sale->created_at->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    {{-- ===== Items Table ===== --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ===== Totals ===== --}}
    <div class="row mt-3">
        <div class="col-6">
            <strong>Payments</strong>
            <ul class="list-unstyled mb-0 small">
                @foreach ($sale->payments as $pay)
                    <li>
                        {{ ucfirst($pay->payment_type) }} :
                        {{ number_format($pay->amount, 2) }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-6 text-end">
            <div>Subtotal: {{ number_format($sale->subtotal, 2) }}</div>
            <div>Discount: {{ number_format($sale->discount, 2) }}</div>
            <div>Shipping: {{ number_format($sale->shipping_charge, 2) }}</div>
            <div>Coupon discount: {{ number_format($sale->coupon_discount, 2) }}</div>
            <div>Points discount: {{ number_format($sale->point_discount, 2) }}</div>
            <hr class="my-1">
            <div class="fw-bold fs-6">
                Total: {{ number_format($sale->total, 2) }}
            </div>
            <div class="text-success fw-semibold">
                Paid: {{ number_format($sale->paid_amount, 2) }}
            </div>
            <div class="text-danger fw-semibold">
                due: {{ number_format($sale->due_amount, 2) }}
            </div>
        </div>
    </div>
</div>

<script>
    function printSaleDetails() {

        let content = document.getElementById('salePrintArea').innerHTML;
        let win = window.open('', '', 'width=800,height=600');

        win.document.write(`
        <html>
        <head>
            <title>Sale Invoice</title>
            <style>
                
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .row {
                    display: flex;
                    width: 100%;
                    }
                    .col-6 {
                        width: 50%;
                    }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 6px; }
                th { background: #f5f5f5; }
                .text-end { text-align: right; }
                .text-center { text-align: center; }
                hr { border: none; border-top: 1px solid #ccc; margin: 8px 0; }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${content}
        </body>
        </html>
    `);

        win.document.close();
    }
</script>
