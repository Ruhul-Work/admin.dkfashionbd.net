@extends('backend.layouts.master')

@section('meta')
    <title>Employees</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Employee Management</h6>
            <p class="text-muted m-0">Manage employees</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Employees</li>
        </ul>
    </div>

    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Employees</h5>

            <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>

                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>

                <a href="{{ route('employee.employees.create') }}" class="d-flex btn btn-primary btn-sm px-12 py-8 radius-8">
                    <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                    Add Employee
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="employeesTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label">S.L</label>
                            </div>
                        </th>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Joining Date</th>
                        <th>Blood Group</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var DATATABLE_URL = "{{ route('employee.employees.list.ajax') }}";

        $(document).on('click', '.btn-employee-delete', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            const doDelete = () => {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                        if (window.Swal) Swal.fire({
                            icon: 'success',
                            title: res?.message || 'Deleted',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        if (window.Swal) Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Delete failed'
                        });
                    }
                });
            };

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete employee?',
                    text: 'This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete'
                }).then(r => {
                    if (r.isConfirmed) doDelete();
                });
            } else {
                if (confirm('Delete employee?')) doDelete();
            }
        });
    </script>
@endsection