<div class="modal-header py-16 px-24 border-0">
  <h5 class="modal-title">New Transfer</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body p-24">
  <form id="transferForm" action="{{ route('inventory.transfers.store') }}" method="post" data-ajax="true">
    @csrf
    <div class="row g-16">
      <div class="col-md-6">
        <label class="form-label text-sm mb-8">From Warehouse <span class="text-danger">*</span></label>
        <select name="from_warehouse_id" class="form-control js-s2-ajax"
          data-url="{{ route('inventory.warehouses.select2') }}"
          data-placeholder="Select source warehouse" required></select>
        <div class="invalid-feedback d-block from_warehouse_id-error" style="display:none"></div>
      </div>
      <div class="col-md-6">
        <label class="form-label text-sm mb-8">To Warehouse <span class="text-danger">*</span></label>
        <select name="to_warehouse_id" class="form-control js-s2-ajax"
          data-url="{{ route('inventory.warehouses.select2') }}"
          data-placeholder="Select target warehouse" required></select>
        <div class="invalid-feedback d-block to_warehouse_id-error" style="display:none"></div>
      </div>
      <div class="col-12">
        <label class="form-label text-sm mb-8">Note</label>
        <input type="text" name="note" class="form-control radius-8" placeholder="Optional">
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
      <table class="table table-bordered align-middle mb-0" id="transferLines">
        <thead>
          <tr class="bg-base-200">
            <th style="width:50%">Product</th>
            <th class="text-end" style="width:20%">Qty</th>
            <th style="width:10%"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
      <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
    </div>
  </form>
</div>

<template id="transferRowTpl">
  <tr>
    <td>
      <select class="form-control js-s2-ajax"
        name="rows[__IDX__][product_id]"
        data-url="{{ route('product.select2') }}?sellable=1"
        data-placeholder="Select product" required></select>
      <div class="invalid-feedback d-block rows.__IDX__.product_id-error" style="display:none"></div>
    </td>
    <td>
      <input type="number" step="0.001" min="0.001" name="rows[__IDX__][qty]" class="form-control text-end" placeholder="0.000" required>
      <div class="invalid-feedback d-block rows.__IDX__.qty-error" style="display:none"></div>
    </td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger btnDelRow"><iconify-icon icon="mdi:delete"></iconify-icon></button>
    </td>
  </tr>
</template>

<script>
(function(){
  const $form  = $('#transferForm');
  const $modal = $form.closest('.modal');

  function addRow(){
    const $tb = $('#transferLines tbody');
    const idx = $tb.find('tr').length;
    const html = $('#transferRowTpl').html().replaceAll('__IDX__', idx);
    const $row = $(html);
    $tb.append($row);
    window.S2 && S2.auto($row);
  }

  $('#btnAddRow').on('click', addRow);
  addRow();

  $(document).on('click', '.btnDelRow', function(){
    const $tb = $('#transferLines tbody');
    if ($tb.find('tr').length <= 1) {
      $(this).closest('tr').find('input').val('');
      $(this).closest('tr').find('select').val(null).trigger('change');
    } else {
      $(this).closest('tr').remove();
    }
  });

  window.S2 && S2.auto($modal);
})();
</script>
