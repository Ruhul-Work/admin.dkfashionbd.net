@extends('backend.layouts.master')

@section('meta')
  <title>Role Permission Matrix</title>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center p-3">
  <div>
    <h5>Role & Permission</h5>
    <p class="text-muted m-0">Tick abilities per role, per permission.</p>
  </div>
  @if (session('success'))
    <span class="badge bg-success">{{ session('success') }}</span>
  @endif
</div>

<form method="POST" action="{{ route('rbac.role.save') }}">
  @csrf
  <div class="p-3">
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th style="min-width:260px">Permission (Module → Name)</th>
              @foreach ($roles as $role)
                <th class="text-center">
                  {{ $role->name }}
                  @if ($role->is_super)
                    <span class="badge bg-primary ms-1">Super</span>
                  @endif
                </th>
              @endforeach
            </tr>
          </thead>

          <tbody>
          @forelse ($permissions as $module => $rows)
            <tr class="table-light">
              <td colspan="{{ 1 + $roles->count() }}"><strong>{{ $module }}</strong></td>
            </tr>

            @foreach ($rows as $p)
              <tr>
                <td>
                  <div><code>{{ $p->key }}</code></div>
                  <small class="text-muted">{{ $p->name }}</small>
                </td>

                @foreach ($roles as $role)
                  <td class="text-center">
                    @if ($role->is_super)
                      <em>All</em>
                    @else
                      <div class="d-flex justify-content-center gap-2 flex-wrap">
                        @foreach ($abilities as $ab)
                          {{-- unchecked হলেও 0 যাবে --}}
                          <input type="hidden"
                                 name="items[{{ $role->id }}][{{ $p->id }}][{{ $ab }}]"
                                 value="0">

                          <label class="m-0">
                            <input type="checkbox" class="form-check-input my-1"
                              name="items[{{ $role->id }}][{{ $p->id }}][{{ $ab }}]"
                              value="1"
                              {{-- pre-check: matrix[roleId][permissionId][ability] --}}
                              {{ data_get($matrix, $role->id.'.'.$p->id.'.'.$ab) ? 'checked' : '' }}>
                            <span class="form-check-label mx-2">{{ $labels[$ab] ?? ucfirst($ab) }}</span>
                          </label>
                        @endforeach
                      </div>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach

          @empty
            <tr>
              <td colspan="{{ 1 + $roles->count() }}" class="text-center text-muted">
                No permissions found.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-primary">Save Matrix</button>
      </div>
    </div>
  </div>
</form>
@endsection
