@extends('backend.layouts.master')

@section('meta')
    <title>Sale Return</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-24">

        {{-- Left: Title --}}
        <div>
            <h6 class="fw-semibold mb-1">Sale Return</h6>
            <p class="text-sm text-muted mb-0">
                Return sold items and manage refunds
            </p>
        </div>

        {{-- Right: Breadcrumb + Back --}}
        <div class="d-flex align-items-center gap-3">

            <ul class="d-flex align-items-center gap-2 mb-0 list-unstyled">
                <li>
                    <a href="{{ route('backend.dashboard') }}"
                        class="hover-text-primary d-flex align-items-center gap-1 text-muted">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li class="text-muted">-</li>
                <li class="fw-medium">Sale Return</li>
            </ul>

            <a href="{{ route('pos.sales.list') }}" class="btn btn-sm btn-outline-secondary">
                ← Back
            </a>

        </div>
    </div>
    @if (auth()->user()->isSuper() && !current_branch_id())
        <div class="alert alert-warning">
            <strong>Note:</strong> Please select a branch  before processing a sale return.
        </div>
    @endif
    {{-- ======= Sale Return Form ======= --}}
    <form method="POST" action="{{ route('pos.sales.return.store', $sale->id) }}">
        @csrf

        {{-- 🔹 Sale Info --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row text-sm">
                    <div class="col-md-4">
                        <strong>Customer:</strong>
                        {{ $sale->customer?->name ?? 'Walk In Customer' }}<br />
                        <strong>Invoice:</strong> <strong>{{ $sale->invoice_no }}</strong><br />
                        <strong>Date:</strong> {{ $sale->created_at->format('d M Y') }}
                    </div>
                    <div class="col-md-4">
                        <strong>Total Sale:</strong>
                        {{ number_format($sale->total, 2) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Already Paid:</strong>
                        {{ number_format($sale->paid_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Return Items --}}
        <div class="card mb-3">
            <div class="card-header">
                <strong>Return Items</strong>
            </div>

            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="returnItemsTable" class="table table-sm table-bordered mb-0">
                        <thead class="table-success text-sm">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Sold Qty</th>
                                <th class="text-center">Returnable Qty</th>
                                <th class="text-center">Return Qty</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Discount</th>
                                <th class="text-end">Refund</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($saleItems as $item)
                                @php
                                    $alreadyReturned = $returnedQtyMap[$item->id] ?? 0;
                                    $returnableQty = $item->quantity - $alreadyReturned;
                                @endphp

                                <tr>
                                    <td>
                                        {{ $item->product->name }}
                                        <input type="hidden" name="items[{{ $item->id }}][sale_item_id]"
                                            value="{{ $item->id }}">
                                    </td>

                                    <td class="text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="text-center text-muted">
                                        {{ $returnableQty }}
                                    </td>

                                    <td class="text-center ">
                                        <input type="number" class="form-control form-control-sm return-qty"
                                            name="items[{{ $item->id }}][qty]"
                                            data-max="{{ $item->quantity - ($returnedQtyMap[$item->id] ?? 0) }}"
                                            min="0" step="1">
                                    </td>

                                    <td class="text-center">
                                        <input type="number" name="items[{{ $item->id }}][unit_price]"
                                            class="form-control form-control-sm unit-price" step="0.01"
                                            value="{{ $item->unit_price }}" style="">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm return-discount"
                                            name="items[{{ $item->id }}][discount_amount]" value="0"
                                            step="0.01">
                                    </td>

                                    <td class="text-end refund-cell">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 🔹 Refund Summary --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-muted">
                        <strong>Note:</strong> Returned quantity cannot exceed sold quantity.
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="mb-0">
                            Total Refund:
                            <span class="text-danger" id="totalRefund">0.00</span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Refund Payment --}}
        {{-- <div class="card mb-3">
            <div class="card-header">
                <strong>Refund Payment (Optional)</strong>
            </div>

            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="payment_type_id" class="form-control form-control-sm">
                            <option value="">-- Select Method --</option>
                            @foreach ($paymentTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="number" name="refund_paid" id="refund_paid" step="0.01"
                            class="form-control form-control-sm" placeholder="Refund Amount">
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- 🔹 Action --}}
        <div class="text-end">
            <button type="submit" class="btn btn-danger">
                Confirm Return
            </button>
        </div>

    </form>
@endsection

@section('script')
    <script>
        function recalcRefundRow(row) {
            let qty = parseFloat(row.find('.return-qty').val()) || 0;
            let price = parseFloat(row.find('.unit-price').val()) || 0;
            let discount = parseFloat(row.find('.return-discount').val()) || 0;

            let subtotal = qty * price;

            if (discount > subtotal) {
                discount = subtotal;
                row.find('.return-discount').val(discount.toFixed(2));
            }

            let refund = subtotal - discount;
            if (refund < 0) refund = 0;

            row.find('.refund-cell').text(refund.toFixed(2));

            return refund;
        }

        function recalcAllRefunds() {
            let total = 0;

            $('#returnItemsTable tbody tr').each(function() {
                total += recalcRefundRow($(this));
            });

            $('#totalRefund').text(total.toFixed(2));

            let paidInput = $('#refund_paid');
            if (paidInput.length) {
                paidInput.attr('max', total);
                if (!paidInput.val()) {
                    paidInput.val(total.toFixed(2));
                }
            }
        }

        $(document).on('input', '.return-qty, .unit-price, .return-discount', function() {
            recalcAllRefunds();
        });




        document.addEventListener('input', function(e) {

            if (!e.target.classList.contains('return-qty')) return;

            let max = parseFloat(e.target.dataset.max || 0);
            let val = parseFloat(e.target.value || 0);

            if (val > max) {
                e.target.value = max;

                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Quantity',
                    text: `You can return maximum ${max} item(s).`,
                    timer: 1800,
                    showConfirmButton: false
                });
            }

            if (val < 0 || isNaN(val)) {
                e.target.value = 0;
            }
        });
    </script>
@endsection
