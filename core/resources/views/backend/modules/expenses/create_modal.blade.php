{{-- ================= MODAL HEADER ================= --}}
<div class="modal-header py-16 px-24">
    <h5 class="modal-title fw-semibold">Add Expense</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- ================= MODAL BODY ================= --}}
<div class="modal-body px-24 py-16">

    <form id="expenseCreateForm" action="{{ route('expenses.store') }}" method="POST" data-ajax="true" data-branch-id="{{ current_branch_id() }}">

        @csrf

        {{-- ================= BILL INFO ================= --}}
        <div class="card mb-16">
            <div class="card-body">

                <h6 class="mb-12 fw-semibold">Expense Information</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-sm">Expense Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm radius-8"
                            placeholder="e.g. Office Monthly Expense" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-sm">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control form-control-sm radius-8"
                            value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-sm">Reference</label>
                        <input type="text" name="reference" class="form-control form-control-sm radius-8"
                            placeholder="Invoice / Memo No">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-sm">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm radius-8"
                            placeholder="Optional note">
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= EXPENSE ITEMS ================= --}}
        <table class="table table-sm align-middle" id="expenseItemsTable">
            <thead class="table-light">
                <tr>
                    <th style="width:30%">Category</th>
                    <th>Description</th>
                    <th class="text-end" style="width:140px">Amount</th>
                    <th style="width:40px"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="expense-item-row">
                    <td>
                        <select name="items[0][expense_category_id]" class="form-control form-control-sm js-s2-ajax"
                            data-url="{{ route('expenseCategories.select2') }}" required></select>
                    </td>
                    <td>
                        <input type="text" name="items[0][description]" class="form-control form-control-sm"
                            placeholder="Optional">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[0][amount]"
                            class="form-control form-control-sm text-end expense-amount" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow">
                            ✕
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-end mb-8">
            <button type="button" id="btnAddExpenseItem" class="btn btn-sm btn-outline-primary">
                + Add Item
            </button>
        </div>

        {{-- ================= SUMMARY ================= --}}
        <div class="card mb-16">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Total Amount</h6>
                <h5 class="mb-0 fw-bold">
                    <span id="expenseTotalText">0.00</span>
                </h5>
                <input type="hidden" name="total_amount" id="expenseTotalInput">
            </div>
        </div>

        {{-- ================= PAYMENT INFO ================= --}}
        <div class="card mb-16">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label text-md">Payment Method</label>
                        <select name="payment_type_id" class="form-control form-control-sm  js-s2-ajax"
                            data-url="{{ route('paymentTypes.select2') }}">
                        </select>
                        <small class="text-muted">
                            Only payment medium (account handled automatically)
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= ACTION ================= --}}
        <input type="hidden" name="status" id="expenseStatus" value="">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                Cancel
            </button>

            <button type="button" class="btn btn-sm btn-outline-warning" id="btnSaveDraft">
                Save as Draft
            </button>

            <button type="button" class="btn btn-sm btn-success" id="btnPostExpense">
                Post Expense
            </button>

        </div>

    </form>
</div>

{{-- ================= JS HOOKS (NO LOGIC YET) ================= --}}
<script>
    window.EXPENSE_INVOICE_URL = "{{ route('expenses.invoice', ':id') }}";
    window.ExpenseIndex = {
        onSaved: function(res) {

            // 🔹 Table reload
            if ($.fn.DataTable) {
                $('.AjaxDataTable').DataTable().ajax.reload(null, false);
            }

            const expenseId = res?.data?.id || res?.id;

            if (expenseId) {

                const url = window.EXPENSE_INVOICE_URL.replace(':id', expenseId);

                window.open(
                    url,
                    '_blank',
                    'width=900,height=650'
                );
            }

            // 🔹 Close modal
            $('.modal').modal('hide');
        }
    };

    (function() {

        const $table = $('#expenseItemsTable tbody');
        let rowIndex = 1;

        /* ================= ADD ROW ================= */
        $('#btnAddExpenseItem').on('click', function() {

            const $firstRow = $table.find('tr:first');

            // 1️⃣ destroy select2 before cloning
            $firstRow.find('.select2-hidden-accessible').each(function() {
                $(this).select2('destroy');
            });

            // 2️⃣ clone clean row
            const $row = $firstRow.clone();

            // 3️⃣ fix name & clear values
            $row.find('input, select').each(function() {
                const name = $(this).attr('name');
                if (!name) return;

                const newName = name.replace(/\[\d+]/, `[${rowIndex}]`);
                $(this).attr('name', newName).val('');
            });

            $table.append($row);

            // 4️⃣ re-init select2 for ALL rows
            if (window.S2 && typeof window.S2.auto === 'function') {
                window.S2.auto();
            }

            rowIndex++;
        });

        /* ================= REMOVE ROW ================= */
        $table.on('click', '.btnRemoveRow', function() {
            if ($table.find('tr').length === 1) return;
            $(this).closest('tr').remove();
            calculateTotal();
        });

        /* ================= TOTAL CALC ================= */
        $table.on('input', '.expense-amount', calculateTotal);

        function calculateTotal() {
            let total = 0;
            $('.expense-amount').each(function() {
                total += parseFloat($(this).val() || 0);
            });

            $('#expenseTotalText').text(total.toFixed(2));
            $('#expenseTotalInput').val(total.toFixed(2));
        }

        // Form submit handlers
        $('#btnSaveDraft').on('click', function() {
            // Check if branch is selected
            const branchId = parseInt($('#expenseCreateForm').data('branch-id') || 0);
            if (!branchId || branchId === 0) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Branch Not Selected',
                        text: 'Please select a branch first before creating expense',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#FFC107'
                    });
                } else {
                    alert('Please select a branch first before creating expense');
                }
                return false;
            }
            $('#expenseStatus').val('draft');
            $('#expenseCreateForm').submit();
        });

        $('#btnPostExpense').on('click', function() {
            // Check if branch is selected
            const branchId = parseInt($('#expenseCreateForm').data('branch-id') || 0);
            if (!branchId || branchId === 0) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Branch Not Selected',
                        text: 'Please select a branch first before creating expense',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#FFC107'
                    });
                } else {
                    alert('Please select a branch first before creating expense');
                }
                return false;
            }
            $('#expenseStatus').val('posted');
            $('#expenseCreateForm').submit();
        });

        /* ================= INIT ON LOAD ================= */
        if (window.S2 && typeof window.S2.auto === 'function') {
            window.S2.auto();
        }

    })();
</script>
