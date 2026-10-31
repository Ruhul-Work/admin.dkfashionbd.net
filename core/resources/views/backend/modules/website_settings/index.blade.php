@extends('backend.layouts.master')

@section('meta')
  <title>Website Settings</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Website Settings</h6>
      <p class="m-0">Manage website profiles and configurations</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Website Settings</li>
    </ul>
  </div>

  <div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Website Profiles</h5>
      <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
        <div class="search-set me-2">
          <div id="tableSearch" class="search-input"></div>
        </div>

        <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
          @include('backend.include.buttons')
        </ul>

        @perm('website-setting.website-settings.create')
            <a href="{{ route('website-setting.website-settings.create') }}" class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8">
                <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>Add Setting Profile
            </a>
        @endperm
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="settingsTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">S.L</th>
            <th>Logo</th>
            <th>Profile Key</th>
            <th>Company Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Date</th>
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
    var DATATABLE_URL = "{{ route('website-setting.website-settings.list.ajax') }}";

  $(document).on('click', '.btn-delete', function(e){
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
          const msg = xhr.responseJSON?.msg || 'Cannot delete this item.';
          Swal && Swal.fire({ icon:'warning', title:'Error', text: msg });
        }
      });
    };

    if (window.Swal){
      Swal.fire({
        icon: 'warning',
        title: 'Delete settings?',
        text: 'This action cannot be undone.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        confirmButtonColor: '#d33'
      }).then(r => { if (r.isConfirmed) doDelete(); });
    } else {
      if (confirm('Delete these settings?')) doDelete();
    }
  });
  </script>
@endsection
