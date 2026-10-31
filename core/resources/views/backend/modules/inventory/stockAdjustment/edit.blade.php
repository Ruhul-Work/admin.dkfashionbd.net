@extends('backend.layouts.master')

@section('meta')
    <title>Edit Stock Adjustment - {{ $adjustment->reference_no ?? '#' . $adjustment->id }}</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Edit Stock Adjustment</h6>
            <p class="text-muted m-0">Adjustment: {{ $adjustment->reference_no ?? '#' . $adjustment->id }}</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium"><a href="{{ route('backend.dashboard') }}"
                    class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a>
            </li>
            <li>-</li>
            <li class="fw-medium"><a href="{{ route('inventory.adjustments.index') }}">Adjustments Edit</a></li>
        </ul>
    </div>

    <form id="adjForm" action="{{ route('inventory.adjustments.update', $adjustment->id) }}" method="post"
        data-ajax-form="true">
        @csrf
        @method('PUT')
        <input type="hidden" name="post_now" id="post_now" value="0">

        {{-- top controls --}}
        <div class="card mb-16">
            <div class="card-body p-14">
                <div class="row g-16">
                    <div class="col-md-3">
                        <label class="form-label text-sm mb-6">Warehouse <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouseSelect" class="form-control js-s2-ajax"
                            data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="Select warehouse"
                            required>
                            @if (isset($warehouses))
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ $adjustment->warehouse_id == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback d-block warehouse_id-error" style="display:none"></div>
                    </div>

                    <div class="col-md-3">
                        @php $isSuper = auth()->check() && optional(auth()->user()->role)->is_super == 1; @endphp
                        <label class="form-label text-sm mb-6">Branch <span class="text-danger">*</span></label>
                        @if ($isSuper)
                            <select name="branch_id" id="branchSelect" class="form-control js-s2-ajax"
                                data-url="{{ route('org.branches.select2') }}" data-placeholder="Select branch">
                                @if (isset($adjustment->branch))
                                    <option value="{{ $adjustment->branch->id }}" selected>{{ $adjustment->branch->name }}
                                    </option>
                                @endif
                            </select>
                        @else
                            <input type="text" class="form-control bg-light"
                                value="{{ $adjustment->branch->name ?? 'Default Branch' }}" readonly disabled>
                            <input type="hidden" name="branch_id" id="branchId"
                                value="{{ auth()->user()->branch_id ?? ($adjustment->branch_id ?? 1) }}">
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-sm mb-6">Date & Time</label>
                        <input type="datetime-local" name="when" class="form-control"
                            value="{{ optional($adjustment->adjust_date ?? $adjustment->created_at)->format('Y-m-d\TH:i') }}">
                        <div class="invalid-feedback d-block when-error" style="display:none"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-sm mb-6">Global Reason</label>
                        <input type="text" name="global_reason" class="form-control" placeholder="(optional)"
                            value="{{ $adjustment->reason_code ?? $adjustment->note }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-16">
            <div class="col-md-12 text-end">
                <div class="btn-group" role="group">
                    <button type="button" id="btnSaveDraft" class="btn btn-sm btn-outline-secondary">Save Draft</button>
                    <button type="button" id="btnSavePost" class="btn btn-sm btn-success">Save & Post</button>
                </div>
                <a href="{{ route('inventory.adjustments.index') }}" class="btn btn-sm btn-outline-danger">Cancel</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Lines</h6>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" step="0.01" id="bulkCost" class="form-control form-control-sm w-110"
                        placeholder="Unit cost">
                    <input type="number" step="0.001" id="bulkQty" class="form-control form-control-sm w-110"
                        placeholder="Qty (+/-)">
                    <button type="button" class="btn btn-sm btn-outline-primary minw-120" id="btnApplyAll">Apply to
                        all</button>
                    <button type="button" class="btn btn-sm btn-outline-danger " id="btnClearLines" title="Clear">
                        <iconify-icon icon="mdi:refresh" class="text-lg"></iconify-icon></button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-scroll-wrap mb-8">
                    <table class="table table-bordered align-middle table-fixed-rows" id="linesTable">
                        <colgroup>
                            <col style="width:36%;">
                            <col style="width:18%;">
                            <col style="width:18%;">
                            <col style="width:20%;">
                            <col style="width:8%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Variant</th>
                                <th class="text-center">Qty (+/−)</th>
                                <th class="text-center">Unit Cost</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- rows injected by JS from `window.__ADJ_ITEMS__` --}}
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Tip: Qty positive → IN, negative → OUT.</small>
            </div>
        </div>

    </form>

    {{-- templates --}}
    <template id="variantRowTpl">
        <tr data-vid="__VID__">
            <td>
                <div class="d-flex gap-10">
                    <img src="__IMG__" style="width:36px;height:36px;object-fit:cover;border-radius:6px">
                    <div>
                        <div class="fw-semibold">__VNAME__</div>
                        <small class="text-muted">__VSKU__</small>
                    </div>
                </div>
                <input type="hidden" name="rows[__IDX__][product_id]" value="__VID__">
            </td>
            <td><input type="number" step="0.001" class="form-control text-end" name="rows[__IDX__][qty]"
                    value="__QTY__" required></td>
            <td><input type="number" step="0.01" class="form-control text-end" name="rows[__IDX__][unit_cost]"
                    value="__COST__"></td>
            <td><input type="text" class="form-control" name="rows[__IDX__][reason]" value="__REASON__"
                    placeholder="(optional)"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btnDel"><iconify-icon
                        icon="solar:trash-bin-trash-outline"></iconify-icon></button>
            </td>
            {{-- hidden per-row warehouse/branch/direction fields --}}
            <input type="hidden" class="row-warehouse" name="rows[__IDX__][warehouse_id]" value="__WH__">
            <input type="hidden" class="row-branch" name="rows[__IDX__][branch_id]" value="__BR__">
            <input type="hidden" class="row-direction" name="rows[__IDX__][direction]" value="__DIR__">
        </tr>
    </template>
