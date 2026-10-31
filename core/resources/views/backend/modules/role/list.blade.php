{{-- resources/views/backend/modules/role/list.blade.php --}}
@extends('backend.layouts.master')

@section('meta')
  <title>Role List</title>
@endsection

@section('content')

    {{-- Breadcrumb/Header --}}
    <div class=" d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Role List</h6>
            <p class="text-muted m-0">Show all role</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href={{ route('backend.dashboard') }} class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Role List</li>
        </ul>
    </div>
    {{-- End Breadcrumb/Header --}}

  <div class="card basic-data-table">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Datatables</h5>

        <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
          <!-- Search box container (DataTables filter এখানে move হবে) -->
          <div class="search-set me-2">
            <div id="tableSearch" class="search-input"></div>
          </div>

          <!-- Custom buttons (inline list) -->
          <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
            @include('backend.include.buttons')
          </ul>
          <!-- Add New Role Button -->
          @perm('rbac.role.index')
            <button
              class="btn btn-warning btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2">
              <a href="{{ route('rbac.role.index') }}">
              <i class="ri-user-settings-line text-sm me-14 w-auto"></i>
              <span>Bulk Access</span>
              </a>
            </button>
          @endperm
          @perm('rbac.role.create')
            <button type="button"
              class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2"
              data-bs-toggle="modal" data-bs-target="#exampleModal">
              <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
              Add New Role
            </button>
          @endperm
        </div>
    </div>

      <div class="card-body">
        <div style="overflow-x: auto;">
          <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="dataTable" data-page-length="10" style="width:100%">
            <thead>
              <tr>
                <th scope="col" style="width:60px">
                  <div class="form-check style-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="select-all">
                    <label class="form-check-label">S.L</label>
                  </div>
                </th>
                <th scope="col" style="min-width:200px">Name</th>
                <th scope="col">Key</th>
                <th scope="col" style="width:120px">Type</th>
                <th scope="col" style="width:100px">Action</th>
              </tr>
            </thead>
            <tbody><!-- filled by DataTables via AJAX --></tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- Modal Start -->
     <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                 <form id="roleCreateForm" action="{{ route('rbac.role.store') }}" method="post">
                    @csrf
                    <div class="row">
                      <div class="col-12 mb-20">
                        <label class="form-label text-sm mb-8">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control radius-8" placeholder="Enter Role Name" required>
                        <div class="invalid-feedback d-block name-error" style="display:none"></div>
                      </div>

                      <div class="col-12 mb-20">
                        <label class="form-label text-sm mb-8">Key (optional)</label>
                        <input type="text" name="key" class="form-control radius-8" placeholder="e.g. manager, sales_rep">
                        <div class="invalid-feedback d-block key-error" style="display:none"></div>
                      </div>

                      <div class="col-12 mb-20">
                        <label class="form-label text-sm mb-8">Super Admin?</label>
                        <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                <input class="form-check-input" type="checkbox" name="is_super" role="switch"  value="1" id="isSuperSwitch">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="switch2">Grant all permissions</label>
                            </div> 
                      </div>
                            

                      <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                        <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-48 py-12 radius-8">
                          Save
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End -->

    <!-- Edit Role Modal -->
    <div class="modal fade" id="roleEditModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
          <div class="modal-header py-16 px-24 border-0">
            <h5 class="modal-title">Edit Role</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-24">
            <form id="roleEditForm" method="post">
              @csrf
              @method('PUT')
              <div class="row">
                <div class="col-12 mb-20">
                  <label class="form-label text-sm mb-8">Role Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control radius-8" required>
                  <div class="invalid-feedback d-block name-error" style="display:none"></div>
                </div>

                <div class="col-12 mb-20">
                  <label class="form-label text-sm mb-8">Key (optional)</label>
                  <input type="text" name="key" class="form-control radius-8">
                  <div class="invalid-feedback d-block key-error" style="display:none"></div>
                </div>

                <div class="col-12 mb-20">
                  <label class="form-label text-sm mb-8">Super Admin?</label>
                  <div class="form-switch switch-purple d-flex align-items-center gap-3">
                    <input class="form-check-input" type="checkbox" name="is_super" value="1" id="editIsSuper">
                    <label class="form-check-label" for="editIsSuper">Grant all permissions</label>
                  </div>
                </div>

                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                  <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Edit Role Modal End -->

@endsection

