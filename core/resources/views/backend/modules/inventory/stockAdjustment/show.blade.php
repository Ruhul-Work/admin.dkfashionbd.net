@extends('backend.layouts.master')
@section('meta')
    <title>Adjustment #{{ $adjustment->reference_no ?? $adjustment->id }}</title>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6>Adjustment: {{ $adjustment->reference_no ?? '#' . $adjustment->id }}</h6>
            <small class="text-muted">Date:
                {{ optional($adjustment->adjust_date)->format('Y-m-d H:i') ?? optional($adjustment->created_at)->format('Y-m-d H:i') }}</small>
        </div>
        {{-- <div>
            <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-sm btn-outline-neutral-900 minw-120">Back</a>
            @if ($adjustment->status === 'DRAFT')
                <a href="{{ route('inventory.adjustments.edit', $adjustment->id) }}"
                    class="btn btn-sm btn-outline-primary minw-120">Edit</a>
                <button id="btnPost" data-url="{{ route('inventory.adjustments.post', $adjustment->id) }}"
                    class="btn btn-sm btn-outline-success minw-120">Post</button>
                <button id="btnDelete" data-url="{{ route('inventory.adjustments.destroy', $adjustment->id) }}"
                    class="btn btn-sm btn-outline-danger minw-120">Delete</button>
            @elseif($adjustment->status === 'POSTED')
                <button id="btnCancel" data-url="{{ route('inventory.adjustments.cancel', $adjustment->id) }}"
                    class="btn btn-sm btn-warning">Cancel</button>
            @endif
        </div> --}}

        <div class="d-flex gap-2">
            {{-- Back button --}}
            <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-sm btn-outline-neutral-900"
                title="Back to List">
                <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            </a>

            @if ($adjustment->status === 'DRAFT')
                {{-- Edit button --}}
                <a href="{{ route('inventory.adjustments.edit', $adjustment->id) }}" class="btn btn-sm btn-outline-primary"
                    title="Edit Transfer">
                    <iconify-icon icon="solar:pen-outline" class="text-lg"></iconify-icon>
                </a>

                {{-- Post button --}}
                <button id="btnPost" data-url="{{ route('inventory.adjustments.post', $adjustment->id) }}"
                    class="btn btn-sm btn-outline-success" title="Approve & Post">
                    <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                </button>

                {{-- Delete button --}}
            @elseif($adjustment->status === 'POSTED')
                <button id="btnCancel" data-url="{{ route('inventory.adjustments.cancel', $adjustment->id) }}"
                    class="btn btn-sm btn-outline-danger" title="cancel"><iconify-icon icon="mdi:refresh" class="text-lg"></button>
            @endif

        </div>
    </div>

    <div class="card mb-12">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><strong>Warehouse:</strong> {{ optional($adjustment->warehouse)->name ?? '—' }}</div>
                <div class="col-md-3"><strong>Branch:</strong> {{ optional($adjustment->branch)->name ?? '—' }}</div>
                <div class="col-md-3"><strong>Status:</strong> <span
                        class="border px-24 py-4 radius-4 fw-medium text-sm {{ $adjustment->status === 'POSTED' ? 'fw-semibold text-success-600 bg-success-100' : ($adjustment->status === 'CANCELLED' ? 'fw-semibold text-danger-600 bg-danger-100' : 'fw-semibold text-warning-600 bg-warning-100') }}">{{ $adjustment->status }}</span>
                </div>
                <div class="col-md-3"><strong>By:</strong>
                    {{ optional($adjustment->creator)->name ?? $adjustment->created_by }}</div>
            </div>

            <h6 class="mt-3">Items</h6>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered table-scrollable mb-0">
                    <thead>
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
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ image(optional($it->product)->image) }}"
                                            style="width:36px;height:36px;object-fit:cover;border-radius:6px">
                                        <div>
                                            <div class="fw-semibold">{{ stringShortner(optional($it->product)->name ?? '—', 35) }}</div>
                                            <small class="text-muted">{{ optional($it->product)->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($it->quantity, 3) }}</td>
                                <td class="text-end">{{ $it->unit_cost ? number_format($it->unit_cost, 2) : '—' }}</td>
                                <td>{{ $it->direction }}</td>
                                <td>{{ $it->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($adjustment->status === 'POSTED')
                <h6 class="mt-3">Ledger Entries</h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-scrollable mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Direction</th>
                                <th class="text-end">Qty</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>


                            @php
                                $ledgers = $adjustment->ledgerEntries()->get(); // eager loaded if you prefer loadMissing earlier
                            @endphp

                            @if ($ledgers->isEmpty())
                                <p class="text-muted">No ledger entries for this adjustment.</p>
                            @else
                                @foreach ($ledgers as $lg)
                                    <tr>
                                        <td>{{ optional($lg->txn_date)->format('Y-m-d H:i') ?? optional($lg->created_at)->format('Y-m-d H:i') }}
                                        </td>
                                        <td>{{ stringShortner(optional($lg->product)->name ?? '—', 35) }}</td>
                                        <td>{{ $lg->direction ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($lg->quantity ?? 0, 3) }}</td>
                                        <td>{{ $lg->note ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            $('#btnPost').on('click', function() {
                if (!confirm('Post this adjustment? This will update stock.')) return;
                const url = $(this).data('url');
                $.post(url, {
                    _token: "{{ csrf_token() }}"
                }).done(res => {
                    Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Posted',
                            timer: 800,
                            showConfirmButton: false
                        })
                        .then(() => location.reload());
                }).fail(err => {
                    Swal.fire({
                        icon: 'error',
                        title: err.responseJSON?.msg || 'Post failed'
                    });
                });
            });

            $('#btnCancel').on('click', function() {
                if (!confirm('Cancel posted adjustment?')) return;
                const url = $(this).data('url');
                $.post(url, {
                    _token: "{{ csrf_token() }}"
                }).done(res => {
                    Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Cancelled',
                            timer: 800,
                            showConfirmButton: false
                        })
                        .then(() => location.reload());
                }).fail(err => {
                    Swal.fire({
                        icon: 'error',
                        title: err.responseJSON?.msg || 'Cancel failed'
                    });
                });
            });

            $('#btnDelete').on('click', function() {
                if (!confirm('Delete draft adjustment?')) return;
                const url = $(this).data('url');
                $.ajax({
                    url,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                }).done(res => {
                    Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Deleted',
                            timer: 700,
                            showConfirmButton: false
                        })
                        .then(() => window.location =
                            "{{ route('inventory.adjustments.index') }}");
                }).fail(err => Swal.fire({
                    icon: 'error',
                    title: err.responseJSON?.msg || 'Delete failed'
                }));
            });
        });
    </script>
@endsection
