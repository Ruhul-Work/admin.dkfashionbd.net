@extends('backend.layouts.master')

@section('meta')
    <title>Edit Stock Transfer</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Edit Transfer #{{ $transfer->id }}</h6>
            <p class="text-muted m-0">Modify transfer details and items</p>
        </div>
        <a href="{{ route('inventory.transfers.index') }}" class="btn btn-sm btn-outline-neutral-900" title="Back to List">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
        </a>
    </div>

    <form id="transferForm" action="{{ route('inventory.transfers.update', $transfer->id) }}" method="post">
        @csrf

        <div class="row g-16 mb-16">
            <div class="col-md-3">
                <label class="form-label">From Warehouse <span class="text-danger">*</span></label>
                <select name="from_warehouse_id" class="form-control js-s2-ajax"
                    data-url="{{ route('inventory.warehouses.select2') }}" required>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $transfer->from_warehouse_id == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">To Warehouse <span class="text-danger">*</span></label>
                <select name="to_warehouse_id" class="form-control js-s2-ajax"
                    data-url="{{ route('inventory.warehouses.select2', ['ignore_branch' => 1]) }}" required>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $transfer->to_warehouse_id == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Date & Time</label>
                <input type="datetime-local" name="transfer_date" class="form-control"
                    value="{{ old('transfer_date', optional($transfer->transfer_date)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">

            </div>

            <div class="col-md-3">
                <label class="form-label">Reference</label>
                <input type="text" name="reference_no" class="form-control" value="{{ $transfer->reference_no }}"
                    placeholder="(optional)">
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Items</h6>
                <div class="d-flex gap-2">
                    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-primary"><iconify-icon
                            icon="mdi:plus" class="text-lg"></iconify-icon></button>
                    <button type="button" id="btnClearRows" class="btn btn-sm btn-outline-danger"><iconify-icon
                            icon="mdi:refresh" class="text-lg"></iconify-icon> </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-scrollable" id="transferLines">
                        <thead>
                            <tr>
                                <th style="width:45%">Product</th>
                                <th class="text-end" style="width:15%">Qty</th>
                                <th class="text-end" style="width:15%">Unit Cost</th>
                                <th style="width:20%">Note</th>
                                <th style="width:5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer->items as $idx => $it)
                                <tr data-idx="{{ $idx }}">
                                    <td>
                                        <select name="rows[{{ $idx }}][product_id]"
                                            class="form-control js-s2-ajax" data-url="{{ route('product.select2') }}"
                                            required>
                                            @if ($it->product)
                                                <option value="{{ $it->product_id }}" selected>
                                                    {{ $it->product->name }} ({{ $it->product->sku }})
                                                </option>
                                            @endif
                                        </select>
                                    </td>
                                    <td><input type="number" name="rows[{{ $idx }}][quantity]"
                                            class="form-control text-end" min="0.001" step="0.001"
                                            value="{{ $it->quantity }}"></td>
                                    <td><input type="number" name="rows[{{ $idx }}][unit_cost]"
                                            class="form-control text-end" step="0.01" value="{{ $it->unit_cost }}"></td>
                                    <td><input type="text" name="rows[{{ $idx }}][note]" class="form-control"
                                            value="{{ $it->note }}"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">Update Transfer</button>
            </div>
        </div>
    </form>

    {{-- Row template --}}
    <template id="rowTpl">
        <tr data-idx="__IDX__">
            <td>
                <select name="rows[__IDX__][product_id]" class="form-control js-s2-ajax"
                    data-url="{{ route('product.select2') }}" required></select>
            </td>
            <td><input type="number" min="0.001" step="0.001" class="form-control text-end"
                    name="rows[__IDX__][quantity]" required></td>
            <td><input type="number" step="0.01" class="form-control text-end" name="rows[__IDX__][unit_cost]"></td>
            <td><input type="text" class="form-control" name="rows[__IDX__][note]"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                </button>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <script>
        (function() {
            const $form = $('#transferForm');
            const $tbody = $('#transferLines tbody');
            let idx = $tbody.find('tr').length;

            // init global select2 for selects present on page
            if (window.S2 && typeof window.S2.auto === 'function') {
                window.S2.auto($('#transferForm'));
            }

            function addRow(data = {}) {
                const html = $('#rowTpl').html().replaceAll('__IDX__', idx);
                const $tr = $(html);
                $tbody.append($tr);
                if (window.S2 && typeof window.S2.auto === 'function') window.S2.auto($tr);
                idx++;
            }

            $('#btnAddRow').on('click', () => addRow());
            $tbody.on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
            });
            $('#btnClearRows').on('click', () => {
                $tbody.empty();
                addRow();
            });

            $form.on('submit', function(e) {
                e.preventDefault(); // stop default submit

                const fromWh = $('select[name="from_warehouse_id"]').val();
                const toWh = $('select[name="to_warehouse_id"]').val();

                if (fromWh && toWh && fromWh === toWh) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'The From Warehouse and To Warehouse must be different.'
                    });
                    return false;
                }

                if ($('#transferLines tbody tr').length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Add at least one product row'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Confirm Update',
                    text: 'Do you want to update this transfer?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // show small loader
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: $form.attr('action'),
                            type: 'POST',
                            data: $form.serialize(),
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: 'Transfer updated successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // redirect or reload
                                    window.location.href =
                                        "{{ route('inventory.transfers.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred.'
                                });
                            }
                        });
                    }
                });
            });



        })();
    </script>
@endsection
