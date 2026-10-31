<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>POS Invoice</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }

        body {
            font-family: monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            /* max-width: 80mm; */
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print()">

    <!-- Header -->
    <div class="center">
        <strong>{{ optional($companySetting)->name ?? config('app.name') }}</strong><br>
        {{ $sale->branch?->name }}<br>
        Phone: {{ optional($companySetting)->phone ?? $sale->branch?->phone ?? '-' }}<br>
    </div>

    <hr>

    Invoice: {{ $sale->invoice_no }}<br>
    Date: {{ $sale->created_at->format('d-m-Y H:i') }}<br>
    Customer: {{ $sale->customer?->name ?? 'Walk In' }}

    <hr>

    <!-- Items -->
    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2">
                    {{ $item->product->name }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}
                </td>
                <td class="right">
                    {{ number_format($item->line_total, 2) }}
                </td>
            </tr>
        @endforeach
    </table>

    <hr>
    <!-- Summary -->

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>

        @if ($sale->shipping_charge > 0)
            <tr>
                <td>Shipping</td>
                <td class="right">{{ number_format($sale->shipping_charge, 2) }}</td>
            </tr>
        @endif

        @if ($sale->discount > 0)
            <tr>
                <td>Discount</td>
                <td class="right">-{{ number_format($sale->discount, 2) }}</td>
            </tr>
        @endif

        @if ($sale->coupon_discount > 0)
            <tr>
                <td>Coupon Discount</td>
                <td class="right">-{{ number_format($sale->coupon_discount, 2) }}</td>
            </tr>
        @endif

        @if ($sale->point_discount > 0)
            <tr>
                <td>Points Discount</td>
                <td class="right">-{{ number_format($sale->point_discount, 2) }}</td>
            </tr>
        @endif

        <tr class="total">
            <td>Total</td>
            <td class="right">{{ number_format($sale->total, 2) }}</td>
        </tr>
    </table>
    <hr>
    <!-- Payments -->
    @foreach ($sale->payments as $pay)
        {{ strtoupper($pay->payment_type) }} :
        {{ number_format($pay->amount, 2) }}<br>
    @endforeach

    Paid: {{ number_format($sale->paid_amount, 2) }}<br>
    Due: {{ number_format($sale->due_amount, 2) }}
    <hr>

    <!-- Footer -->
    <div class="center">
        Thank you for shopping!<br>
        Powered by Bintel POS System
    </div>

</body>

</html>