@endsection

@php
    // prepare an array of items in PHP context to avoid Blade / JS quoting issues
    $adjItems = $adjustment->items
        ->map(function ($it) use ($adjustment) {
            return [
                'id' => $it->id,
                'product_id' => $it->product_id,
                'product_name' => optional($it->product)->name ?? '',
                'product_sku' => optional($it->product)->sku ?? '',
                // use asset() here in PHP context
                'image' => optional($it->product)->image ? image($it->product->image) : asset('images/placeholder.png'),
                // signed quantity: IN positive, OUT negative
                'quantity' => ($it->direction === 'OUT' ? -1 : 1) * (float) $it->quantity,
                'unit_cost' => $it->unit_cost !== null ? (float) $it->unit_cost : null,
                'reason' => $it->note ?? '',
                'warehouse_id' => $it->warehouse_id ?? ($adjustment->warehouse_id ?? 0),
                'branch_id' =>
                    $it->branch_id ?? ($adjustment->branch_id ?? (auth()->check() ? auth()->user()->branch_id : 0)),
            ];
        })
        ->values()
        ->all();
@endphp

@section('script')
    <script>
        (function() {
            const $form = $('#adjForm');
            const $lines = $('#linesTable tbody');
            const $warehouseSelect = $('#warehouseSelect');
            const $branchSelect = $('#branchSelect');
            const isSuper = {{ $isSuper ? 'true' : 'false' }};
            const CSRF = "{{ csrf_token() }}";

            // stringShortner
            function stringShortner(name, length) {
                if (!name) return '';
                if (name.length > length) return name.slice(0, length) + '...';
                else return name;
            }

            // expose to JS safely — use unescaped JSON to preserve numeric types
            window.__ADJ_ITEMS__ = {!! json_encode($adjItems, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

            // helper to add a row
            let added = new Set(); // product_id set to prevent dupes
            function addRow(item, idx) {
                const vid = String(item.product_id);
                if (added.has(vid)) return;
                const tpl = $('#variantRowTpl').html()
                    .replaceAll('__VID__', vid)
                    .replaceAll('__VNAME__', stringShortner(item.product_name, 35))
                    .replaceAll('__VSKU__', item.product_sku || '')
                    .replaceAll('__IMG__', item.image || '{{ asset('images/placeholder.png') }}')
                    .replaceAll('__IDX__', idx)
                    .replaceAll('__QTY__', (item.quantity !== undefined ? parseFloat(item.quantity).toFixed(3) :
                        '0.000'))
                    .replaceAll('__COST__', (item.unit_cost !== null && item.unit_cost !== undefined) ? parseFloat(item
                        .unit_cost).toFixed(2) : '')
                    .replaceAll('__REASON__', item.reason ? item.reason.replace(/"/g, '&quot;') : '')
                    .replaceAll('__WH__', item.warehouse_id || '')
                    .replaceAll('__BR__', item.branch_id || '')
                    .replaceAll('__DIR__', (item.quantity >= 0 ? 'IN' : 'OUT'));

                const $row = $(tpl);
                $lines.append($row);
                added.add(vid);
            }

            // populate initial
            function populateInitial() {
                $lines.empty();
                added.clear();
                const items = window.__ADJ_ITEMS__ || [];
                items.forEach((it, i) => addRow(it, i));
                renumberRows();
            }

            populateInitial();

            // renumber names
            function renumberRows() {
                $lines.find('tr').each(function(i) {
                    $(this).find('input,select,textarea').each(function() {
                        const name = $(this).attr('name') || '';
                        if (!name) return;
                        const newName = name.replace(/rows\[\d+\]/, 'rows[' + i + ']');
                        $(this).attr('name', newName);
                    });
                });
            }

            // delete line
            $lines.on('click', '.btnDel', function() {
                const $tr = $(this).closest('tr');
                const vid = String($tr.data('vid'));
                added.delete(vid);
                $tr.remove();
                renumberRows();
            });

            // Apply all
            $('#btnApplyAll').on('click', function() {
                const costRaw = $('#bulkCost').val();
                const qtyRaw = $('#bulkQty').val();
                const hasCost = costRaw !== '' && !isNaN(parseFloat(costRaw));
                const hasQty = qtyRaw !== '' && !isNaN(parseFloat(qtyRaw));
                if (!hasCost && !hasQty) {
                    swal.fire({
                        icon: 'warning',
                        title: 'Enter cost and/or qty to apply'
                    });
                    return;
                }
                if (hasCost) {
                    const cost = parseFloat(costRaw).toFixed(2);
                    $lines.find('input[name$="[unit_cost]"]').each(function() {
                        this.value = cost;
                    });
                }
                if (hasQty) {
                    const qty = parseFloat(qtyRaw).toFixed(3);
                    $lines.find('input[name$="[qty]"]').each(function() {
                        this.value = qty;
                    });
                }
            });

            $('#btnClearLines').on('click', function() {
                $lines.empty();
                added.clear();
            });

            // normalize rows before submit
            function normalizeRowsBeforeSubmit() {
                let ok = true;
                $lines.find('tr').each(function() {
                    const $tr = $(this);
                    const $qty = $tr.find('input[name$="[qty]"]');
                    const raw = $qty.val();
                    if (raw === '' || isNaN(parseFloat(raw))) {
                        $qty.addClass('is-invalid');
                        ok = false;
                        return;
                    }
                    let num = parseFloat(raw);
                    const dir = (num >= 0) ? 'IN' : 'OUT';
                    $tr.find('.row-direction').val(dir);
                    $qty.val(num.toFixed(3));
                    // ensure warehouse/branch present
                    const wh = $warehouseSelect.val() || $tr.find('.row-warehouse').val() || '';
                    $tr.find('.row-warehouse').val(wh);
                    if (!isSuper) {
                        // hidden branch already set server-side
                    } else {
                        const br = $branchSelect.val() || $tr.find('.row-branch').val() || '';
                        $tr.find('.row-branch').val(br);
                    }
                });
                return ok;
            }

            // Save handlers
            function toggleButtons(disabled) {
                $('#btnSaveDraft, #btnSavePost').prop('disabled', disabled);
            }

            $('#btnSaveDraft').on('click', function() {
                $('#post_now').val('0');
                submitForm();
            });
            $('#btnSavePost').on('click', function() {
                $('#post_now').val('1');
                submitForm();
            });

            function submitForm() {
                if (!$warehouseSelect.val()) {
                    swal.fire({
                        icon: 'warning',
                        title: 'Select a warehouse'
                    });
                    return;
                }
                if ($lines.find('tr').length === 0) {
                    swal.fire({
                        icon: 'warning',
                        title: 'Add at least one line item'
                    });
                    return;
                }
                if (!normalizeRowsBeforeSubmit()) {
                    swal.fire({
                        icon: 'warning',
                        title: 'Please fix invalid rows'
                    });
                    return;
                }

                toggleButtons(true);
                const url = $form.attr('action');
                // serialize as array
                const data = $form.serializeArray();
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Saved',
                            timer: 900,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.href = "{{ route('inventory.adjustments.index') }}", 900);
                    },
                    error(xhr) {
                        const msg = xhr?.responseJSON?.msg || 'Failed';
                        Swal.fire({
                            icon: 'error',
                            title: msg
                        });
                        toggleButtons(false);
                    }
                });
            }

            // When warehouse changes, set per-row warehouse hidden input
            $warehouseSelect.on('change', function() {
                const wid = $(this).val();
                $lines.find('.row-warehouse').val(wid);
            });

            // branch select change
            if (isSuper) {
                $branchSelect.on('change', function() {
                    const bid = $(this).val();
                    $lines.find('.row-branch').val(bid);
                });
            }

            // init select2 (if used)
            window.S2 && S2.auto($(document));

            // on ready: ensure hidden values updated
            $(document).ready(function() {
                if ($lines.find('tr').length > 0 && $warehouseSelect.val()) {
                    $warehouseSelect.trigger('change');
                }
                if (isSuper && $branchSelect.val()) {
                    $branchSelect.trigger('change');
                }
            });

        })();
    </script>
@endsection
