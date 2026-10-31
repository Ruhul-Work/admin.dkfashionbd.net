@extends('backend.layouts.master')

@section('meta')
  <title>Warehouses</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Warehouse List</h6>
      <p class="text-muted m-0">Manage warehouses per branch</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Warehouses</li>
    </ul>
  </div>

  <div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Datatables</h5>
      <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
        <div class="search-set me-2"><div id="tableSearch" class="search-input"></div></div>
        <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
          @include('backend.include.buttons')
        </ul>

        <button class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 AjaxModal"
                data-ajax-modal="{{ route('inventory.warehouses.createModal') }}"
                data-size="md"
                data-onload="WarehousesIndex.onLoad"
                data-onsuccess="WarehousesIndex.onSaved">
          <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon> Add Warehouse
        </button>
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="warehousesTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">S.L</th>
            <th>Warehouse</th>
            <th>Branch</th>
            <th>Type</th>
            <th>Default</th>
            <th>Status</th>
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
  var DATATABLE_URL = "{{ route('inventory.warehouses.list.ajax') }}";

  // Delete
  $(document).on('click', '.btn-warehouse-delete', function(e){
    e.preventDefault();
    const url = $(this).data('url');

    const doDelete = () => $.ajax({
      url, type:'POST', dataType:'json',
      data: {_method:'DELETE', _token:'{{ csrf_token() }}'},
      success: function(res){
        $('.AjaxDataTable').DataTable().ajax.reload(null,false);
        window.Swal && Swal.fire({icon:'success', title: res?.msg || 'Deleted', timer:1000, showConfirmButton:false});
      },
      error: function(xhr){
        if (xhr.status === 422){
          window.Swal && Swal.fire({icon:'warning', title:'Blocked', text: xhr.responseJSON?.msg || 'Cannot delete this warehouse.'});
        } else if (xhr.status === 403){
          window.Swal && Swal.fire({icon:'warning', title:'Forbidden', text: xhr.responseJSON?.message || 'Permission denied'});
        } else {
          window.Swal && Swal.fire({icon:'error', title:'Failed', text:'Delete failed'});
        }
      }
    });

    if (window.Swal){
      Swal.fire({icon:'warning', title:'Delete warehouse?', text:'This action cannot be undone.',
        showCancelButton:true, confirmButtonText:'Yes, delete', confirmButtonColor:'#d33'
      }).then(r=>{ if(r.isConfirmed) doDelete(); });
    } else {
      if (confirm('Delete this warehouse?')) doDelete();
    }
  });
</script>
@endsection
