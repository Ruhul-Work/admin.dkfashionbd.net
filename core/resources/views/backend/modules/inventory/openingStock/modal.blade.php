<div class="modal-header py-16 px-24 border-0" data-modal-key="opening-create">
    <h5 class="modal-title">Add Opening Stock</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body p-24">
    <form id="openingCreateForm" action="{{ route('inventory.openingStock.store') }}" method="post" data-ajax="true">
        @csrf

        <div class="row g-16">
            {{-- Warehouse --}}
            <div class="col-md-6">
                <label class="form-label text-sm mb-8">Warehouse <span class="text-danger">*</span></label>
                <select name="warehouse_id" class="form-control js-s2-ajax"
                    data-url="{{ route('inventory.warehouses.select2') }}" data-placeholder="Select warehouse"
                    required></select>
                <div class="invalid-feedback d-block warehouse_id-error" style="display:none"></div>
            </div>

            {{-- Optional global note --}}
            <div class="col-md-6">
                <label class="form-label text-sm mb-8">Note</label>
                <input type="text" name="note" class="form-control radius-8" placeholder="Opening note (optional)">
            </div>
        </div>

        <hr class="my-16">

        <div class="d-flex align-items-center justify-content-between mb-12">
            <h6 class="mb-0">Items</h6>
            <button type="button" class="btn btn-sm btn-primary" id="btnAddRow">
                + Add Row
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="openingLinesTable">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-center" style="width:20%">Product</th>
                        <th class="text-center" style="width:35%">Qty</th>
                        <th class="text-center" style="width:35%">Unit Cost</th>
                        <th class="text-center" style="width:10%">Line Note</th>
                        <th style="width:5%"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
            <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
        </div>
    </form>
</div>

{{-- Row template --}}
<template id="openingRowTpl">
    <tr>
        <td>
            <select class="form-control js-s2-ajax" name="rows[__IDX__][product_id]"
                data-url="{{ route('product.select2') }}?sellable=1" data-placeholder="Select product"
                required></select>
            <div class="invalid-feedback d-block rows.__IDX__.product_id-error" style="display:none"></div>
        </td>
        <td>
            <input type="number" step="0.001" min="0.001" name="rows[__IDX__][qty]" class="form-control text-end"
                placeholder="0.000" required>
            <div class="invalid-feedback d-block rows.__IDX__.qty-error" style="display:none"></div>
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="rows[__IDX__][unit_cost]" class="form-control text-end"
                placeholder="(optional)">
            <div class="invalid-feedback d-block rows.__IDX__.unit_cost-error" style="display:none"></div>
        </td>
        <td>
            <input type="text" name="rows[__IDX__][note]" class="form-control" placeholder="(optional)">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btnDelRow">
                <iconify-icon icon="mdi:delete"></iconify-icon>
            </button>
        </td>
    </tr>
</template>



<script>
(function() {
    const $form = $('#openingCreateForm');
    const $modal = $form.closest('.modal');

    function addRow() {
        const $tb = $('#openingLinesTable tbody');
        const idx = $tb.find('tr').length;
        const html = $('#openingRowTpl').html().replaceAll('__IDX__', idx);
        const $row = $(html);
        $tb.append($row);
        // init only inside the new row
        if (window.S2 && typeof S2.auto === 'function') S2.auto($row);
    }

    // First row when modal loads
    addRow();

    // Add row
    $('#btnAddRow').on('click', addRow);

    // Delete row (at least 1 row kept)
    $(document).on('click', '.btnDelRow', function() {
        const $tb = $('#openingLinesTable tbody');
        if ($tb.find('tr').length <= 1) {
            $(this).closest('tr').find('input').val('');
            const $sel = $(this).closest('tr').find('select');
            $sel.val(null).trigger('change');
        } else {
            $(this).closest('tr').remove();
        }
    });

    // Init all select2 within the modal (warehouse + first row)
    if (window.S2 && typeof S2.auto === 'function') S2.auto($modal);
})();
</script>