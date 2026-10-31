<form data-ajax="true" method="POST" action="{{ route('business-journals.store') }}">
    @csrf

    <div class="modal-header">
        <h6 class="modal-title d-flex align-items-center gap-2">
            <iconify-icon icon="mdi:notebook-plus-outline" class="text-primary text-xl"></iconify-icon>
            Create New Journal
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body p-24">

        {{-- Date --}}
        <div class="mb-16">
            <label class="form-label fw-semibold" for="bj_date">
                Date <span class="text-danger">*</span>
            </label>
            <input type="date" id="bj_date" name="date"
                   class="form-control form-control-sm"
                   value="{{ now()->toDateString() }}" required>
            <div class="invalid-feedback d-block date-error text-danger small"></div>
        </div>

        {{-- Journal Name --}}
        <div class="mb-16">
            <label class="form-label fw-semibold" for="bj_name">
                Journal Name <span class="text-danger">*</span>
            </label>
            <input type="text" id="bj_name" name="name"
                   class="form-control form-control-sm"
                   placeholder="e.g. Partner Capital Q1" required maxlength="255">
            <div class="invalid-feedback d-block name-error text-danger small"></div>
        </div>

        {{-- Journal Category --}}
        <div class="mb-16">
            <label class="form-label fw-semibold" for="bj_category">
                Journal Category
            </label>
            <select id="bj_category" name="category" class="form-select form-select-sm">
                <option value="">Select journal category</option>
                <option value="Owner Investment (Capital Introduced)">Owner Investment (Capital Introduced)</option>
                <option value="Drawings">Drawings</option>
                <option value="Borrowings">Borrowings</option>
                <option value="Income">Income</option>
                <option value="Expense">Expense</option>
                <option value="Bank">Bank</option>
                <option value="Cash">Cash</option>
            </select>
            <div class="invalid-feedback d-block category-error text-danger small"></div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" data-bs-dismiss="modal">
            <iconify-icon icon="mdi:close"></iconify-icon>Close
        </button>
        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
            Save Changes
        </button>
    </div>

</form>
