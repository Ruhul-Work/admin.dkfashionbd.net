@extends('backend.layouts.master')

@section('meta')
  <title>Purchase Orders</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Purchase Orders</h6>
      <p class=" m-0">Manage purchase orders</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Purchases</li>
    </ul>
  </div>

  <div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Purchase Orders</h5>
      <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
        <div class="search-set me-2">
          <div id="tableSearch" class="search-input"></div>
        </div>

        <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
          @include('backend.include.buttons')
        </ul>

        @perm('purchase.orders.store')
        <a class="d-flex btn btn-dark btn-sm px-12  radius-8 "
          href="{{ route('purchase.orders.create') }}">
           <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>New Purchase
        </a>
        @endperm

        {{-- @perm('purchase.orders.import')
        <button 
            class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 AjaxModal"
            data-size="lg"
            data-onsuccess="PurchaseIndex.onSaved"
            data-ajax-modal="{{ route('purchase.import') ?? '#' }}">
            <iconify-icon icon="ic:baseline-upload_file" class="text-xl"></iconify-icon>Import
        </button>
        {{-- @endperm --}}
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="purchaseTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">
              <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label">S.L</label>
              </div>
            </th>
            <th>PO#</th>
            <th>Supplier</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Outstanding</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Created At</th>
            <th>Invoice</th>
            <th style="width:120px">Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
@endsection

@section('script')
  <script>
    var DATATABLE_URL = "{{ route('purchase.list.ajax') }}";

    window.purchasePayIndex = {
      onSaved: function (res) {
        // DataTable current page reload
        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
        if (window.Swal) Swal.fire({icon:'success', title:'Saved', text: res?.msg || 'Saved', timer:1000, showConfirmButton:false});
      }
    };

    $(function(){
      // Initialize DataTable
     
      // Delete order handler (delegated)
      $(document).on('click', '.btn-order-delete', function(e){
        e.preventDefault();
        const url = $(this).data('url');

        const doDelete = function(){
          $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function(res){
              $('.AjaxDataTable').DataTable().ajax.reload(null, false);
              if (window.Swal) Swal.fire({ icon:'success', title: res?.msg || 'Deleted', timer: 1000, showConfirmButton:false });
            },
            error: function(xhr){
              if (xhr.status === 422){
                const msg = xhr.responseJSON?.msg || 'Cannot delete this purchase.';
                Swal && Swal.fire({ icon:'warning', title:'Blocked', text: msg });
              } else if (xhr.status === 403){
                Swal && Swal.fire({ icon:'error', title:'Forbidden', text: xhr.responseJSON?.message || 'Permission denied' });
              } else {
                Swal && Swal.fire({ icon:'error', title:'Failed', text:'Delete failed' });
              }
            }
          });
        };

        if (window.Swal){
          Swal.fire({
            icon: 'warning',
            title: 'Delete purchase order?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#d33'
          }).then(r => { if (r.isConfirmed) doDelete(); });
        } else {
          if (confirm('Delete this purchase?')) doDelete();
        }
      });



    });
  </script>
@endsection
