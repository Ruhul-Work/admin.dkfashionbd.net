<div class="modal-header">
    <h5 class="modal-title">Change Sale Status</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('pos.sales.updateStatus', $sale->id) }}" method="POST" data-ajax="true">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="status" class="form-label">Select Status</label>
            <select name="status" id="status" class="form-select">
                <option value="pending" {{ $sale->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $sale->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="delivered" {{ $sale->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $sale->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-info">Update Status</button>
    </div>
</form>
