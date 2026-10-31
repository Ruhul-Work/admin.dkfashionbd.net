@extends('backend.layouts.master')

@section('meta')
  <title>User Permission Overrides</title>
@endsection

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
      <div>
        <h6 class="fw-semibold mb-0">User Wise Permission </h6>
        <p class="text-muted m-0">Set per-user allow/deny. “Inherit” means use role baseline.</p>
      </div>

      <ul class="d-flex align-items-center gap-2 mb-0">
        <li class="fw-medium">
          <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
            Dashboard
          </a>
        </li>
        <li>-</li>
        <li class="fw-medium">User Wise Permission</li>
      </ul>

      <div class="w-100"></div>
  <div class="text-end w-100">
    @if (session('success'))
      <span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">{{ session('success') }}</span>
    @endif
    <span class="badge bg-light text-dark text-md"># {{ $user->id }}</span>
    <span class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">{{ $user->name }}</span>
  </div>
      {{-- Button on a new line (right aligned) --}}
      <div class="text-end w-100">
        <a href="{{ route('usermanage.users.index') }}" class="btn btn-sm btn-danger mt-2">
          Back to Users
        </a>
      </div>
    </div>
{{-- End Breadcrumb/Header --}}


<form method="POST" action="{{ route('usermanage.userspermission.update', $enc) }}">
  @csrf

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th style="min-width:260px">Permission (Module → Name)</th>
              @foreach ($abilities as $ab)
                <th class="text-center">{{ $labels[$ab] ?? ucfirst($ab) }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
          @forelse ($permissions as $module => $rows)
            <tr class="table-light">
              <td colspan="{{ 1 + count($abilities) }}"><strong>{{ $module }}</strong></td>
            </tr>
            @foreach ($rows as $p)
                @php
                  $o = $overrides[$p->id] ?? null;
                  $current = [
                    'view'   => $o->can_view   ?? null,
                    'add'    => $o->can_add    ?? null,
                    'edit'   => $o->can_edit   ?? null,
                    'delete' => $o->can_delete ?? null,
                    'export' => $o->can_export ?? null,
                  ];
                  $base = $roleMatrix[$p->id] ?? null; 
                  $colMap = ['view'=>'can_view','add'=>'can_add','edit'=>'can_edit','delete'=>'can_delete','export'=>'can_export'];
                @endphp

                <tr>
                  <td>
                    <div><code>{{ $p->key }}</code></div>
                    <small class="text-muted">{{ $p->name }}</small>
                  </td>

              @foreach ($abilities as $ab)
                @php
                  $col = $colMap[$ab];
                  $roleAllows = $isTargetSuper ? true : (bool) (($roleMatrix[$p->id]->$col ?? 0));
                  $override   = $current[$ab];      
                  $effective  = is_null($override) ? $roleAllows : (bool)$override;
                @endphp

                <td class="text-center">
                  @if ($isTargetSuper)
                    <span class="badge rounded-pill bg-primary px-3 py-2">Super: All</span>
                  @else
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                      {{-- override select --}}
                      <select class="form-select form-select-sm w-auto override-select"
                              name="items[{{ $p->id }}][{{ $ab }}]"
                              data-role-allow="{{ $roleAllows ? 1 : 0 }}">
                        <option value=""  {{ $override === null ? 'selected' : '' }}>Inherit</option>
                        <option value="1" {{ $override === 1    ? 'selected' : '' }}>Allow</option>
                        <option value="0" {{ $override === 0    ? 'selected' : '' }}>Deny</option>
                      </select>

                      {{-- chips: Role / Effect --}}
                      <div class="d-flex flex-column align-items-start gap-1 text-nowrap">
                        <span class="badge {{ $roleAllows ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} chip-role">
                          Role: {{ $roleAllows ? 'Allow' : 'Deny' }}
                        </span>
                        <span class="badge {{ $effective ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} chip-effect">
                          Effect: {{ $effective ? 'Allow' : 'Deny' }}
                        </span>
                      </div>
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
            @endforeach
          @empty
            <tr><td colspan="{{ 1 + count($abilities) }}" class="text-center text-muted">No permissions found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-primary">Save Overrides</button>
      </div>
    </div>

</form>
@endsection
@section('script')

  @if (session('success'))
  <script>
  Swal.fire({
    icon: 'success',
    title: 'Success',
    text: @json(session('success')), // safe encode
    confirmButtonText: 'OK'
  });
  </script>
  @endif

  @if (session('error'))
  <script>
  Swal.fire({
    icon: 'error',
    title: 'Oops!',
    text: @json(session('error'))
  });
  </script>
  @endif

@endsection