@section('script')
  <script>
    // AjaxDataTable init 
    var DATATABLE_URL = "{{ route('rbac.role.list.ajax') }}";

    
    //role create form submit
    $(document).on('submit', '#roleCreateForm', function(e){
      e.preventDefault();
      const $form = $(this);
      const url   = $form.attr('action');
      const data  = $form.serialize();

      $form.find('.name-error,.key-error').hide().text('');

      $.ajax({
        type: 'POST',
        url,
        data,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res){
          $('#exampleModal').modal('hide');
          $form[0].reset();
          $('.AjaxDataTable').DataTable().ajax.reload(null, false);

          if (window.Swal) {
            Swal.fire({ icon:'success', title:'Success', text:res.msg || 'Role created', timer:1500, showConfirmButton:false });
          }
        },
        error: function(xhr){
          if (xhr.status === 422) {
            const errs = xhr.responseJSON.errors || {};
            if (errs.name) $form.find('.name-error').text(errs.name[0]).show();
            if (errs.key)  $form.find('.key-error').text(errs.key[0]).show();
          } else {
            Swal && Swal.fire({ icon:'error', title:'Failed', text:'Something went wrong' });
          }
        }
      });
    });


    // Edit button click → load JSON → fill form → open modal
    $(document).on('click.role', '.btn-role-edit', function(e){
      e.preventDefault();
      const id = $(this).data('id');
      const showUrl   = "{{ route('rbac.role.edit', ':id') }}".replace(':id', id);
      const updateUrl = "{{ route('rbac.role.update', ':id') }}".replace(':id', id);
      const $form = $('#roleEditForm');
      $form.find('.name-error,.key-error').hide().text('');

      $.getJSON(showUrl, function(res){
        // set form action
        $form.attr('action', updateUrl);
        $form.find('input[name="name"]').val(res.name || '');
        $form.find('input[name="key"]').val(res.key || '');
        $form.find('input[name="is_super"]').prop('checked', !!res.is_super);

        // যদি বর্তমান ইউজার সুপার না হয় → toggle disable
        @if(! auth()->user()?->isSuper())
          $form.find('input[name="is_super"]').prop('disabled', true);
        @endif
        const modal = new bootstrap.Modal(document.getElementById('roleEditModal'));
        modal.show();
      })
      .fail(function(){
        Swal && Swal.fire({icon:'error', title:'Failed', text:'Could not load role.'});
      });
    });

    // Edit form submit → AJAX PUT
    $(document).on('submit', '#roleEditForm', function(e){
      e.preventDefault();
      const $form = $(this);
      const url   = $form.attr('action');
      const data  = $form.serialize();

      $form.find('.name-error,.key-error').hide().text('');

      $.ajax({
        type: 'POST', // method spoofed with _method=PUT
        url, data, dataType: 'json',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res){
          // hide modal
          $('#roleEditModal').modal('hide');
          // reload current page of DataTable
          $('.AjaxDataTable').DataTable().ajax.reload(null, false);
          Swal && Swal.fire({ icon:'success', title:'Updated', text:res.msg || 'Role updated', timer:1200, showConfirmButton:false });
        },
        error: function(xhr){
          if (xhr.status === 422) {
            const errs = xhr.responseJSON.errors || {};
            if (errs.name) $('#roleEditForm .name-error').text(errs.name[0]).show();
            if (errs.key)  $('#roleEditForm .key-error').text(errs.key[0]).show();
          } else if (xhr.status === 403) {
            Swal && Swal.fire({ icon:'warning', title:'Forbidden', text:xhr.responseJSON?.message || 'Permission denied' });
          } else {
            Swal && Swal.fire({ icon:'error', title:'Failed', text:'Something went wrong' });
          }
        }
      });
    });

    // Delete role
    $(document).on('click', '.btn-role-delete', function(e){
      e.preventDefault();
      const id = $(this).data('id');
      const url = "{{ route('rbac.role.destroy', ':id') }}".replace(':id', id);

      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: 'POST',
            url: url,
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function(res){
              $('.AjaxDataTable').DataTable().ajax.reload(null, false);
              Swal.fire({ icon:'success', title:'Deleted', text:res.msg || 'Role deleted', timer:1200, showConfirmButton:false });
            },
            error: function(xhr){
              if(xhr.status === 403){
                Swal.fire({ icon:'error', title:'Forbidden', text:xhr.responseJSON?.msg || 'Not allowed' });
              } else {
                Swal.fire({ icon:'error', title:'Failed', text:'Something went wrong' });
              }
            }
          });
        }
      });
    });


  </script>
@endsection
