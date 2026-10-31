@extends('backend.layouts.master')

@section('meta')
  <title>Fiscal Years</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Fiscal Year List</h6>
      <p class="m-0">Manage fiscal years</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Fiscal Years</li>
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

        @perm('fiscal-years.store')
            <button 
                class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8 AjaxModal"
                data-ajax-modal="{{ route('fiscal-years.createModal') }}"  
                data-size="lg"
                data-onsuccess="FiscalYearsIndex.onSaved">
                <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>Add Fiscal Year
        </button>
        @endperm
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="fiscalYearsTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">
              <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label">S.L</label>
              </div>
            </th>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
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
    var DATATABLE_URL = "{{ route('fiscal-years.list.ajax') }}";

    window.FiscalYearsIndex = {
        onSaved: function (res) {
            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            if (window.Swal) Swal.fire({icon:'success', title:'Created', text: res?.msg || 'Saved', timer:1000, showConfirmButton:false});
        }
    };

    $(document).on('click', '.btn-fiscal-year-delete', function(e){
        e.preventDefault();
        const url = $(this).data('url');

        const doDelete = () => {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function(res){
                    $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                    if (window.Swal) {
                        Swal.fire({ icon:'success', title: res?.msg || 'Deleted', timer: 1000, showConfirmButton:false });
                    }
                },
                error: function(xhr){
                    if (xhr.status === 422){
                        const msg = xhr.responseJSON?.msg || 'Cannot delete this fiscal year.';
                        Swal && Swal.fire({ icon:'warning', title:'Blocked', text: msg });
                    } else if (xhr.status === 403){
                        Swal && Swal.fire({ icon:'warning', title:'Forbidden', text: xhr.responseJSON?.message || 'Permission denied' });
                    } else {
                        Swal && Swal.fire({ icon:'error', title:'Failed', text:'Delete failed' });
                    }
                }
            });
        };

        if (window.Swal){
            Swal.fire({
                icon: 'warning',
                title: 'Delete fiscal year?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                confirmButtonColor: '#d33'
            }).then(r => { if (r.isConfirmed) doDelete(); });
        } else {
            if (confirm('Delete this fiscal year?')) doDelete();
        }
    });
  </script>
@endsection
