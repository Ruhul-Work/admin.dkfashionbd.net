@extends('backend.layouts.master')

@section('meta')
    <title>Users List</title>
@endsection

@section('content')
    {{-- Breadcrumb/Header (আপনার থিমের স্টাইল) --}}
    <div class=" d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Users List</h6>
            <p class="text-muted m-0">All User</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">User List</li>
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
                @perm('usermanage.users.store')
                    <button type="button" class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#userCreateModal">
                        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                        Add user
                    </button>
                @endperm
            </div>
        </div>
        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="usersTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    </div>


    <!-- Create User Modal -->
    <div class="modal fade" id="userCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border-0">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-24">
                    <form id="userCreateForm" action="{{ route('usermanage.users.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control radius-8" required>
                                <div class="invalid-feedback d-block name-error" style="display:none"></div>
                            </div>
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Email</label>
                                <input type="email" name="email" class="form-control radius-8"
                                    placeholder="user@example.com">
                                <div class="invalid-feedback d-block email-error" style="display:none"></div>
                            </div>
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Username</label>
                                <input type="text" name="username" class="form-control radius-8" placeholder="username">
                                <div class="invalid-feedback d-block username-error" style="display:none"></div>
                            </div>
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Phone</label>
                                <input type="text" name="phone" class="form-control radius-8"
                                    placeholder="01XXXXXXXXX">
                                <div class="invalid-feedback d-block phone-error" style="display:none"></div>
                            </div>

                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control radius-8" required>
                                <div class="invalid-feedback d-block password-error" style="display:none"></div>
                            </div>
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control radius-8" required>
                            </div>

                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Role <span class="text-danger">*</span></label>
                                <select id="createRole" name="role_id" class="form-control radius-8" required></select>
                                <div class="invalid-feedback d-block role-error" style="display:none"></div>
                            </div>

                            {{-- <div class="col-md-3 mb-16">
                                <label class="form-label text-sm mb-6">Branch</label>
                                <input type="number" name="branch_id" class="form-control radius-8"
                                    placeholder="Branch ID">
                                <div class="invalid-feedback d-block branch-error" style="display:none"></div>
                            </div> --}}
                            <div class="col-md-6 mb-16">
                                <label class="form-label text-sm mb-6">Branch</label>
                                <select id="createBranch" name="branch_id" class="form-control radius-8"></select>
                                <div class="invalid-feedback d-block branch_id-error" style="display:none"></div>
                            </div>


                            <div class="col-md-3 mb-16">
                                <label class="form-label text-sm mb-6">Status</label>
                                <select name="status" class="form-select radius-8">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-3 mt-12">
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
    {{-- end add modal --}}


