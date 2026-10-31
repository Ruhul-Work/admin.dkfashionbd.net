@extends('backend.layouts.master')

@section('meta')
    <title>Create Stock Transfer</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">New Stock Transfer</h6>
            <p class="text-muted m-0">Move stock between warehouses</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium"><a href="{{ route('backend.dashboard') }}"
                    class="d-flex align-items-center gap-1 hover-text-primary"><iconify-icon
                        icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a></li>
            <li>-</li>
            <li class="fw-medium"><a href="{{ route('inventory.transfers.index') }}">Transfers</a></li>
            <li>-</li>
            <li class="fw-medium">Create</li>
        </ul>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="transferForm" action="{{ route('inventory.transfers.store') }}" method="post">
        @csrf

        <div class="row g-16 mb-16">
            <div class="col-md-3">
                <label class="form-label">From Warehouse <span class="text-danger">*</span></label>
                <select name="from_warehouse_id" id="fromWarehouse" class="form-control js-s2-ajax"
                    data-url="{{ route('inventory.warehouses.select2') }}" required>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">To Warehouse <span class="text-danger">*</span></label>
                <select name="to_warehouse_id" id="toWarehouse" class="form-control js-s2-ajax"
                    data-url="{{ route('inventory.warehouses.select2', ['ignore_branch' => 1]) }}" required>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Date & Time</label>
                <input type="datetime-local" name="transfer_date" class="form-control"
                    value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Reference</label>
                <input type="text" name="reference_no" class="form-control" placeholder="(optional)">
            </div>
        </div>

        <div class="row mb-12">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Lines</h6>
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
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted">Add rows for each product to transfer. Qty must be > 0.</small>
                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ route('inventory.transfers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Transfer</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- row template --}}
    <template id="rowTpl">
        <tr data-idx="__IDX__">
            <td>
                <select name="rows[__IDX__][product_id]" class="form-control  js-s2-ajax"
                    data-url="{{ route('product.select2') }}" data-placeholder="Search product by name/sku"
                    required></select>
            </td>
            <td><input type="number" min="0.001" step="0.001" class="form-control text-end"
                    name="rows[__IDX__][quantity]" required></td>
            <td><input type="number" step="0.01" class="form-control text-end" name="rows[__IDX__][unit_cost]"></td>
            <td><input type="text" class="form-control" name="rows[__IDX__][note]"></td>
            <td class="text-center"><button type="button"
                    class="btn btn-sm btn-outline-danger btn-remove-row"><iconify-icon
                        icon="solar:trash-bin-trash-outline"></iconify-icon></button></td>
        </tr>
    </template>

@endsection

@section('script')
    <script>
        (function() {
            const $form = $('#transferForm');
            const $tbody = $('#transferLines tbody');
            let idx = 0;

            // Add initial empty row
            function addRow(data = {}) {
                const html = $('#rowTpl').html().replaceAll('__IDX__', idx);
                const $tr = $(html);
                $tbody.append($tr);

                // find the select inside this new row
                const $select = $tr.find('select.js-s2-ajax');

                // If you have global S2.auto helper that auto-inits .js-s2-ajax selects, use it scoped to the new row:
                if (window.S2 && typeof window.S2.auto === 'function') {
                    // S2.auto expects a jQuery context; pass the row so only its selects are initialized
                    window.S2.auto($tr);
                }

                // If provided data, set values after select initialized
                if (data.product_id) {
                    // create an option and select it (works even if ajax)
                    const label = data.text || data.name || data.sku || data.product_id;
                    const option = new Option(label, data.product_id, true, true);
                    $select.append(option).trigger('change');
                }

                if (data.quantity) $tr.find('input[name$="[quantity]"]').val(data.quantity);
                if (data.unit_cost) $tr.find('input[name$="[unit_cost]"]').val(data.unit_cost);
                if (data.note) $tr.find('input[name$="[note]"]').val(data.note);

                idx++;
            }
            addRow();

            // add row button
            $('#btnAddRow').on('click', function() {
                addRow();
            });

            // remove row
            $tbody.on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
                // renumber names (optional)
                renumber();
            });

            function renumber() {
                $('#transferLines tbody tr').each(function(i) {
                    $(this).attr('data-idx', i);
                    $(this).find('[name]').each(function() {
                        const name = $(this).attr('name');
                        const newName = name.replace(/rows\[\d+\]/, 'rows[' + i + ']');
                        $(this).attr('name', newName);
                    });
                });
                idx = $('#transferLines tbody tr').length;
            }

            $('#btnClearRows').on('click', function() {
                $tbody.empty();
                addRow();
            });

            // client-side simple validation: ensure at least one row & product selected
            $form.on('submit', function(e) {
                const fromWh = $('#fromWarehouse').val();
                const toWh = $('#toWarehouse').val();

                if (fromWh && toWh && fromWh === toWh) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'The From Warehouse and To Warehouse must be different.'
                    });
                    return false;
                }

                // basic checks
                if ($('#transferLines tbody tr').length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'Add at least one product row'
                    });
                    return false;
                }

                let ok = true;
                $('#transferLines tbody tr').each(function() {
                    const $sel = $(this).find('select[js-s2-product], select[name*="[product_id]"]');
                    const pid = $(this).find('select[name*="[product_id]"]').val();
                    const qty = $(this).find('input[name$="[quantity]"]').val();
                    if (!pid || pid === '' || !qty || parseFloat(qty) <= 0) {
                        ok = false;
                        return false;
                    }
                });
                if (!ok) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Please fill all product rows with valid qty'
                    });
                    return false;
                }

                // allow submit — server will validate again
                if (
                    Swal.fire({
                        title: 'Confirm Save?',
                        text: 'Are you sure you want to save this stock transfer?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            swal.fire({
                                title: 'Saving...',
                                didOpen: () => {
                                    swal.showLoading();
                                }
                            });
                            $form.off('submit').submit();
                        } else {
                            $form.off('submit'); // prevent multiple binds
                        }
                    })
                ) return false;
            });

            // init global select2 for warehouse selects if using js-s2-ajax
            window.S2 && S2.auto($(document));

        })();
    </script>
@endsection
