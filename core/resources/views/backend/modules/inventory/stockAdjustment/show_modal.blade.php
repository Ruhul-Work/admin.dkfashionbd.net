{{-- Modal Header --}}
<div class="modal-header">
    <h6 class="modal-title fw-semibold">
        Adjustment: {{ $adjustment->reference_no ?? '#' . $adjustment->id }}
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- Modal Body --}}
<div class="modal-body">

    {{-- Basic Info --}}
    <div class="row mb-3 text-sm">
        <div class="col-md-6">
            <strong>Warehouse:</strong> {{ optional($adjustment->warehouse)->name ?? '—' }} <br>
            <strong>Branch:</strong> {{ optional($adjustment->branch)->name ?? '—' }} <br>
            <strong>By:</strong> {{ optional($adjustment->creator)->name ?? $adjustment->created_by }}
        </div>

        <div class="col-md-6 text-end">
            <span
                class="badge
                {{ $adjustment->status === 'POSTED'
                    ? 'bg-success'
                    : ($adjustment->status === 'CANCELLED'
                        ? 'bg-danger'
                        : 'bg-warning') }}">
                {{ $adjustment->status }}
            </span>
            <br>
            <strong>Date:</strong>
            {{ optional($adjustment->adjust_date)->format('Y-m-d H:i')
                ?? optional($adjustment->created_at)->format('Y-m-d H:i') }}
        </div>
    </div>

    {{-- Items --}}
    <h6 class="text-sm fw-semibold mb-2">Items</h6>
    <div class="table-responsive mb-3" style="max-height:300px">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Cost</th>
                    <th>Direction</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adjustment->items as $it)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ stringShortner(optional($it->product)->name ?? '—', 35) }}
                            </div>
                            <small class="text-muted">
                                {{ optional($it->product)->sku }}
                            </small>
                        </td>
                        <td class="text-end">{{ number_format($it->quantity, 3) }}</td>
                        <td class="text-end">
                            {{ $it->unit_cost ? number_format($it->unit_cost, 2) : '—' }}
                        </td>
                        <td>{{ $it->direction }}</td>
                        <td>{{ $it->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Ledger Entries (only if POSTED) --}}
    @if ($adjustment->status === 'POSTED')
        <h6 class="text-sm fw-semibold mb-2">Ledger Entries</h6>

        <div class="table-responsive" style="max-height:300px">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Direction</th>
                        <th class="text-end">Qty</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustment->ledgerEntries as $lg)
                        <tr>
                            <td>
                                {{ optional($lg->txn_date)->format('Y-m-d H:i')
                                    ?? optional($lg->created_at)->format('Y-m-d H:i') }}
                            </td>
                            <td>{{ stringShortner(optional($lg->product)->name ?? '—', 35) }}</td>
                            <td>{{ $lg->direction }}</td>
                            <td class="text-end">{{ number_format($lg->quantity, 3) }}</td>
                            <td>{{ $lg->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No ledger entries found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>

{{-- Modal Footer --}}
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
        Close
    </button>

    @if ($adjustment->status === 'DRAFT')
        <button
            class="btn btn-success btn-sm AjaxModal"
            data-size="sm"
            data-ajax-modal="{{ route('inventory.adjustments.post', $adjustment->id) }}">
            Post Adjustment
        </button>
    @endif
</div>
