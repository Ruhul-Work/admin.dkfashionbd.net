<div class="modal-header">
    <h6 class="modal-title">
        Voucher #{{ $journalEntry->voucher_no }}
        <span class="badge bg-secondary ms-2">
            {{ $journalEntry->voucherType?->name }}
        </span>
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    {{-- Meta --}}
    <div class="row mb-3 small text-muted">
        <div class="col-md-4">
            <div>
            <strong>Voucher No:</strong> {{ $journalEntry->voucher_no }}
            </div>
            <strong>Date:</strong> {{ $journalEntry->entry_date }}
        </div>
        <div class="col-md-4">
            <strong>Branch:</strong> {{ $journalEntry->branch?->name }}
        </div>
        <div class="col-md-4">
            <strong>Created By:</strong> {{ $journalEntry->createdBy?->name }}
        </div>
    </div>

    {{-- Narration --}}
    <div class="mb-3">
        <strong>Narration:</strong>
        <div class="border rounded p-2 bg-light">
            {{ $journalEntry->narration }}
        </div>
    </div>

    {{-- Lines --}}
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Account</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDebit = 0;
                    $totalCredit = 0;
                @endphp

                @foreach ($journalEntry->lines as $line)
                    @php
                        $totalDebit += $line->debit;
                        $totalCredit += $line->credit;
                    @endphp
                    <tr>
                        <td>{{ $line->account?->name }}</td>
                        <td class="text-end">{{ number_format($line->debit, 2) }}</td>
                        <td class="text-end">{{ number_format($line->credit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-semibold">
                <tr>
                    <td class="text-end">Total</td>
                    <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                    <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
