<div class="modal-header">
    <h5 class="modal-title">Change Payment Status</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="paymentStatusForm" action="{{ route('pos.sales.updatePaymentStatus', $sale->id) }}" method="POST" data-ajax="true">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="payment_status" class="form-label">Select Payment Status</label>
            <select name="payment_status" id="payment_status" class="form-select">
                <option value="paid" {{ $sale->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ $sale->payment_status == 'pending' ? 'selected' : '' }}>Pending</option> 
                {{-- <option value="due" {{ $sale->payment_status == 'due' ? 'selected' : '' }}>Due</option> --}}
            </select>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-info">Update</button>
    </div>
</form>

