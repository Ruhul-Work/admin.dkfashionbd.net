{{-- ================= MODAL HEADER ================= --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Transfer: {{ $transfer->reference_no ?? '#' . $transfer->id }}
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- ================= MODAL BODY ================= --}}
<div class="modal-body">

    {{-- Basic Info --}}
    <div class="row mb-3 text-sm">
        <div class="col-md-6">
            <strong>From Warehouse:</strong>
            {{ optional($transfer->fromWarehouse)->name ?? '—' }} <br>
            <strong>From Branch:</strong>
            {{ optional(optional($transfer->fromWarehouse)->branch)->name ?? '—' }}
        </div>

        <div class="col-md-6 text-end">
            <strong>To Warehouse:</strong>
            {{ optional($transfer->toWarehouse)->name ?? '—' }} <br>
            <strong>To Branch:</strong>
            {{ optional(optional($transfer->toWarehouse)->branch)->name ?? '—' }}
        </div>
    </div>

    <div class="row mb-3 text-sm">
        <div class="col-md-6">
            <strong>Status:</strong>
            <span class="badge
                {{ $transfer->status === 'POSTED'
                    ? 'bg-success'
                    : ($transfer->status === 'CANCELLED'
                        ? 'bg-danger'
                        : 'bg-warning') }}">
                {{ $transfer->status }}
            </span>
        </div>

        <div class="col-md-6 text-end">
            <strong>Date:</strong>
            {{ optional($transfer->transfer_date)->format('Y-m-d H:i')
                ?? optional($transfer->created_at)->format('Y-m-d H:i') }}
        </div>
    </div>

    {{-- ================= ITEMS ================= --}}
    <h6 class="text-sm fw-semibold mb-2">Items</h6>

    <div class="table-responsive mb-3" style="max-height:300px">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Cost</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transfer->items as $it)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ optional($it->product)->name ?? '—' }}
                            </div>
                            <small class="text-muted">
                                {{ optional($it->product)->sku ?? '' }}
                            </small>
                        </td>
                        <td class="text-end">{{ number_format($it->quantity, 3) }}</td>
                        <td class="text-end">
                            {{ $it->unit_cost ? number_format($it->unit_cost, 2) : '—' }}
                        </td>
                        <td>{{ $it->note ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No items found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= LEDGER ENTRIES ================= --}}
    @if ($transfer->status === 'POSTED')
        <h6 class="text-sm fw-semibold mb-2">Ledger Entries</h6>

        <div class="table-responsive" style="max-height:300px">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Direction</th>
                        <th class="text-end">Qty</th>
                        <th>Branch</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ledgers as $lg)
                        <tr>
                            <td>
                                {{ optional($lg->txn_date)->format('Y-m-d H:i')
                                    ?? optional($lg->created_at)->format('Y-m-d H:i') }}
                            </td>
                            <td>{{ optional($lg->product)->name ?? '—' }}</td>
                            <td>{{ $lg->direction ?? '—' }}</td>
                            <td class="text-end">{{ number_format($lg->quantity ?? 0, 3) }}</td>
                            <td>{{ optional($lg->branch)->name ?? '—' }}</td>
                            <td>{{ $lg->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No ledger entries
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>

{{-- ================= MODAL FOOTER ================= --}}
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
        Close
    </button>
</div>
