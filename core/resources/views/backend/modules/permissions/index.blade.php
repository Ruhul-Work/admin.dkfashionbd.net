@extends('backend.layouts.master')

@section('meta')
    <title>Permissions </title>
@endsection

@section('content')

    {{-- Breadcrumb/Header --}}
    <div class=" d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Permissions List</h6>
            <p class="text-muted m-0">Register keys & attach routes</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href={{ route('backend.dashboard') }} class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Permissions List</li>
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
                @perm('rbac.permissions.store', 'add')
                    <button type="button" class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#permCreateModal">
                        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                        Add Permission
                    </button>
                @endperm
            </div>
        </div>
        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="permTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                        <th>Module</th>
                        <th>Permission</th>
                        <th>Routes</th>
                        <th style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    </div>


    <!-- Create Permission Modal -->
    <div class="modal fade" id="permCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border-0">
                    <h5 class="modal-title">Add Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-24">
                    <form id="permCreateForm" action="{{ route('rbac.permissions.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <!-- Module (searchable + creatable) -->
                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Module <span class="text-danger">*</span></label>
                                <select id="permModule" name="module" class="form-control select-2 radius-8"
                                    required></select>
                                <div class="form-text">টাইপ করলেই পুরনো মডিউল দেখাবে; নতুন হলে Enter চাপলেই তৈরি হবে।</div>
                                <div class="invalid-feedback d-block module-error" style="display:none"></div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control radius-8"
                                    placeholder="e.g. Order Management" required>
                                <div class="invalid-feedback d-block name-error" style="display:none"></div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Key <span class="text-danger">*</span></label>
                                <input type="text" name="key" class="form-control radius-8"
                                    placeholder="e.g. orders.manage" required>
                                <div class="invalid-feedback d-block key-error" style="display:none"></div>
                            </div>

                            <div class="col-md-3 mb-20">
                                <label class="form-label text-sm mb-8">Sort</label>
                                <input type="number" name="sort" class="form-control radius-8" placeholder="0">
                                <div class="invalid-feedback d-block sort-error" style="display:none"></div>
                            </div>

                            <div class="col-md-3 mb-20">
                                <label class="form-label text-sm mb-8">Active?</label>
                                <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked
                                        id="permIsActive">
                                    <label class="form-check-label" for="permIsActive">Enable this permission</label>
                                </div>
                            </div>

                            <!-- Routes (multi-select + tags) -->
                            <div class="col-12 mb-10">
                                <label class="form-label text-sm mb-8">Routes <span class="text-danger">*</span></label>
                                <select id="permRoutes" name="routes[]" class="form-control radius-8"
                                    multiple="multiple"></select>
                                <div class="form-text">টাইপ করে পছন্দ করো বা নতুন route লিখে Enter চাপো।</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
                            <button type="button"
                                class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- End Create Permission Modal -->

    <!-- Edit Permission Modal -->
    <div class="modal fade" id="permEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border-0">
                    <h5 class="modal-title">Edit Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-24">
                    <form id="permEditForm" method="post">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" value="">
                        <div class="row">
                            <!-- Module (searchable + creatable) -->
                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Module <span class="text-danger">*</span></label>
                                <select id="editModule" name="module" class="form-control radius-8" required></select>
                                <div class="form-text">টাইপ করলেই লিস্ট, নতুন হলে Enter চাপলেই তৈরি হবে।</div>
                                <div class="invalid-feedback d-block module-error" style="display:none"></div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control radius-8" required>
                                <div class="invalid-feedback d-block name-error" style="display:none"></div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="form-label text-sm mb-8">Key <span class="text-danger">*</span></label>
                                <input type="text" name="key" class="form-control radius-8" required>
                                <div class="invalid-feedback d-block key-error" style="display:none"></div>
                            </div>

                            <div class="col-md-3 mb-20">
                                <label class="form-label text-sm mb-8">Sort</label>
                                <input type="number" name="sort" class="form-control radius-8" placeholder="0">
                                <div class="invalid-feedback d-block sort-error" style="display:none"></div>
                            </div>

                            <div class="col-md-3 mb-20">
                                <label class="form-label text-sm mb-8">Active?</label>
                                <!-- unchecked হলে 0 পাঠাতে hidden -->
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="editIsActive">
                                    <label class="form-check-label" for="editIsActive">Enable this permission</label>
                                </div>
                            </div>

                            <!-- Routes (multi-select + tags) -->
                            <div class="col-12 mb-10">
                                <label class="form-label text-sm mb-8">Routes</label>
                                <select id="editRoutes" name="routes[]" class="form-control radius-8"
                                    multiple="multiple"></select>
                                <div class="form-text">টাইপ করে সিলেক্ট করো বা নতুন লিখে Enter।</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
                            <button type="button"
                                class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- End Edit Permission Modal -->



    @endsection

    @section('script')
        <script>
            var DATATABLE_URL = "{{ route('rbac.permissions.list.ajax') }}";



            // route define for  submit
            const MODULES_URL = "{{ route('rbac.permissions.modules') }}";
            const ROUTES_SUGGEST_URL = "{{ route('rbac.permissions.routes.suggest') }}";
            const SHOW_URL = "{{ route('rbac.permissions.edit', ':id') }}";
            const UPDATE_URL = "{{ route('rbac.permissions.update', ':id') }}";



            const MODAL_ID = '#permCreateModal';
            const $modal = $(MODAL_ID);
            const $form = $('#permCreateForm');

            function initCreateModalSelects() {
                // Module: searchable + creatable (tags:true)
                $('#permModule').select2({
                    dropdownParent: $modal,
                    placeholder: 'Select or type a module',
                    tags: true,
                    allowClear: true,
                    ajax: {
                        url: MODULES_URL,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => ({
                            results: data?.results || []
                        })
                    },
                    width: '100%'
                });

                // Routes: multiple + tags + ajax suggest
                $('#permRoutes').select2({
                    dropdownParent: $modal,
                    placeholder: 'Type and select routes…',
                    tags: true, // unknown route allow (soft)
                    multiple: true,
                    allowClear: true,
                    ajax: {
                        url: ROUTES_SUGGEST_URL,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => ({
                            results: data?.results || []
                        })
                    },
                    width: '100%'
                });
            }

            // Modal open => init once + reset fields
            $modal.on('shown.bs.modal', function() {
                if (!$('#permModule').hasClass('select2-hidden-accessible')) {
                    initCreateModalSelects();
                }
                // reset
                $form[0].reset();
                $('#permModule').val(null).trigger('change');
                $('#permRoutes').val(null).trigger('change');
                $form.find('.module-error,.name-error,.key-error,.sort-error').hide().text('');
            });

            // Create submit (AJAX JSON প্রত্যাশিত)
            $(document).on('submit', '#permCreateForm', function(e) {
                e.preventDefault();
                const url = $form.attr('action');
                const data = $form.serialize(); // routes[] + module + others will be included

                $form.find('.module-error,.name-error,.key-error,.sort-error').hide().text('');

                $.ajax({
                    type: 'POST',
                    url,
                    data,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        // ARIA-safe: focus blur, then close & on hidden => reload DT
                        const modalEl = document.getElementById('permCreateModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

                        document.activeElement && document.activeElement.blur();

                        modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                            modalEl.removeEventListener('hidden.bs.modal', onHidden);

                            const dt = $('.AjaxDataTable').DataTable();
                            dt.ajax.reload(() => dt.page('first').draw('page'), false);

                            window.Swal && Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.msg || 'Permission created',
                                timer: 1200,
                                showConfirmButton: false
                            });

                            $form[0].reset();
                            $('#permModule, #permRoutes').val(null).trigger('change');
                        });

                        modal.hide();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errs = xhr.responseJSON?.errors || {};
                            if (errs.module) $form.find('.module-error').text(errs.module[0]).show();
                            if (errs.name) $form.find('.name-error').text(errs.name[0]).show();
                            if (errs.key) $form.find('.key-error').text(errs.key[0]).show();
                            if (errs.sort) $form.find('.sort-error').text(errs.sort[0]).show();
                        } else {
                            window.Swal && Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Something went wrong'
                            });
                        }
                    }
                });
            });

            // Edit permission


            const EDIT_MODAL_ID = '#permEditModal';
            const $editModal = $(EDIT_MODAL_ID);
            const $editForm = $('#permEditForm');

            // Select2 init (Module + Routes)
            function initEditSelects() {
                // Module: single + tags
                $('#editModule').select2({
                    dropdownParent: $editModal,
                    placeholder: 'Select or type a module',
                    tags: true,
                    allowClear: true,
                    ajax: {
                        url: MODULES_URL,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => ({
                            results: data?.results || []
                        })
                    },
                    width: '100%'
                });

                // Routes: multiple + tags + suggest
                $('#editRoutes').select2({
                    dropdownParent: $editModal,
                    placeholder: 'Type and select routes…',
                    tags: true,
                    multiple: true,
                    allowClear: true,
                    ajax: {
                        url: ROUTES_SUGGEST_URL,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => ({
                            results: data?.results || []
                        })
                    },
                    width: '100%'
                });
            }

            // Open Edit (Action button)
            $(document).on('click', '.btn-perm-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const url = SHOW_URL.replace(':id', id);

                // init selects once
                if (!$('#editModule').hasClass('select2-hidden-accessible')) {
                    initEditSelects();
                }

                // reset form + errors
                $editForm[0].reset();
                $editForm.find('.module-error,.name-error,.key-error,.sort-error').hide().text('');
                $('#editModule').val(null).trigger('change');
                $('#editRoutes').val(null).trigger('change');

                $.getJSON(url, function(res) {
                        // set form action (PUT)
                        const updateUrl = UPDATE_URL.replace(':id', res.id);
                        $editForm.attr('action', updateUrl);
                        $editForm.find('input[name="id"]').val(res.id);

                        // fill simple fields
                        $editForm.find('input[name="name"]').val(res.name || '');
                        $editForm.find('input[name="key"]').val(res.key || '');
                        $editForm.find('input[name="sort"]').val(res.sort ?? '');
                        $('#editIsActive').prop('checked', !!res.is_active);

                        // Module prefill (ensure option exists)
                        if (res.module) {
                            const opt = new Option(res.module, res.module, true, true);
                            $('#editModule').append(opt).trigger('change');
                        }

                        // Routes prefill (ensure options exist)
                        const routes = res.routes || [];
                        if (routes.length) {
                            const $routes = $('#editRoutes');
                            routes.forEach(r => {
                                const opt = new Option(r, r, true, true);
                                $routes.append(opt);
                            });
                            $routes.trigger('change');
                        }

                        // show modal
                        const modal = new bootstrap.Modal(document.getElementById('permEditModal'));
                        modal.show();
                    })
                    .fail(function() {
                        window.Swal && Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Could not load permission.'
                        });
                    });
            });

            // Submit Edit (PUT)
            $(document).on('submit', '#permEditForm', function(e) {
                e.preventDefault();
                const url = $editForm.attr('action');
                const data = $editForm.serialize();

                $editForm.find('.module-error,.name-error,.key-error,.sort-error').hide().text('');

                $.ajax({
                    type: 'POST', // method spoofed via _method=PUT
                    url,
                    data,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        const modalEl = document.getElementById('permEditModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

                        // ARIA-safe close
                        document.activeElement && document.activeElement.blur();
                        modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                            modalEl.removeEventListener('hidden.bs.modal', onHidden);

                            // reload current page (stay)
                            $('.AjaxDataTable').DataTable().ajax.reload(null, false);

                            window.Swal && Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: res.msg || 'Permission updated',
                                timer: 1100,
                                showConfirmButton: false
                            });
                        });

                        modal.hide();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errs = xhr.responseJSON?.errors || {};
                            if (errs.module) $editForm.find('.module-error').text(errs.module[0]).show();
                            if (errs.name) $editForm.find('.name-error').text(errs.name[0]).show();
                            if (errs.key) $editForm.find('.key-error').text(errs.key[0]).show();
                            if (errs.sort) $editForm.find('.sort-error').text(errs.sort[0]).show();
                        } else if (xhr.status === 403) {
                            window.Swal && Swal.fire({
                                icon: 'warning',
                                title: 'Forbidden',
                                text: xhr.responseJSON?.message || 'Permission denied'
                            });
                        } else {
                            window.Swal && Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Something went wrong'
                            });
                        }
                    }
                });
            });



            // Delete (Permissions)
            $(document).on('click', '.btn-perm-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const url = "{{ route('rbac.permissions.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Delete permission?',
                    text: 'All attached routes will be removed. This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it',
                }).then((res) => {
                    if (!res.isConfirmed) return;

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(resp) {
                            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: resp.msg || 'Permission deleted',
                                timer: 1100,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'In use',
                                    text: xhr.responseJSON?.msg ||
                                        'Permission is used by roles.'
                                });
                            } else if (xhr.status === 403) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Forbidden',
                                    text: xhr.responseJSON?.message || 'Not allowed.'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: 'Something went wrong'
                                });
                            }
                        }
                    });
                });
            });
        </script>
    @endsection
