<div class="modal-header py-16 px-24 border-0" data-modal-key="loyalty-create">
    <h5 class="modal-title">Add Loyalty Rule</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
    <form id="loyaltyCreateForm" action="{{ route('loyalty.store') }}" method="post" data-ajax="true">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control radius-8" placeholder="e.g. Black Friday Offer"
                    required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Earn Amount <span class="text-danger">*</span></label>
                <input type="text" name="earn_amount" class="form-control radius-8" placeholder="e.g. 1000" required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Earn Points <span class="text-danger">*</span></label>
                <input type="text" name="earn_points" class="form-control radius-8" placeholder="e.g. 100" required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="redeem_points" class="form-control radius-8" placeholder="e.g. 100"
                    required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Redeem Amount <span class="text-danger">*</span></label>
                <input type="text" name="redeem_amount" class="form-control radius-8" placeholder="e.g. 1000"
                    required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Min Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="min_redeem_points" class="form-control radius-8" placeholder="e.g. 100"
                    required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-20">
                <label class="form-label text-sm mb-8">Max Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="max_redeem_points" class="form-control radius-8" placeholder="e.g. 1000"
                    required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>


            <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
                <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Save</button>
            </div>
        </div>
    </form>
</div>
