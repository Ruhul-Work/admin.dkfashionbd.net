<!-- Receive All Modal -->
{{-- <div class="modal fade" id="receiveAllModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"> --}}
    <div class="modal-content">
      <form id="receiveAllForm" data-ajax="true" method="POST" action="{{ route('purchase.receipts.store') }}" enctype="multipart/form-data">
         @csrf
        <input type="hidden" name="purchase_order_id" value="{{ $order->id }}">

        <div class="modal-header">
          <h5 class="modal-title">Receive All — {{ $order->po_number }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p>You are about to receive <strong>all remaining</strong> items for this Purchase Order. This will add quantities to stock and set order status to <strong>receive</strong>.</p>

          <div class="mb-3">
            <label class="form-label">Receipt Date</label>
            <input type="date" name="receipt_date" class="form-control" value="{{ now()->toDateString() }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Note (optional)</label>
            <textarea name="note" class="form-control" rows="3"></textarea>
          </div>

          <h6>Items to receive</h6>
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Product</th>
                <th>Ordered</th>
                <th>Already received</th>
                <th>Will receive</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $it)
                @php $remaining = max(0, $it->quantity - ($it->received_quantity ?? 0)); @endphp
                <tr>
                  <td>{{ $it->product->name ?? '-' }}</td>
                  <td>{{ $it->quantity }}</td>
                  <td>{{ $it->received_quantity ?? 0 }}</td>
                  <td>{{ $remaining }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm rounded-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="receiveAllSubmit" class="btn btn-dark btn-sm rounded-4">Receive All</button>
        </div>
      </form>
    </div>

    @section('scripts')
    <script>
      const purchaseReceipt = {
        onReceived: function(orderId) {
          // Reload the page to reflect changes
          window.location.href = "{{ route('purchase.index') }}";
        }
      };
    </script>
    @endsection
  {{-- </div>
</div> --}}
