@extends('backend.layouts.master')

@section('meta')
    <title>Advanced Stock Adjustment</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Stock Adjustment </h6>
            <p class="text-muted m-0">Pick parents on left → edit variants on right</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium"><a href="{{ route('backend.dashboard') }}"
                    class="d-flex align-items-center gap-1 hover-text-primary"><iconify-icon
                        icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard</a></li>
            <li>-</li>
            <li class="fw-medium"><a href="{{ route('inventory.adjustments.index') }}">Stock Adjustments</a></li>
        </ul>
    </div>

    <form id="adjForm" action="{{ route('inventory.adjustments.store') }}" method="post" data-ajax-form="true">
        @csrf

        @php $isSuper = auth()->check() && optional(auth()->user()->role)->is_super == 1; @endphp

        <div class="card mb-16">
            <div class="card-body p-14">
                <div class="row g-16">
                    {{-- Warehouse --}}
                    <div class="col-md-3">
                        <label class="form-label text-sm">Warehouse <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouseSelect" class="form-control js-s2-ajax"
                            data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="Select warehouse"
                            required>
                        </select>
                        <div class="invalid-feedback d-block warehouse_id-error" style="display:none"></div>
                    </div>

                    {{-- Branch --}}
                    <div class="col-md-3">
                        <label class="form-label text-sm">Branch <span class="text-danger">*</span></label>
                        @if ($isSuper)
                            <select name="branch_id" id="branchSelect" class="form-control js-s2-ajax"
                                data-url="{{ route('org.branches.select2') }}" data-placeholder="Select branch">
                            </select>
                        @else
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->branch->name ?? 'Default Branch' }}" readonly disabled>
                            <input type="hidden" name="branch_id" id="branchId" value="{{ auth()->user()->branch_id ?? 1 }}">
                        @endif
                    </div>

                    {{-- Date & Time --}}
                    <div class="col-md-3">
                        <label class="form-label text-sm mb-6">Date & Time</label>
                        <input type="datetime-local" name="when" class="form-control form-control-sm"
                            value="{{ now()->format('Y-m-d\TH:i') }}">
                        <div class="invalid-feedback d-block when-error" style="display:none"></div>
                    </div>

                    {{-- Global Reason --}}
                    <div class="col-md-3">
                        <label class="form-label text-sm mb-6">Global Reason</label>
                        <input type="text" name="global_reason" class="form-control form-control-sm" placeholder="(optional)">
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            {{-- LEFT: Parents gallery --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-9">
                                <select id="parentFilter" class="form-control js-s2-ajax"
                                    data-url="{{ route('product.parents.select2') }}"
                                    data-placeholder="Search parents (name, sku)">
                                </select>
                            </div>
                            <div class="col-3">
                                <button type="button" id="btnClearParents" class="btn btn-sm btn-outline-danger w-100 px-0  gap-1">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-12">
                        <div id="parentsGrid" class="grid grid-cols-2 gap-12"></div>
                        <div class="d-flex justify-content-center mt-12">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLoadMore">
                                Load more
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            {{-- RIGHT: Variants editor --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Selected Variants</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" step="0.01" id="bulkCost" class="form-control form-control-sm w-110"
                                placeholder="Unit cost">
                            <input type="number" step="0.001" id="bulkQty" class="form-control form-control-sm w-110"
                                placeholder="Qty (+/−)">
                            <button type="button" class="btn btn-sm btn-outline-primary d-flex minw-120" id="btnApplyAll"><iconify-icon icon="material-symbols:check" class="text-lg"></iconify-icon> Apply to
                                all  </button>
                            <button type="button" class="btn btn-sm d-flex btn-outline-danger minw-100"
                                id="btnClearLines"><iconify-icon icon="solar:trash-bin-trash-outline" class="text-xl"></iconify-icon> Clear</button>
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
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted">Tip: Qty positive → IN, negative → OUT.</small>
                    </div>
                    {{-- <div class="card-footer d-flex justify-content-center gap-2">
                        <a href="{{ route('inventory.adjustments.index') }}"
                            class="btn border border-danger-600 text-danger-600">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save Adjustment</button>
                    </div> --}}

                    <div class="card-footer d-flex justify-content-center gap-2">
                        <a href="{{ route('inventory.adjustments.index') }}"
                            class="btn btn-sm btn-outline-danger">Cancel</a>

                        <button type="button" id="btnSaveDraft" class="btn btn-sm btn-outline-secondary">Save
                            Draft</button>
                        <button type="button" id="btnSavePost" class="btn btn-sm btn-success">Save & Post</button>
                    </div>

                    <!-- hidden flag to indicate immediate post -->
                    <input type="hidden" name="post_now" id="post_now" value="0">
                </div>
            </div>
        </div>

        {{-- Hidden inputs (rows) dynamically append হবে --}}
    </form>

    {{-- Templates --}}
    <template id="parentCardTpl">
        <div class="wh-card card hover-shadow cursor-pointer" data-id="__ID__">
            <div class="card-body p-10">
                <div class="d-flex gap-10">
                    <img class="rounded" src="__IMG__" alt="" style="width:54px;height:54px;object-fit:cover;">
                    <div class="flex-1">
                        <div class="fw-semibold lh-sm mb-2">__NAME__</div>
                        <div class="text-muted small">__SKU__</div>
                    </div>
                </div>
                <div class="mt-8 d-flex gap-6 flex-wrap">
                    <!-- optional badges -->
                </div>
            </div>
        </div>
    </template>


    <template id="variantRowTpl">
        <tr data-vid="__VID__" data-parent="__PID__">
            <td>
                <div class="d-flex gap-10">
                    <img src="__IMG__" style="width:36px;height:36px;object-fit:cover;border-radius:6px">
                    <div>
                        <div class="fw-semibold">__VNAME__</div>
                        <small class="text-muted">__VSKU__</small>
                        <div class="small text-muted fw-semibold text-danger-300">Stock: <span
                                class="sys-qty">__SYSQ__</span></div>
                    </div>
                </div>
                <input type="hidden" name="rows[__IDX__][product_id]" value="__VID__">
            </td>
            <td>
                <input type="number" step="0.001" class="form-control form-control-sm text-end qty-input" name="rows[__IDX__][qty]"
                    placeholder="+/- 0.000" required>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm text-end" name="rows[__IDX__][unit_cost]"
                    placeholder="0.00">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="rows[__IDX__][reason]" placeholder="(optional)">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btnDel"><iconify-icon icon="solar:trash-bin-trash-outline" class="text-xl"></iconify-icon></button>
            </td>

            <!-- hidden field for direction; will be set before submit -->
            <input type="hidden" name="rows[__IDX__][direction]" class="row-direction" value="IN">
            <!-- optional per-item warehouse/branch (if you allow overrides) -->
            <input type="hidden" name="rows[__IDX__][warehouse_id]" class="row-warehouse" value="">
            <input type="hidden" name="rows[__IDX__][branch_id]" class="row-branch" value="">
        </tr>
    </template>
@endsection



@section('script')
    <script>
        (function() {
            // ---- ROUTE TEMPLATES (Blade must render these) ----
            // warehouse detail endpoint template: replace :id with actual id
            const warehouseAjaxUrlTemplate = "{{ route('inventory.warehouses.showForAjax', ['warehouse' => ':id']) }}";
            // stock currents bulk endpoint (POST)
            const stockCurrentsBulkUrl = "{{ route('inventory.adjustments.stock.currents.bulk') }}";
            // product variants base url prefix (we will append /{parent}/variants)
            const productVariantsBase = "{{ url('product/products') }}";


            // ---- State + DOM refs ----
            const $form = $('#adjForm');
            const $doc = $(document);
            const $warehouse = $form.find('select[name="warehouse_id"]');
            const $branchSelect = $('#branchSelect'); // present for super users (optional)
            const $branchHidden = $('#branchId'); // present for normal users (hidden)
            const $parentsGrid = $('#parentsGrid');
            const $lines = $('#linesTable tbody');
            const addedVariants = new Set();

            window.S2 && S2.auto($doc);

            // Helper: current branch id (returns integer or 0)
            function getCurrentBranchId() {
                if ($branchSelect.length && $branchSelect.val()) {
                    return +$branchSelect.val();
                }
                if ($branchHidden.length && $branchHidden.val()) {
                    return +$branchHidden.val();
                }
                return 0;
            }

            // Helper: current warehouse id
            function getCurrentWarehouseId() {
                return $warehouse.val() ? +$warehouse.val() : 0;
            }

            // Debug helper
            function dbg(...args) {
                if (window.console) console.log('[AdjDBG]', ...args);
            }

            // ---- Select2 hook on warehouse: populate branch (from option or AJAX) ----
            $warehouse.on('select2:select', function(evt) {
                const data = evt.params ? evt.params.data : null;
                const wid = getCurrentWarehouseId();
                let branchFromOption = null;
                if (data && (data.branch_id !== undefined)) branchFromOption = data.branch_id;

                if (branchFromOption !== null) {
                    // if branch embedded in select2 option, use it
                    dbg('warehouse select has embedded branch', branchFromOption);
                    setBranch(branchFromOption, data.branch_name || null);
                    // update all sys-qtys
                    triggerSystemQtyRefresh();
                    return;
                }

                // fallback: call warehouse detail endpoint
                if (!wid) {
                    setBranch(null);
                    triggerSystemQtyRefresh();
                    return;
                }

                const url = warehouseAjaxUrlTemplate.replace(':id', wid);
                dbg('fetching warehouse details', url);
                $.getJSON(url)
                    .done(function(res) {
                        const bId = res.branch_id ?? 0;
                        setBranch(bId, res.branch_name ?? null);
                    })
                    .fail(function(err) {
                        dbg('warehouse detail ajax failed', err);
                    })
                    .always(function() {
                        triggerSystemQtyRefresh();
                    });
            });

            // if warehouse cleared
            $warehouse.on('select2:clear', function() {
                setBranch(null);
                triggerSystemQtyRefresh();
            });

            // When branch (admin) manual change -> update system qtys
            $branchSelect.on('change', function() {
                triggerSystemQtyRefresh();
            });

            // setBranch: sets either hidden input or select value
            function setBranch(branchId, branchName = null) {
                if ($branchSelect.length) {
                    if (branchId) {
                        // ensure option exists then select
                        if ($branchSelect.find("option[value='" + branchId + "']").length === 0) {
                            const text = branchName ?? ('Branch ' + branchId);
                            const newOpt = new Option(text, branchId, true, true);
                            $branchSelect.append(newOpt);
                        }
                        $branchSelect.val(branchId).trigger('change');
                    } else {
                        $branchSelect.val(null).trigger('change');
                    }
                } else if ($branchHidden.length) {
                    if (branchId) $branchHidden.val(branchId);
                }
            }

            // stringShortner
            function stringShortner(name, length) {
                if (!name) return '';
                if (name.length > length) return name.slice(0, length) + '...';
                else return name;
            }

            // ---- Parents gallery load (kept simple) ----
            let page = 1,
                lastQuery = '';

            function loadParents(reset = false) {
                const $parentSel = $('#parentFilter');

                const q = ($parentSel.val() && $parentSel.find(':selected').text()) || lastQuery || '';

                if (reset) {
                    page = 1;
                    $parentsGrid.empty();
                }
                lastQuery = q;

                $.getJSON("{{ route('product.parents.index') }}", {
                        q,
                        page
                    })
                    .done(function(res) {
                        const items = res.data || [];
                        items.forEach(p => $parentsGrid.append(cardHTML(p)));
                        if (!res.next_page) $('#btnLoadMore').prop('disabled', true).text('No more');
                        else {
                            $('#btnLoadMore').prop('disabled', false).text('Load more');
                            page++;
                        }
                    });
            }

            // cardHTML - reuse your template
            function cardHTML(p) {
                let html = $('#parentCardTpl').html()
                    .replaceAll('__ID__', p.id)
                    .replaceAll('__IMG__', p.image || '{{ asset('images/placeholder.png') }}')
                    .replaceAll('__NAME__', stringShortner(p.name, 25))
                    .replaceAll('__SKU__', p.sku || '');
                return $(html);
            }

            $('#btnLoadMore').on('click', () => loadParents(false));

            // Filters
            $('#parentFilter').on('select2:select', () => loadParents(true));

            loadParents(true);

            $('#btnClearParents').on('click', function() {
                $('#parentFilter').val(null).trigger('change');
                $parentsGrid.find('.wh-card').removeClass('active');
                clearAllLines();
                loadParents(true);
            });

            function clearAllLines() {
                addedVariants.clear();
                $lines.empty();
            }


            // parent click toggles
            $parentsGrid.on('click', '.wh-card', function() {
                const $c = $(this);
                const id = String($c.data('id'));
                const active = $c.toggleClass('active').hasClass('active');
                if (active) fetchVariants(id);
                else removeVariantsOfParent(id);
            });

            function removeVariantsOfParent(pid) {
                $lines.find('tr[data-parent="' + pid + '"]').each(function() {
                    addedVariants.delete(String($(this).data('vid')));
                    $(this).remove();
                });
                renumberRows();
            }


            // ---- fetchVariants: call server with warehouse+branch -> add rows with system_qty if provided ----
            function fetchVariants(parentId) {
                const warehouseId = getCurrentWarehouseId() || '';
                const branchId = getCurrentBranchId() || 0;
                const url = productVariantsBase + '/' + parentId + '/variants';

                dbg('fetchVariants', {
                    parentId,
                    warehouseId,
                    branchId,
                    url
                });

                $.getJSON(url, {
                        warehouse_id: warehouseId,
                        branch_id: branchId
                    })
                    .done(function(res) {
                        const list = res.data || [];
                        list.forEach(v => addVariantRow(parentId, v));
                        // after add, if any row has system_qty undefined, call bulk update
                        maybeBulkRefresh();
                    })
                    .fail(function(err) {
                        dbg('fetchVariants failed', err);
                    });
            }

            // ---- addVariantRow: show row and set sys-qty placeholder ----
            function addVariantRow(pid, v) {
                const vid = String(v.id);
                if (addedVariants.has(vid)) return;
                const idx = $lines.find('tr').length;
                const sysq = (v.system_qty !== undefined && v.system_qty !== null) ? parseFloat(v.system_qty).toFixed(
                    3) : '__SYS__';

                let html = $('#variantRowTpl').html()
                    .replaceAll('__VID__', vid)
                    .replaceAll('__PID__', pid)
                    .replaceAll('__VNAME__', stringShortner(v.name, 35))
                    .replaceAll('__VSKU__', v.sku || '')
                    .replaceAll('__IMG__', v.image || '{{ asset('images/placeholder.png') }}')
                    .replaceAll('__IDX__', idx)
                    .replaceAll('__SYSQ__', sysq);

                const $row = $(html);
                // set per-item warehouse/branch hidden fields to header defaults
                const wh = getCurrentWarehouseId() || '';
                const br = getCurrentBranchId() || 0;
                $row.find('.row-warehouse').val(wh);
                $row.find('.row-branch').val(br);
                if (v.default_unit_cost) $row.find('input[name$="[unit_cost]"]').val(v.default_unit_cost);

                $lines.append($row);
                addedVariants.add(vid);
                renumberRows();
            }

            // btnApplyAll handler -  bulkCost, bulkQty

            $('#btnApplyAll').on('click', function() {
                const costRaw = $('#bulkCost').val();
                const qtyRaw = $('#bulkQty').val();

                const hasCost = costRaw !== '' && !isNaN(parseFloat(costRaw));
                const hasQty = qtyRaw !== '' && !isNaN(parseFloat(qtyRaw));

                if (!hasCost && !hasQty) {
                    window.Swal && Swal.fire({
                        icon: 'info',
                        title: 'Nothing to apply without Unit or Quantity',
                        showConfirmButton: true
                    });
                    return;
                }

                if (hasCost) {
                    const cost = parseFloat(costRaw);
                    $('#linesTable tbody input[name$="[unit_cost]"]').each(function() {
                        this.value = cost.toFixed(2);
                    });
                }

                if (hasQty) {
                    // qty পজিটিভ/নেগেটিভ—যা দিবে তাই বসবে
                    const qty = parseFloat(qtyRaw);
                    $('#linesTable tbody input[name$="[qty]"]').each(function() {
                        this.value = qty.toFixed(3);
                    });
                }

            });
            $('#btnClearLines').on('click', clearAllLines);

            // global reason→ empty line reasons
            $form.find('input[name="global_reason"]').on('input', function() {
                const val = this.value;
                $lines.find('input[name$="[reason]"]').each(function() {
                    if (!this.value) this.value = val;
                });
            });

            // renumber inputs
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

            // if any row has placeholder '__SYS__', call bulk endpoint to fill them
            function maybeBulkRefresh() {
                const ids = [];
                $lines.find('tr').each(function() {
                    const vid = String($(this).data('vid'));
                    const sysCell = $(this).find('.sys-qty').text();
                    if (sysCell === '__SYS__') ids.push(vid);
                });
                if (ids.length === 0) return;
                bulkFetchSystemQty(ids);
            }

            // trigger for full refresh (e.g., warehouse/branch changed)
            function triggerSystemQtyRefresh() {
                // collect all product ids on grid and call bulk
                const pids = [];
                $lines.find('tr').each(function() {
                    pids.push(String($(this).data('vid')));
                });
                if (pids.length === 0) return;
                bulkFetchSystemQty(pids);
            }

            // ---- bulk fetch system qtys and fill into rows ----
            function bulkFetchSystemQty(productIds) {
                const wid = getCurrentWarehouseId();
                const bid = getCurrentBranchId();
                if (!wid) {
                    // clear all sys-qtys to 0 if no warehouse selected
                    $lines.find('.sys-qty').text('0.000');
                    return;
                }

                dbg('bulkFetchSystemQty', {
                    productIds,
                    wid,
                    bid
                });

                $.ajax({
                    url: stockCurrentsBulkUrl,
                    method: 'POST',
                    data: {
                        product_ids: productIds,
                        warehouse_id: wid,
                        branch_id: bid,
                        _token: "{{ csrf_token() }}"
                    },
                    success(res) {
                        const map = res.data || {};
                        $lines.find('tr').each(function() {
                            const vid = String($(this).data('vid'));
                            const sys = map[vid] !== undefined ? parseFloat(map[vid]).toFixed(3) :
                                '0.000';
                            $(this).find('.sys-qty').text(sys);
                            // keep hidden warehouse/branch synced
                            $(this).find('.row-warehouse').val(wid);
                            $(this).find('.row-branch').val(bid);
                        });
                    },
                    error(xhr) {
                        dbg('bulkFetchSystemQty failed', xhr);
                    }
                });
            }

            // ---- normalize rows before submit (preserve signed qty) ----
            function normalizeRowsBeforeSubmit() {
                let ok = true;
                $lines.find('tr').each(function() {
                    const $tr = $(this);
                    const $qty = $tr.find('input[name$="[qty]"]');
                    const raw = $qty.val();
                    if (raw === '' || raw === null || isNaN(parseFloat(raw))) {
                        $qty.addClass('is-invalid');
                        ok = false;
                        return;
                    }
                    let qty = parseFloat(raw);
                    const dir = qty >= 0 ? 'IN' : 'OUT';
                    $tr.find('input.row-direction').val(dir);
                    $qty.val(qty.toFixed(3));
                });
                return ok;
            }

            // submit handlers (save draft / save & post)
            $('#btnSaveDraft').on('click', function() {
                $('#post_now').val('0');
                doSubmit();
            });
            $('#btnSavePost').on('click', function() {
                $('#post_now').val('1');
                doSubmit();
            });

            function doSubmit() {
                if (!getCurrentWarehouseId()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select a warehouse'
                    });
                    return;
                }
                if ($lines.find('tr').length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Add at least one item'
                    });
                    return;
                }
                if (!normalizeRowsBeforeSubmit()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fix quantities'
                    });
                    return;
                }
                const data = $form.serialize();
                $('#btnSaveDraft,#btnSavePost').prop('disabled', true);
                $.ajax({
                    url: $form.attr('action'),
                    method: $form.attr('method') || 'POST',
                    data: data,
                    success(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg || 'Saved',
                            timer: 900,
                            showConfirmButton: false
                        });
                        setTimeout(() => location = "{{ route('inventory.adjustments.index') }}", 900);
                    },
                    error(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: xhr?.responseJSON?.msg || 'Error'
                        });
                        $('#btnSaveDraft,#btnSavePost').prop('disabled', false);
                    }
                });
            }

            // delete row handler
            $lines.on('click', '.btnDel', function() {
                const $tr = $(this).closest('tr');
                addedVariants.delete(String($tr.data('vid')));
                $tr.remove();
                renumberRows();
            });

            // initial: if warehouse preset, trigger refresh on load
            $(document).ready(function() {
                if (getCurrentWarehouseId()) {
                    // try to trigger select2:select to populate branch if option had metadata
                    $warehouse.trigger('select2:select');
                    // and trigger a system qty fetch
                    triggerSystemQtyRefresh();
                }
            });

        })();
    </script>
@endsection
