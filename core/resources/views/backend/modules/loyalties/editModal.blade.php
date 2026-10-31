<div class="modal-header py-16 px-24 border-0" data-modal-key="loyalty-edit">
    <h5 class="modal-title">Edit Loyalty Rule</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-26">
    <form id="loyaltyEditForm" action="{{ route('loyalty.update', $loyaltyRule->id) }}" method="post" data-ajax="true">
        @csrf
        @method('PUT')

        <div class="row g-16">
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control radius-8" required
                    value="{{ $loyaltyRule->name }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Earn Amount <span class="text-danger">*</span></label>
                <input type="text" name="earn_amount" class="form-control radius-8" required
                    value="{{ $loyaltyRule->earn_amount }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Earn Points <span class="text-danger">*</span></label>
                <input type="text" name="earn_points" class="form-control radius-8" required
                    value="{{ $loyaltyRule->earn_points }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="redeem_points" class="form-control radius-8" required
                    value="{{ $loyaltyRule->redeem_points }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Redeem Amount <span class="text-danger">*</span></label>
                <input type="text" name="redeem_amount" class="form-control radius-8" required
                    value="{{ $loyaltyRule->redeem_amount }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Min Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="min_redeem_points" class="form-control radius-8" required
                    value="{{ $loyaltyRule->min_redeem_points }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>
            <div class="col-md-6 mb-16">
                <label class="form-label text-sm mb-6">Max Redeem Points <span class="text-danger">*</span></label>
                <input type="text" name="max_redeem_points" class="form-control radius-8" required
                    value="{{ $loyaltyRule->max_redeem_points }}">
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>


            {{-- actions --}}
            <div class="d-flex align-items-center justify-content-center gap-3 mt-12 p-3">
                <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-48 py-12 radius-8">Update</button>
            </div>

        </div>
    </form>
</div>
