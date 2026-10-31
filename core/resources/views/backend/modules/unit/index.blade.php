@extends('backend.layouts.master')

@section('meta')
  <title>Units</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Units List</h6>
      <p class="text-muted m-0">All Units</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Units</li>
    </ul>
  </div>

  <div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Datatables</h5>
      <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
        <div class="search-set me-2">
          <div id="tableSearch" class="search-input"></div>
        </div>

        <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
          @include('backend.include.buttons')
        </ul>
        <button
          class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 AjaxModal"
          data-ajax-modal="{{ route('units.createModal') }}"
          data-size="md"
          data-onsuccess="UnitsIndex.onSaved">
          <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon> Add Unit
        </button>
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="unitsTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">
              <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label">S.L</label>
              </div>
            </th>
            <th>Unit</th>
            <th>Code</th>
            <th>Precision</th>
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
  // DataTables URL 
  var DATATABLE_URL = "{{ route('units.list.ajax') }}";

  // ✅ AjaxModal submit success callback
  window.UnitsIndex = {
    onSaved: function(res){
      if (window.Swal) {
        Swal.fire({
          icon: 'success',
          title: res?.msg || 'Saved',
          timer: 1100,
          showConfirmButton: false,
        });
        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
      } else {
        table.ajax.reload(null, false); // fallback
      }
    }
  };

  // 🗑️ Delete (units)
  $(document).on('click', '.btn-unit-delete', function(e){
    e.preventDefault();
    const url = $(this).data('url');

    const doDelete = () => {
      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(res){
          if (window.Swal) {
            Swal.fire({
              icon:'success',
              title: res?.msg || 'Deleted',
              timer: 900,
              showConfirmButton:false,              
            });
             $('.AjaxDataTable').DataTable().ajax.reload(null, false);
          } else {
            table.ajax.reload(null, false);
          }
        },
        error: function(xhr){
          let title='Failed', text='Delete failed', icon='error';
          if (xhr.status === 422){
            icon='warning'; title='Blocked';
            text = xhr.responseJSON?.msg || 'Cannot delete this unit.';
          } else if (xhr.status === 403){
            icon='warning'; title='Forbidden';
            text = xhr.responseJSON?.message || 'Permission denied';
          }
          window.Swal ? Swal.fire({ icon, title, text }) : alert(text);
        }
      });
    };

    if (window.Swal){
      Swal.fire({
        icon: 'warning',
        title: 'Delete unit?',
        text: 'This action cannot be undone.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        confirmButtonColor: '#d33'
      }).then(r => { if (r.isConfirmed) doDelete(); });
    } else {
      if (confirm('Delete this unit?')) doDelete();
    }
  });
</script>
@endsection
