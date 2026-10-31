@extends('backend.layouts.master')

@section('meta')
  <title>Business Types</title>
@endsection

@section('content')
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Business Types</h6>
      <p class="text-muted m-0">Master list of business types</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Business Types</li>
    </ul>
  </div>

  <div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Types</h5>
      <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
        <div class="search-set me-2"><div id="tableSearch" class="search-input"></div></div>
        <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
          @include('backend.include.buttons')
        </ul>

        @perm('org.btypes.store')
        <button
          class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2 AjaxModal"
          data-ajax-modal="{{ route('org.btypes.createModal') }}"
          data-size="sm"
          data-onsuccess="BTypesIndex.onSaved">
          <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon> Add Type
        </button>
        @endperm
      </div>
    </div>

    <div class="card-body">
      <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="btypesTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">
              <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label">S.L</label>
              </div>
            </th>
            <th>Name</th>
            <th style="width:120px">Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
@endsection

@section('script')
<script>

  var DATATABLE_URL = "{{ route('org.btypes.list.ajax') }}";
 

  window.BTypesIndex = {
    onSaved: function(res){
      if ($.fn.dataTable) $('.AjaxDataTable').DataTable().ajax.reload(null, false);
    }
  };

    $(document).on('click', '.btn-btype-del', function(e){
    e.preventDefault();
    const url = $(this).data('url');
    if (window.Swal) {
        Swal.fire({icon:'warning', title:'Delete this type?', showCancelButton:true})
        .then(res=>{
            if(res.isConfirmed){
            $.ajax({type:'DELETE', url, headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
            .done(()=> $('.AjaxDataTable').DataTable().ajax.reload(null,false))
            .fail(()=> Swal.fire({icon:'error', title:'Failed'}));
            }
        });
    } else if (confirm('Delete this type?')) {
        $.ajax({type:'DELETE', url, headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
        .done(()=> $('.AjaxDataTable').DataTable().ajax.reload(null,false));
    }
    });

</script>
@endsection
