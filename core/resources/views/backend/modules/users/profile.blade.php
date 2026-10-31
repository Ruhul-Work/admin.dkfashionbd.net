@extends('backend.layouts.master')
@section('meta')
    <title>User Profile</title>
@endsection

@section('content')
    {{-- Breadcrumb/Header --}}
    
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">User Profile</h6>
            <p class="text-muted m-0">View and edit user information.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">User Profile</li>
        </ul>
    </div>


    <div class="card col-lg-8">
        <div class="card-body table-responsive">
            <h5 class="mb-4">Profile Information</h5>
            <div class="row">
                <div class="col-lg-4">

                    <label class="form-label " for="">Name</label>
                    <input class="form-control " type="name" disabled value="{{ ucwords($user->name ?? 'N/A') }}">
                </div>
                <div class="col-lg-4">

                    <label class="form-label " for="">Email</label>
                    <input class="form-control " type="name" disabled value="{{ $user->email ?? 'N/A' }}">
                </div>
                <div class="col-lg-4">

                    <label class="form-label " for="">Phone</label>
                    <input class="form-control " type="name" disabled value="{{ $user->phone ?? 'N/A' }}">
                </div>
                <div class="col-lg-4">

                    <label class="form-label p-1 mt-1" for="">Role</label>
                    <input class="form-control " type="name" disabled value="{{ $user->role->name ?? 'N/A' }}">
                </div>

                <div class="col-lg-8 row mt-3 mx-1 mb-3">
                    <label class="form-lable p-1" for="">Profile Image</label>
                    <img src="{{ image($user->image) }}" alt="Profile Image" class="img-fluid w-25 rounded" />
                </div>
                <div class="">
                    <p class=""><strong>Status:</strong> {{ $user->status == 1 ? 'Active' : 'Inactive' }}</p>
                </div>



            </div>
        </div>


        <div class="card-footer d-flex justify-content-end">

            <a href="{{ route('usermanage.users.index') }}" class="btn btn-dark me-3">Back to Users</a>

            <a href="#"class="btn btn-info AjaxModal"
                data-ajax-modal="{{ route('usermanage.users.edit.modal', $user->id) }}" data-size="lg"
                data-onload="UserProfile.onLoad" data-onsuccess="UserProfile.onSaved">
                Edit Profile
            </a>

        </div>
    </div>
@endsection

@section('script')
    <script>
        window.UserProfile = {
            onLoad($modal) {
                // এই পেজের জন্য Select2 init (roles)
                $modal.find('.js-select2').each(function() {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) return;
                    $el.select2({
                        dropdownParent: $modal,
                        width: '100%',
                        placeholder: 'Select...',
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
            },
            onSaved(res) {
                // Profile UI live update
                const u = res?.data || {};
                $('[data-profile-name]').text(u.name || '');
                $('[data-profile-email]').text(u.email || '');
                $('[data-profile-phone]').text(u.phone || '');
                $('[data-profile-role]').text(u.role || 'N/A');
                $('[data-profile-status]').text(String(u.status) === '1' ? 'Active' : 'Inactive');
            }
        };
    </script>
@endsection
