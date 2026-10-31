@extends('backend.layouts.master')

@section('meta')
    <title>Stock Transfer #{{ $transfer->id ?? '—' }}</title>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-semibold mb-0">Transfer: {{ $transfer->reference_no ?? '#' . $transfer->id }}</h6>
            <small class="text-muted">Date:
                {{ optional($transfer->transfer_date)->format('Y-m-d H:i') ?? optional($transfer->created_at)->format('Y-m-d H:i') }}</small>
        </div>

        <div class="d-flex gap-2">
            {{-- Back button --}}
            <a href="{{ route('inventory.transfers.index') }}" class="btn btn-sm btn-outline-neutral-900" title="Back to List">
                <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            </a>

            @if ($transfer->status === 'DRAFT')
                {{-- Edit button --}}
                <a href="{{ route('inventory.transfers.edit', $transfer->id) }}" class="btn btn-sm btn-outline-primary"
                    title="Edit Transfer">
                    <iconify-icon icon="solar:pen-outline" class="text-lg"></iconify-icon>
                </a>

                {{-- Post button --}}
                <button id="btnPost" data-url="{{ route('inventory.transfers.post', $transfer->id) }}"
                    class="btn btn-sm btn-outline-success" title="Approve & Post">
                    <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                </button>

                {{-- Delete button --}}
                <form id="frmDelete" action="{{ route('inventory.transfers.destroy', $transfer->id) }}" method="post"
                    class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Transfer">
                        <iconify-icon icon="solar:trash-bin-trash-outline" class="text-lg"></iconify-icon>
                    </button>
                </form>
            @elseif ($transfer->status === 'POSTED')
                <div class="d-flex align-items-center gap-1">
                    {{-- <span class="badge bg-success">Posted</span> --}}
                    <small class="text-muted ms-2">Posted at:
                        {{ optional($transfer->created_at)->format('Y-m-d H:i') }}</small>
                </div>
            @endif
        </div>

    </div>

    <div class="card mb-12">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>From Warehouse:</strong>
                    <div>{{ optional($transfer->fromWarehouse)->name ?? '—' }}</div>
                    <small class="text-muted">Branch:
                        {{ optional($transfer->fromBranch ?? optional($transfer->fromWarehouse)->branch)->name ?? '—' }}</small>
                </div>
                <div class="col-md-4">
                    <strong>To Warehouse:</strong>
                    <div>{{ optional($transfer->toWarehouse)->name ?? '—' }}</div>
                    <small class="text-muted">Branch:
                        {{ optional($transfer->toBranch ?? optional($transfer->toWarehouse)->branch)->name ?? '—' }}</small>
                </div>
                <div class="col-md-4 ">

                    <div>
                        <strong>Status:</strong>
                        <span
                            class="border px-24 py-4 radius-4 fw-medium text-sm
                        {{ $transfer->status === 'POSTED' ? 'fw-semibold text-success-600 bg-success-100' : ($transfer->status === 'CANCELLED' ? 'fw-semibold text-danger-600 bg-danger-100' : 'fw-semibold text-warning-600 bg-warning-100') }}">
                            {{ $transfer->status }}
                        </span>
                    </div>
                    <div class="mt-2"><strong>By:</strong>
                        {{ optional($transfer->creator)->name ?? optional($transfer->created_by) }}</div>
                </div>
            </div>

            <h6 class="mt-3">Items</h6>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered table-sm table-scrollable">
                    <thead>
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
                                    <div class="d-flex gap-2 align-items-center">
                                        @if (optional($it->product)->image)
                                            <img src="{{ image(optional($it->product)->image) }}"
                                                style="width:36px;height:36px;object-fit:cover;border-radius:6px">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ optional($it->product)->name ?? '—' }}</div>
                                            <small class="text-muted">{{ optional($it->product)->sku ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($it->quantity, 3) }}</td>
                                <td class="text-end">{{ $it->unit_cost ? number_format($it->unit_cost, 2) : '—' }}</td>
                                <td>{{ $it->note ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No items</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transfer->status === 'POSTED')
                <h6 class="mt-4">Ledger Entries</h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-scrollable">
                        <thead>
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
                            @forelse($ledgers as $lg)
                                <tr>
                                    <td>{{ optional($lg->txn_date)->format('Y-m-d H:i') ?? optional($lg->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td>{{ optional($lg->product)->name ?? '—' }}</td>
                                    <td>{{ $lg->direction ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($lg->quantity ?? 0, 3) }}</td>
                                    <td>{{ optional($lg->branch)->name ?? '—' }}</td>
                                    <td>{{ $lg->note ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No ledger entries</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

@endsection

@section('script')
    <script>
        (function() {
            const CSRF = document.querySelector('meta[name="csrf-token"]');

            $(document).on('click', '#btnPost', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (!url) return;

                Swal.fire({
                    title: 'Post transfer?',
                    text: 'This will create ledger entries and update stock.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Post',
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    $.post(url, {
                            _token: CSRF
                        })
                        .done(function(res) {
                            if (res.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: res.msg || 'Posted'
                                });
                                setTimeout(() => location.reload(), 700);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.msg || 'Failed'
                                });
                            }
                        })
                        .fail(function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText
                            });
                        });
                });
            });

            $(document).on('submit', '#frmDelete', function(e) {
                e.preventDefault();
                const $form = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This transfer will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: $form.attr('action'),
                        type: 'POST',
                        data: $form.serialize(),
                        success: function(res) {
                            if (res.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.msg
                                });
                                setTimeout(() => window.location.href =
                                    '{{ route('inventory.transfers.index') }}', 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: res.msg
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText
                            });
                        }
                    });
                });
            });


        })();
    </script>
@endsection
