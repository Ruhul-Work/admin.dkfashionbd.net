@extends('backend.layouts.master')

@section('meta')
    <title>Firewall Rules</title>
@endsection

@section('content')

    {{-- Breadcrumb/Header --}}
    <div class=" d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Firewall</h6>
            <p class="text-muted m-0">Allow/Block IPs & manage rules</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href={{ route('backend.dashboard') }} class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Firewall List</li>
        </ul>
    </div>
    {{-- End Breadcrumb/Header --}}

    <div class="card basic-data-table">
        {{-- card header --}}
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
                <!-- Add New Button -->
                @perm('security.firewall.store')
                    <button type="button" class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#fwCreateModal">
                        <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                        Add Rule
                    </button>
                @endperm
            </div>
        </div>
        {{-- End card header --}}

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="fwTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                        <th>IP Address</th>
                        <th>Type</th>
                        <th>Comments</th>
                        <th>Created</th>
                        <th style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="fwCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border-0">
                    <h5 class="modal-title">Add Firewall Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form id="fwCreateForm" action="{{ route('security.firewall.store') }}" method="post">
                        @csrf
                        <div class="mb-16">
                            <label class="form-label text-sm mb-6">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" class="form-control radius-8"
                                placeholder="e.g. 203.0.113.10" required>
                            <div class="invalid-feedback d-block ip-error" style="display:none"></div>
                        </div>
                        <div class="mb-16">
                            <label class="form-label text-sm mb-6">Type</label>
                            <select name="type" class="form-select radius-8">
                                <option value="block">Block</option>
                                <option value="allow">Allow</option>
                            </select>
                        </div>
                        <div class="mb-16">
                            <label class="form-label text-sm mb-6">Comments</label>
                            <input type="text" name="comments" class="form-control radius-8" placeholder="(optional)">
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3 mt-12">
                            <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('security.firewall.list.ajax') }}";
        
        // Allow/Block টগল
        $(document).on('click', '.btn-fw-toggle', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $.post("{{ route('security.firewall.toggle', ':id') }}".replace(':id', id))
                .done(function() {
                    $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                })
                .fail(function() {
                    Swal && Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Toggle failed'
                    });
                });
        });

        // Delete
        $(document).on('click', '.btn-fw-delete', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = "{{ route('security.firewall.destroy', ':id') }}".replace(':id', id);

            const go = () => $.ajax({
                    type: 'POST',
                    url,
                    data: {
                        _method: 'DELETE'
                    }
                })
                .done(function() {
                    $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                    Swal && Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        timer: 900,
                        showConfirmButton: false
                    });
                })
                .fail(function() {
                    Swal && Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Delete failed'
                    });
                });

            if (window.Swal) {
                Swal.fire({
                        icon: 'warning',
                        title: 'Delete this rule?',
                        showCancelButton: true,
                        confirmButtonText: 'Delete'
                    })
                    .then(x => x.isConfirmed && go());
            } else {
                if (confirm('Delete this rule?')) go();
            }
        });

        // Create submit
        $(document).on('submit', '#fwCreateForm', function(e) {
            e.preventDefault();
            const $f = $(this);
            $f.find('.ip-error').hide().text('');
            $.post($f.attr('action'), $f.serialize())
                .done(function(res) {
                    $('#fwCreateModal').modal('hide');
                    $f[0].reset();
                    $('#fwTable').DataTable().ajax.reload(null, false);
                    Swal && Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        timer: 1000,
                        showConfirmButton: false
                    });
                })
                .fail(function(xhr) {
                    if (xhr.status === 422) {
                        const errs = xhr.responseJSON?.errors || {};
                        if (errs.ip_address) $f.find('.ip-error').text(errs.ip_address[0]).show();
                    } else {
                        Swal && Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Something went wrong'
                        });
                    }
                });
        });
    </script>
@endsection
