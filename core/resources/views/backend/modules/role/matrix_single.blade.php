@extends('backend.layouts.master')
@section('meta') <title>Manage Permissions — {{ $role->name }}</title> @endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-16">
  <div>
    <h5 class="mb-0 mb-2">Manage Permissions</h5>
    <small class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">{{ $role->name }} {!! $role->is_super ? '<span class="badge bg-primary ms-1">Super</span>' : '' !!}</small>
  </div>
  <a href="{{ route('rbac.role.index') }}" class="btn btn-danger btn-sm">Bulk Matrix (advanced)</a>
</div>

@if($role->is_super)
  <div class="alert alert-info mb-16">This is a <strong>Super Role</strong>. All permissions are granted, editing is disabled.</div>
@endif

<form method="POST" action="{{ route('rbac.role.matrix.save', $role) }}" id="roleMatrixForm">
  @csrf
  <div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
      <div class="d-flex gap-2">
        <input type="search" id="permSearch" class="form-control form-control-sm w-auto" placeholder="Search permissions…">
        <select id="moduleFilter" class="form-select form-select-sm w-auto">
          <option value="">All modules</option>
          @foreach($permissions as $module => $_) <option value="{{ $module }}">{{ $module }}</option> @endforeach
        </select>
      </div>

      @unless($role->is_super)
      <div class="d-flex gap-2">
        {{-- Column presets --}}
        @foreach($abilities as $ab)
          <button type="button" class="btn btn-outline-secondary btn-sm js-col-toggle" data-col="{{ $ab }}">
            All {{ $labels[$ab] ?? ucfirst($ab) }}
          </button>
        @endforeach
      </div>
      @endunless
    </div>

    <div class="card-body shadow-lg" style="overflow:auto; max-height:70vh;">
      <table class="table table-bordered align-middle" id="matrixTable">
        <thead class="table-light sticky-top" >
          <tr>
            <th style="min-width:280px" class="bg-warning-50">Permission (Module → Name)</th>
            @foreach ($abilities as $ab)
              <th class="text-center bg-warning-50" style="width:110px">{{ $labels[$ab] ?? ucfirst($ab) }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
        @forelse ($permissions as $module => $rows)
          <tr class="table-secondary module-row"><td colspan="{{ 1 + count($abilities) }}"><strong>{{ $module }}</strong>
            @unless($role->is_super)
              <span class="float-end">
                <a href="#" class="small js-module-preset" data-module="{{ $module }}" data-preset="none">None</a> ·
                <a href="#" class="small js-module-preset" data-module="{{ $module }}" data-preset="read">Read</a> ·
                <a href="#" class="small js-module-preset" data-module="{{ $module }}" data-preset="editor">Editor</a> ·
                <a href="#" class="small js-module-preset" data-module="{{ $module }}" data-preset="manager">Manager</a> ·
                <a href="#" class="small js-module-preset" data-module="{{ $module }}" data-preset="exporter">Exporter</a>
              </span>
            @endunless
          </td></tr>

          @foreach ($rows as $p)
            <tr class="perm-row" data-module="{{ $module }}">
              <td>
                <small class="text-muted fw-bold">{{ $p->name }}</small>
                <div><code>{{ $p->key }}</code></div>
                
              </td>

              @foreach ($abilities as $ab)
                <td class="text-center">
                  @if ($role->is_super)
                    <em>All</em>
                  @else
                    {{-- unchecked হলেও 0 যাবে --}}
                    <input type="hidden" name="items[{{ $p->id }}][{{ $ab }}]" value="0">
                    <input type="checkbox"
                      class="form-check-input perm-toggle"
                      name="items[{{ $p->id }}][{{ $ab }}]"
                      value="1"
                      {{ (data_get($matrix, $p->id.'.'.$ab, 0)) ? 'checked' : '' }}
                      data-ab="{{ $ab }}">
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        @empty
          <tr><td colspan="{{ 1 + count($abilities) }}" class="text-center text-muted">No permissions.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
      <span id="unsavedBadge" class="badge bg-warning" style="display:none">Unsaved changes</span>
      <button class="btn  btn-success" {{ $role->is_super ? 'disabled' : '' }}>Save </button>
    </div>
  </div>
</form>
@endsection

@section('script')
<script>
  // Filter: search + module
  (function(){
    const $rows = $('#matrixTable .perm-row');
    $('#permSearch').on('input', function(){
      const q = this.value.toLowerCase();
      $rows.each(function(){
        const txt = $(this).text().toLowerCase();
        $(this).toggle(txt.indexOf(q) !== -1);
      });
    });
    $('#moduleFilter').on('change', function(){
      const mod = this.value;
      $rows.show();
      if (mod) $rows.not(`[data-module="${mod}"]`).hide();
    });
  })();

  // Dependency rule: edit/add/delete/export ⇒ view auto-on; view off ⇒ others off
  $(document).on('change', '.perm-toggle', function(){
    const $tr = $(this).closest('tr');
    const ab  = $(this).data('ab');
    const $view = $tr.find('input[name*="[view]"]');

    if (ab !== 'view' && this.checked) {
      $view.prop('checked', true);
    }
    if (ab === 'view' && !this.checked) {
      $tr.find('input.perm-toggle').not($view).prop('checked', false);
    }
    $('#unsavedBadge').show();
  });

  // Column toggles (bulk)
  $(document).on('click', '.js-col-toggle', function(e){
    e.preventDefault();
    const ab = $(this).data('col');
    const $checks = $(`input.perm-toggle[data-ab="${ab}"]`);
    const anyUnchecked = $checks.is(':not(:checked)');
    $checks.prop('checked', anyUnchecked);
    if (ab !== 'view' && anyUnchecked) {
      // ensure view on
      $checks.each(function(){
        const $row = $(this).closest('tr');
        $row.find('input[name*="[view]"]').prop('checked', true);
      });
    }
    $('#unsavedBadge').show();
  });

  // Module presets
  $(document).on('click', '.js-module-preset', function(e){
    e.preventDefault();
    const mod = $(this).data('module');
    const preset = $(this).data('preset');
    const $rows = $(`tr.perm-row[data-module="${mod}"]`);

    $rows.each(function(){
      const $row = $(this);
      const map = {
        none:    {view:0, add:0, edit:0, delete:0, export:0},
        read:    {view:1, add:0, edit:0, delete:0, export:0},
        editor:  {view:1, add:1, edit:1, delete:0, export:0},
        manager: {view:1, add:1, edit:1, delete:1, export:0},
        exporter:{view:1, add:0, edit:0, delete:0, export:1},
      }[preset] || {};
      Object.keys(map).forEach(ab=>{
        $row.find(`input.perm-toggle[data-ab="${ab}"]`).prop('checked', !!map[ab]);
      });
    });
    $('#unsavedBadge').show();
  });
</script>
@endsection
