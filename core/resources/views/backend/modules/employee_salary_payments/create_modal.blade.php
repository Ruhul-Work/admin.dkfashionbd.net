<div class="modal-header py-16 px-24">
    <h5 class="modal-title fw-semibold">Employee Salary Payment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body px-24 py-16">
    <form id="employeeSalaryPaymentForm" action="{{ route('employee.salary-payments.store') }}" method="POST" data-ajax="true">
        @csrf

        <div class="row g-3 mb-16">
            <div class="col-md-6">
                <label class="form-label text-sm">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" id="employee_id" class="form-select radius-8" required>
                    <option value="">Select employee</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" data-balance="{{ $emp->current_balance }}">{{ $emp->name }} (Payable: {{ number_format($emp->current_balance, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm">Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control radius-8" value="{{ now()->toDateString() }}" required>
            </div>
        </div>

        <div class="row g-3 mb-16">
            <div class="col-md-12">
                <label class="form-label text-sm">Paid From Account <span class="text-danger">*</span></label>
                <select name="account_id" class="form-select radius-8" required>
                    <option value="">Select account</option>
                    @foreach ($accounts as $ba)
                        <option value="{{ $ba->account_id }}">{{ $ba->account?->name }}@if($ba->account?->bank_name) ({{ $ba->account->bank_name }})@endif</option>
                    @endforeach
                </select>
            </div>
        </div>

        <table class="table table-sm align-middle" id="salaryPaymentItemsTable">
            <thead class="table-light">
                <tr>
                    <th style="width:34%">Salary Type</th>
                    <th style="width:30%">Payment Method</th>
                    <th class="text-end" style="width:26%">Amount</th>
                    <th style="width:40px"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="payment-item-row">
                    <td>
                        <select name="items[0][salary_type]" class="form-select form-control-sm" required>
                            <option value="">Select type</option>
                            <option value="salary">Salary</option>
                            <option value="incentive">Incentive</option>
                            <option value="bonus">Bonus</option>
                            <option value="overtime">Overtime</option>
                            <option value="due">Due</option>
                            <option value="opening">Opening</option>
                        </select>
                    </td>
                    <td>
                        <select name="items[0][payment_method]" class="form-select form-control-sm" required>
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" name="items[0][amount]" class="form-control form-control-sm text-end payment-amount" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btnRemovePaymentRow">✕</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-end mb-8">
            <button type="button" id="btnAddPaymentItem" class="btn btn-sm btn-outline-primary">+ Add Item</button>
        </div>

        <div class="card mb-16">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Total Payment</h6>
                <h5 class="mb-0 fw-bold">
                    <span id="paymentTotalText">0.00</span>
                </h5>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-16">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
    </form>
</div>

<script>
    (function() {
        const balanceMap = @json($balances);
        const $table = $('#salaryPaymentItemsTable tbody');
        let rowIndex = 1;

        function calculateTotal() {
            let total = 0;
            $('.payment-amount').each(function() {
                total += parseFloat($(this).val() || 0);
            });
            $('#paymentTotalText').text(total.toFixed(2));
        }

        function prefillBalance() {
            const emp = $('#employee_id').val();
            const balance = parseFloat(balanceMap[emp] || 0);
            if (balance > 0) {
                const $firstRow = $table.find('tr:first');
                const currentAmount = parseFloat($firstRow.find('.payment-amount').val() || 0);
                if (!currentAmount) {
                    $firstRow.find('select').val('salary');
                    $firstRow.find('.payment-amount').val(balance.toFixed(2));
                    calculateTotal();
                }
            }
        }

        $('#employee_id').on('change', prefillBalance);

        $('#salaryPaymentItemsTable').on('input change', '.payment-amount', calculateTotal);

        $('#btnAddPaymentItem').on('click', function() {
            const $firstRow = $table.find('tr:first');
            const $row = $firstRow.clone();
            $row.find('input, select').each(function() {
                const name = $(this).attr('name');
                if (!name) return;
                const newName = name.replace(/\[\d+\]/, `[${rowIndex}]`);
                $(this).attr('name', newName).val('');
            });
            $table.append($row);
            rowIndex++;
            calculateTotal();
        });

        $table.on('click', '.btnRemovePaymentRow', function() {
            if ($table.find('tr').length === 1) return;
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $('#employeeSalaryPaymentForm').on('submit', function(e) {
            // Allow payments exceeding payable; balance may become negative.
        });

        calculateTotal();
    })();
</script>