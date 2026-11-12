@extends('backend.layouts.master')

@section('meta')
    <title>Create Employee</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Employee Management</h6>
            <p class="fw-semibold mb-0">Create new Employee</p>
        </div>
        <div class="text-end w-100">
            <a href="{{ route('employee.employees.index') }}" class="btn btn-secondary bg-dark"><i data-feather="arrow-left" class="me-2"></i>Back to Employees</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="employee_form" method="post" action="{{ route('employee.employees.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="star-sign">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control" name="emp_id">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                  
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Joining Date</label>
                            <input type="date" class="form-control" name="joining_date">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Blood Group</label>
                            <select class="form-select" name="blood_group">
                                <option value="">Select</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}">{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                      <div class="col-lg-12 col-sm-12 col-12">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $('#employee_form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('employee.employees.index') }}';
                        }
                    });
                },
                error: function(xhr) {
                    var responseObj = xhr.responseJSON;
                    var errorMessages = responseObj && responseObj.errors
                        ? Object.values(responseObj.errors).flat()
                        : [responseObj && responseObj.message ? responseObj.message : 'An error occurred'];
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages.join('<br>')
                    });
                }
            });
        });
    </script>
@endsection