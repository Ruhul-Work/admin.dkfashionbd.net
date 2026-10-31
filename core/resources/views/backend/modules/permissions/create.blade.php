@extends('backend.layouts.master')

@section('meta')
  <title>Add Permission</title>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center p-3">
  <div>
    <h5>Add Permission</h5>
    <p class="text-muted m-0">Register a permission key</p>
  </div>
  <a href="{{ route('rbac.permissions.index') }}" class="btn btn-sm btn-secondary">Back to Permissions</a>
</div>

<div class="p-3">
  <div class="card">
    <form action="{{ route('rbac.permissions.store') }}" method="POST">
      @csrf
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="row g-3">
          {{-- Row 1 --}}
          <div class="col-12 col-md-4">
            <label class="form-label">Module <span class="text-danger">*</span></label>
            <input type="text" name="module" class="form-control" value="{{ old('module') }}" placeholder="e.g. User Management" required>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Users" required>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Key (prefix) <span class="text-danger">*</span></label>
            <input type="text" name="key" id="perm_key" class="form-control" value="{{ old('key') }}" placeholder="e.g. usermanage.users" required>
          </div>

          {{-- Row 2 --}}
          <div class="col-12 col-md-4">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
              <option value="route"  {{ old('type','route')==='route' ? 'selected' : '' }}>Route</option>
              <option value="feature"{{ old('type')==='feature' ? 'selected' : '' }}>Feature</option>
            </select>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Sort</label>
            <input type="number" name="sort" class="form-control" value="{{ old('sort', 0) }}">
          </div>

          <div class="col-12 col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                     {{ old('is_active', 1) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>

          {{-- Row 3 --}}
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Optional">{{ old('description') }}</textarea>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-primary">Create Permission</button>
      </div>
    </form>
  </div>
</div>
@endsection