@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('usermanage.users.list.ajax') }}";

        // Shared endpoints 
        
        const USER_UPDATE_URL = @json(route('usermanage.users.update', ['user' => '__ID__']));
        const STORE_URL = "{{ route('usermanage.users.store') }}";
        
        const ROLES_URL = "{{ route('usermanage.users.roles') }}";
        // --- Select2 init helpers
        function initRoleSelect($el, $modal) {
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2({
                dropdownParent: $modal,
                width: '100%',
                placeholder: 'Select a role',
                allowClear: true,
                ajax: {
                    url: ROLES_URL,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term || ''
                    }),
                    processResults: data => ({
                        results: data?.results || []
                    })
                }
            });
        }



        function initBranchSelect($el, $modal) {
            $el.select2({
                dropdownParent: $modal,
                placeholder: 'Select branch',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('org.branches.select2') }}",
                    dataType: 'json',
                    delay: 200,
                    data: params => ({
                        q: params.term || ''
                    }),
                    processResults: data => data
                }
            });
        }

        const $createModal = $('#userCreateModal');
        const $createForm = $('#userCreateForm');

        $createModal.on('shown.bs.modal', function() {
            // reset
            $createForm[0].reset();

            // clear errors (সব জায়গায় এক নাম)
            $createForm.find(
                '.name-error,.email-error,.username-error,.phone-error,.password-error,.role-error,.branch_id-error'
            ).hide().text('');

            // init/select2
            const $m = $(this);
            initRoleSelect($('#createRole'), $m);
            $('#createRole').val(null).trigger('change');

            initBranchSelect($('#createBranch'), $m);
            $('#createBranch').val(null).trigger('change');
        });

        // Submit
        $(document).on('submit', '#userCreateForm', function(e) {
            e.preventDefault();

            const url = (typeof STORE_URL !== 'undefined') ? STORE_URL : "{{ route('usermanage.users.store') }}";
            const data = $createForm.serialize();

            $.ajax({
                    type: 'POST',
                    url,
                    data,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .done(function(res) {
                    const el = document.getElementById('userCreateModal');
                    const inst = bootstrap.Modal.getOrCreateInstance(el);
                    document.activeElement && document.activeElement.blur();
                    el.addEventListener('hidden.bs.modal', function onH() {
                        el.removeEventListener('hidden.bs.modal', onH);
                        if ($.fn.dataTable && $('.AjaxDataTable').length) {
                            $('.AjaxDataTable').DataTable().ajax.reload(() => {
                                $('.AjaxDataTable').DataTable().page('first').draw('page');
                            }, false);
                        }
                        window.Swal && Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.msg || 'User created',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    });
                    inst.hide();
                })
                .fail(function(xhr) {
                    if (xhr.status === 422) {
                        const errs = xhr.responseJSON?.errors || {};
                        for (const k in errs) {
                            $createForm.find(`.${k}-error`).text(errs[k][0]).show();
                            const $f = $createForm.find(`[name="${k}"]`);
                            if ($f.length) {
                                $f.addClass('is-invalid');
                                let $fb = $f.siblings('.invalid-feedback');
                                if (!$fb.length) {
                                    $fb = $('<div class="invalid-feedback"></div>');
                                    $f.after($fb);
                                }
                                $fb.text(errs[k][0]).show();
                            }
                        }
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
                });
        });


        // --- Create Modal
        // const $createModal = $('#userCreateModal');
        // const $createForm = $('#userCreateForm');

        // $createModal.on('shown.bs.modal', function() {
        //     $createForm[0].reset();
        //     initRoleSelect($('#createRole'), $createModal);
        //     $('#createRole').val(null).trigger('change');
        //     $createForm.find(
        //         '.name-error,.email-error,.username-error,.phone-error,.password-error,.role-error,.branch-error'
        //         ).hide().text('');
        // });

        // $(document).on('submit', '#userCreateForm', function(e) {
        //     e.preventDefault();
        //     const url = STORE_URL;
        //     const data = $createForm.serialize();

        //     $.ajax({
        //         type: 'POST',
        //         url,
        //         data,
        //         dataType: 'json',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         success: function(res) {
        //             const el = document.getElementById('userCreateModal');
        //             const inst = bootstrap.Modal.getOrCreateInstance(el);
        //             document.activeElement && document.activeElement.blur();
        //             el.addEventListener('hidden.bs.modal', function onH() {
        //                 el.removeEventListener('hidden.bs.modal', onH);
        //                 $('.AjaxDataTable').DataTable().ajax.reload(() => $('.AjaxDataTable')
        //                     .DataTable().page('first').draw('page'), false);
        //                 Swal && Swal.fire({
        //                     icon: 'success',
        //                     title: 'Success',
        //                     text: res.msg || 'User created',
        //                     timer: 1200,
        //                     showConfirmButton: false
        //                 });
        //             });
        //             inst.hide();
        //         },
        //         error: function(xhr) {
        //             if (xhr.status === 422) {
        //                 const errs = xhr.responseJSON?.errors || {};
        //                 for (const k in errs) $createForm.find(`.${k}-error`).text(errs[k][0]).show();
        //             } else if (xhr.status === 403) {
        //                 Swal && Swal.fire({
        //                     icon: 'warning',
        //                     title: 'Forbidden',
        //                     text: xhr.responseJSON?.message || 'Permission denied'
        //                 });
        //             } else {
        //                 Swal && Swal.fire({
        //                     icon: 'error',
        //                     title: 'Failed',
        //                     text: 'Something went wrong'
        //                 });
        //             }
        //         }
        //     });
        // });



        window.UsersIndex = {
            onLoad($modal) {
                // Role select
                $modal.find('.js-role-select').each(function() {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) return;
                    $el.select2({
                        dropdownParent: $modal,
                        width: '100%',
                        placeholder: 'Select role',
                        allowClear: true,
                        ajax: {
                            url: "{{ route('usermanage.users.roles') }}",
                            dataType: 'json',
                            delay: 250,
                            data: params => ({
                                q: params.term || ''
                            }),
                            processResults: data => ({
                                results: data?.results || []
                            })
                        }
                    });
                });

                // Branch select
                $modal.find('.js-branch-select').each(function() {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) return;
                    $el.select2({
                        dropdownParent: $modal,
                        width: '100%',
                        placeholder: 'Select branch',
                        allowClear: true,
                        ajax: {
                            url: "{{ route('org.branches.select2') }}", // <- আগেরটাই
                            dataType: 'json',
                            delay: 250,
                            data: params => ({
                                q: params.term || ''
                            }),
                            processResults: data => data // {results:[{id,text}]}
                        }
                    });
                });
            },

            onSaved(res) {
                if ($('.AjaxDataTable').length && $.fn.DataTable) {
                    $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: res?.msg || 'Saved',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            }

        };

        // Delete user
        $(document).on('click', '.btn-user-delete', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = "{{ route('usermanage.users.destroy', ':id') }}".replace(':id', id);

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
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: res.msg || 'Role deleted',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Forbidden',
                                    text: xhr.responseJSON?.msg || 'Not allowed'
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
                }
            });
        });
    </script>
@endsection
