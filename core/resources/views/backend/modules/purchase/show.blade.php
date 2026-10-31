@extends('backend.layouts.master')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5>Purchase Order: {{ $order->po_number }}</h5>
            <div>
                <a href="{{ route('purchase.orders.show', $order->id) }}" class="btn btn-sm btn-outline-neutral-900"><iconify-icon icon="mdi:refresh" class="text-lg"></a>
                <a href="#" class="btn btn-sm btn-outline-primary-900" id="print-order"><iconify-icon icon="mdi:printer" class="text-lg"></a>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Supplier</h6>
                <p><strong>{{ $order->supplier->name }}</strong><br>{{ $order->supplier->phone ?? '' }}<br>{{ $order->supplier->email ?? '' }}
                </p>
            </div>
            <div class="col-md-6 text-end">
                <h6>Order Info</h6>
                <p>Status: <span class="badge bg-info">{{ ucfirst($order->status) }}</span><br>
                    Payment Status: <span id="payment-status-badge"
                        class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : ($order->payment_status === 'partially_paid' ? 'bg-warning' : 'bg-secondary') }}">{{ $order->payment_status }}</span><br>
                    Order Date: {{ optional($order->order_date)->format('d M Y') }}<br>
                    PO: {{ $order->po_number }}</p>

                @if ($order->purchase_invoice)
                    <p>Invoice: <a href="{{ asset($order->purchase_invoice) }}" target="_blank">View</a></p>
                    <img src="{{ asset($order->purchase_invoice) }}" alt="invoice" style="max-width:100px;">
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>SKU</th>
                            <th>Unit Cost</th>
                            <th>Qty</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $it)
                            <tr>
                                <td>{{ $it->product->name ?? '—' }}</td>
                                <td>{{ $it->sku }}</td>
                                <td>{{ number_format($it->unit_cost, 2) }}</td>
                                <td>{{ $it->quantity }}</td>
                                <td>{{ number_format($it->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Payments</h6>
                <table class="table table-sm " id="payments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->payments as $p)
                            <tr>
                                <td>{{ optional($p->payment_date)->format('d-m-Y') }}</td>
                                <td>{{ $p->method }}</td>
                                <td>{{ number_format($p->amount, 2) }}</td>
                                <td>{{ $p->reference }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-sm btn-warning d-flex gap-1 AjaxModal" id="btn-add-payment"
                    data-onsuccess="purchasePayment.onSaved" data-order-id="{{ $order->id }}"
                    data-ajax-modal="{{ route('purchase.orders.payment.modal', $order->id) }}"><iconify-icon icon="material-symbols:currency-exchange-rounded" class="text-lg"></iconify-icon> Add
                    Payment</button>

                <div >
                    @if ($order->status === 'draft')
                        <button id="btn-receive-all " class="btn btn-sm btn-success d-flex gap-1 AjaxModal" data-ajax-modal="{{ route('purchase.orders.receive-all.modal', $order->id) }}" data-onsuccess="purchaseReceipt.onReceived"><iconify-icon icon="material-symbols:inventory" class="text-lg"></iconify-icon> Receive All</button>
                    @else
                        <span class="badge bg-success">Received</span>
                    @endif
                </div>
            </div>
            </div>

            <div class="col-md-6 text-end">
                <h6>Summary</h6>
                <p>Subtotal: {{ number_format($order->subtotal, 2) }}</p>
                <p>Shipping: {{ number_format($order->shipping_amount, 2) }}</p>
                <p>Discount: {{ number_format($order->subtotal + $order->shipping_amount - $order->total_amount, 2) }}</p>
                <p class="fw-bold">Total: {{ number_format($order->total_amount, 2) }}</p>
                <p class="fw-bold">Paid: <span id="paid-amount">{{ number_format($paid, 2) }}</span></p>
                <p class="fw-bold">Outstanding: <span id="outstanding-amount">{{ number_format($outstanding, 2) }}</span>
                </p>


            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        @media print {
            .navbar-header, .sidebar, .d-footer, .btn, .no-print, .sidebar-mobile-toggle, .sidebar-toggle {
                display: none !important;
            }
            .dashboard-main {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .dashboard-main-body {
                padding: 0 !important;
                margin: 0 !important;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            body {
                background-color: white !important;
            }
            .card {
                border: none !important;
            }
            /* Force side-by-side layout for portrait printing */
            .row > .col-md-6 {
                width: 50% !important;
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            .text-end {
                text-align: right !important;
            }
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#print-order').on('click', function(e) {
                e.preventDefault();
                window.print();
            });
        });

        window.purchasePayIndex = {
            onSaved: function() {
                $('#payments-table tbody').empty().append(
                    @foreach ($order->payments as $p)
                        `<tr>
                        <td>{{ optional($p->payment_date)->format('d-m-Y') }}</td>
                        <td>{{ $p->method }}</td>
                        <td>{{ number_format($p->amount, 2) }}</td>
                        <td>{{ $p->reference }}</td>
                    </tr>`,
                    @endforeach
                );



            }

        };

        window.purchaseReceipt = {
            onReceived: function(orderId) {
           
                //   window.location.href = "{{ route('purchase.index') }}"; 
                window.location.reload();
            }   
        };
    </script>
@endsection
