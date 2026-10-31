<div data-modal-key="userEdit">
    <div class="modal-header py-16 px-24 border-0">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body p-24">
        <form id="userEditForm" data-ajax="true" method="post"
            action="{{ route('usermanage.users.update', $user->id)}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $user->id }}">

            <div class="row">
                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="{{ $user->name }}"
                        required>
                    <div class="invalid-feedback d-block name-error" style="display:none"></div>
                </div>
                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Email</label>
                    <input type="email" name="email" class="form-control radius-8" value="{{ $user->email }}">
                    <div class="invalid-feedback d-block email-error" style="display:none"></div>
                </div>

                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Username</label>
                    <input type="text" name="username" class="form-control radius-8" value="{{ $user->username }}">
                    <div class="invalid-feedback d-block username-error" style="display:none"></div>
                </div>
                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Phone</label>
                    <input type="text" name="phone" class="form-control radius-8" value="{{ $user->phone }}">
                    <div class="invalid-feedback d-block phone-error" style="display:none"></div>
                </div>

                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">New Password</label>
                    <input type="password" name="password" class="form-control radius-8"
                        placeholder="Leave blank to keep">
                    <div class="invalid-feedback d-block password-error" style="display:none"></div>
                </div>
                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control radius-8">
                </div>

                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Role <span class="text-danger">*</span></label>
                    <select id="editRole" name="role_id" class="form-control radius-8 js-role-select" required>
                        @if ($user->role_id)
                            <option value="{{ $user->role_id }}" selected>{{ $user->role->name ?? 'Role' }}</option>
                        @endif
                    </select>
                    <div class="invalid-feedback d-block role-error role_id-error" style="display:none"></div>
                </div>
                {{-- 
        <div class="col-md-3 mb-16">
          <label class="form-label text-sm mb-6">Branch</label>
          <input type="number" name="branch_id" class="form-control radius-8" value="{{ $user->branch_id }}">
          <div class="invalid-feedback d-block branch-error" style="display:none"></div>
        </div> --}}

                <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Branch</label>
                    <select id="editBranch" name="branch_id" class="form-control radius-8 js-branch-select">
                        @if ($user->branch_id)
                            <option value="{{ $user->branch_id }}" selected>
                                {{ optional($user->branch)->name ?? '#' . $user->branch_id }}
                            </option>
                        @endif
                    </select>
                    <div class="invalid-feedback d-block branch_id-error" style="display:none"></div>
                </div>

                 <div class="col-md-6 mb-16">
                    <label class="form-label text-sm mb-6">Image</label>
                    <input type="file" name="image" value="{{image($user->image)}}" class="form-control radius-8"
                        placeholder="Leave blank to keep">
                    <div class="invalid-feedback d-block password-error" style="display:none"></div>
                </div>

                <div class="col-md-3 mb-16">
                    <label class="form-label text-sm mb-6">Status</label>
                    <select name="status" class="form-select radius-8">
                        <option value="1" @selected($user->status == 1)>Active</option>
                        <option value="0" @selected($user->status == 0)>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-center gap-3 mt-12">
                <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
            </div>
        </form>
    </div>
</div>
