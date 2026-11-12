<div class="modal-header py-16 px-24">
    <h5 class="modal-title fw-semibold">Assign Employee Salary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body px-24 py-16">
    <form id="employeeSalaryCreateForm" action="{{ route('employee.salaries.store') }}" method="POST" data-ajax="true">
        @csrf

        <div class="row g-3 mb-16">
            <div class="col-md-6">
                <label class="form-label text-sm">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select radius-8" required>
                    <option value="">Select employee</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-sm">Salary Date <span class="text-danger">*</span></label>
                <input type="date" name="salary_date" class="form-control radius-8" value="{{ now()->toDateString() }}" required>
            </div>
        </div>

        <table class="table table-sm align-middle" id="salaryItemsTable">
            <thead class="table-light">
                <tr>
                    <th style="width:40%">Salary Type</th>
                    <th class="text-end" style="width:45%">Amount</th>
                    <th style="width:40px"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="salary-item-row">
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
                        <input type="number" step="0.01" min="0.01" name="items[0][amount]" class="form-control form-control-sm text-end salary-amount" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btnRemoveSalaryRow">✕</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-end mb-8">
            <button type="button" id="btnAddSalaryItem" class="btn btn-sm btn-outline-primary">+ Add Item</button>
        </div>

        <div class="card mb-16">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Total Amount</h6>
                <h5 class="mb-0 fw-bold">
                    <span id="salaryTotalText">0.00</span>
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
        const $table = $('#salaryItemsTable tbody');
        let rowIndex = 1;

        $('#btnAddSalaryItem').on('click', function() {
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

        $table.on('click', '.btnRemoveSalaryRow', function() {
            if ($table.find('tr').length === 1) return;
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $table.on('input', '.salary-amount', calculateTotal);

        function calculateTotal() {
            let total = 0;
            $('.salary-amount').each(function() {
                total += parseFloat($(this).val() || 0);
            });
            $('#salaryTotalText').text(total.toFixed(2));
        }

        calculateTotal();
    })();
</script>