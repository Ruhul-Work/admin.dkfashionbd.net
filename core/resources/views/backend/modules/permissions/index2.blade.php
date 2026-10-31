@extends('backend.layouts.master')

@section('meta')
  <title>Permissions </title>
@endsection

@section('content')

  {{-- Breadcrumb/Header (আপনার থিমের স্টাইল) --}}
  <div class=" d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
  <div>
    <h6 class="fw-semibold mb-0">Permissions List</h6>
    <p class="text-muted m-0">Register keys & attach routes</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Permissions List</li>
    </ul>
  </div>
  {{-- End Breadcrumb/Header --}}
<div class="p-3 mb-3 bg-white rounded shadow-sm">
  @perm('rbac.permissions.store','add')
  <form action="{{ route('rbac.permissions.store') }}" method="POST" class="d-flex gap-2">
    @csrf
    <input name="module" class="form-control" placeholder="Module (e.g. User Management)" required>
    <input name="name" class="form-control" placeholder="Name (e.g. Users)" required>
    <input name="key" class="form-control" placeholder="Key (e.g. usermanage.users)" required>
    <button class="btn btn-primary">Add</button>
  </form>
  @endperm
</div>
<div class="p-3">
<div class="card">
  <div class="card-body">
    <table class="table">
      <thead>
        <tr>
          <th>Module</th><th>Name</th><th>Key (prefix)</th><th>Routes</th><th>Attach</th>
        </tr>
      </thead>
      <tbody>
        @forelse($permissions as $p)
        <tr>
          <td>{{ $p->module }}</td>
          <td>{{ $p->name }}</td>
          <td><code>{{ $p->key }}</code></td>
          <td style="max-width:420px">
            @foreach($p->routes as $r)
              <span class="badge bg-light text-dark d-inline-flex align-items-center mb-1">
                {{ $r->route_name }}
                @perm('rbac.permissions.routes.detach','delete')
                <form action="{{ route('rbac.permissions.routes.detach', [$p->id,$r->route_name]) }}"
                      method="POST" class="ms-2">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">x</button>
                </form>
                @endperm
              </span>
            @endforeach
          </td>
          <td>
            @perm('rbac.permissions.routes.attach','edit')
            <form action="{{ route('rbac.permissions.routes.attach',$p->id) }}" method="POST" class="d-flex gap-2">
              @csrf
              <input name="route_name" class="form-control" placeholder="route.name" required>
              <button class="btn btn-outline-primary">Attach</button>
            </form>
            @endperm
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No permissions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  </div>
</div>
@endsection
