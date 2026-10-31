<div class="modal-header">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="{{ $action }}" method="post"
      class="AjaxForm"  data-ajax="true"            
      data-onsuccess="SizesIndex.onSaved">
  @csrf
  @if($method === 'PUT') @method('PUT') @endif

  <div class="modal-body">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" required maxlength="255" value="{{ $size->name ?? '' }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Code</label>
      <input name="code" class="form-control" required maxlength="32" value="{{ $size->code ?? '' }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Sort (optional)</label>
      <input name="sort" type="number" min="0" max="65535" value="{{ $size->sort ?? 0 }}" class="form-control">
    </div>

    <div class="col-12 mb-8">
        <label class="form-label text-sm mb-8">Active?</label>
        <div class="form-switch switch-purple d-flex align-items-center gap-3">
          <input type="hidden" name="is_active" value="0">
           <input class="form-check-input my-1" type="checkbox" name="is_active" value="1" id="is_active"
                {{ ($size->is_active ?? true) ? 'checked' : '' }}>
          <label class="form-check-label" for="branchIsActive">Enable this size</label>
        </div>
        <div class="invalid-feedback d-block is_active-error" style="display:none"></div>
      </div>
    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Save</button>
  </div>
</form>
