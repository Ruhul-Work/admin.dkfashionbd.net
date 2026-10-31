@extends('backend.layouts.invoice')
@section('content')
    <style>
        .expense-invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        .expense-invoice-header__left,
        .expense-invoice-header__right {
            flex: 1 1 0;
        }

        .expense-invoice-header__right {
            text-align: right;
        }
    </style>

  
<div class="invoice-wrapper">

        {{-- ================= HEADER ================= --}}
        <div class="expense-invoice-header mb-4">
            <div class="expense-invoice-header__left">
                <h4 class="mb-1 lh-1">Expense Invoice</h4>
                <small class="text-muted d-block mt-1">
                    {{ $expense->status === 'posted' ? 'Posted Expense' : 'Draft Expense' }}
                </small>
            </div>

            <div class="expense-invoice-header__right text-end">
                <h6 class="mb-1 lh-1">{{ $expense->invoice_no ?? 'DRAFT' }}</h6>
                <small class="d-block mt-1">Date: {{ optional($expense->expense_date)->format('d M Y') }}</small>
            </div>
        </div>

        {{-- ================= BASIC INFO ================= --}}
        <table class="table table-sm table-borderless mb-4">
            <tr>
                <td width="20%"><strong>Expense Name</strong></td>
                <td>{{ $expense->name }}</td>
            </tr>
            <tr>
                <td><strong>Reference</strong></td>
                <td>{{ $expense->reference ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Description</strong></td>
                <td>{{ $expense->description ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>
                    <span class="badge {{ $expense->status === 'posted' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ ucfirst($expense->status) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- ================= ITEMS ================= --}}
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expense->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->category?->name ?? '—' }}</td>
                        <td>{{ $item->description ?? '—' }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($expense->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        {{-- ================= PAYMENT ================= --}}
        <div class="mt-3">
            <strong>Payment Method:</strong>
            {{ $expense->payment?->paymentType?->name ?? 'Not Selected' }}
        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="mt-5 d-flex justify-content-between text-muted small">
            <span>Prepared By: {{ optional($expense->creator)->name ?? 'System' }}</span>
            <span>Printed At: {{ now()->format('d M Y H:i') }}</span>
        </div>

    </div>

@endsection
