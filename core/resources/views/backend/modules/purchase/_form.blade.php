@php
    $editing = isset($purchase) && $purchase;
@endphp

<form id="purchase-main-form" method="POST" enctype="multipart/form-data"
    action="{{ $editing ? route('purchase.orders.update', $purchase->id) : route('purchase.orders.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="d-flex p-1 justify-content-between mt-3">
        <h1 class="text-xl lh-1 fw-semibold p-1">Purchase List</h1>

        {{-- <div>
            <h6 class="text-xs   p-1 px-3 bg-dark text-white rounded-pill">#ord1247</h6>
        </div> --}}
    </div>

    <hr class=" px-3" style="border-top: 1px dashed #000;">
    <div class="p-1 mt-3">
        <h1 class="text-lg lh-1 fw-semibold p-1">Supplier's Information</h1>

        {{-- Supplier select (same as create) --}}
        <div class="col-lg-10 d-flex gap-2">
            <div class="mt-1 col-lg-12">
                <select class="js-s2-ajax" name="supplier_id" id="supplier"
                    data-url="{{ route('supplier.select2') }}" data-placeholder="Select Supplier">
                    <option id="recent" value="" selected></option>
                </select>
            </div>
            <div class="p- my-1 d-flex gap-2">
                <button class="btn btn-success rounded-1 btn-sm AjaxModal"
                    data-ajax-modal="{{ route('supplier.createModal') }}" data-size="lg"
                    data-onload="CategoryIndex.onLoad" data-onsuccess="CategoryIndex.onSaved"> <iconify-icon
                        icon="flowbite:users-outline" class="menu-icon"></iconify-icon></button>
                {{-- <button class="btn btn-primary rounded-1 btn-sm"><iconify-icon icon="mdi:qrcode-scan"
                        class="menu-icon"></iconify-icon></button> --}}
            </div>
        </div>
        {{-- Warehouse / Branch / Status / Purchase date / Reference --}}
        <div class="row">
            <div class="col-md-6 py-1 px-3">
                <label for="warehouse_id" class="form-label">Warehouse</label>
                <select class="form-control form-control-sm col-lg-3 purchase-ware-select js-s2-ajax"
                    name="warehouse_id" id="warehouse" data-url="{{ route('inventory.warehouses.select2') }}"
                    data-placeholder="Select warehouse">
                    <option id="recent" value="" selected></option>
                </select>
            </div>

            <div class="col-md-6 py-1 px-3">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branchSelect" class="form-control purchase-ware-select js-s2-ajax"
                    data-url="{{ route('org.branches.select2') }}" data-placeholder="Select branch">
                </select>
            </div>

            {{-- <div class="col-lg-6 py-1 px-3">
                <label for="status" class="form-label">Order Status</label>
                <select name="status" id="status" class="form-control form-control-sm"
                    {{ isset($isEditable) && !$isEditable ? 'disabled' : '' }}>
                    <option value="draft" {{ ($purchase->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                        Draft</option>
                    <option value="received" {{ ($purchase->status ?? '') === 'received' ? 'selected' : '' }}>
                        Received
                    </option>
                </select>
            </div> --}}

            <div class="col-lg-6 py-1 px-3">
                <label for="status" class="form-label">Order Status</label>

                <select name="status" id="status" class="form-control form-control-sm"
                    {{ isset($isEditable) && !$isEditable ? 'disabled' : '' }}>

                    <option value="draft" {{ ($purchase->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>

                    @if (isset($purchase))
                        <option value="received" {{ ($purchase->status ?? '') === 'received' ? 'selected' : '' }}>
                            Received
                        </option>
                    @endif

                </select>
            </div>

            <div class="col-lg-6  py-1 px-3">
                <label for="" class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control form-control-sm"
                    value="{{ old('purchase_date', isset($purchase) && $purchase->order_date ? \Carbon\Carbon::parse($purchase->order_date)->format('Y-m-d') : '') }}"
                    {{ isset($isEditable) && !$isEditable ? 'disabled' : '' }}>
            </div>

            <div class="col-lg-6 px-3">
                <label for="" class="form-label">Reference</label>
                <input type="text" class="form-control form-control-sm" name="reference"
                    value="{{ $purchase->reference ?? '' }}"
                    {{ isset($isEditable) && !$isEditable ? 'readonly' : '' }}>
            </div>

            <div class="col-lg-6 mt- px-3">
                <label for="purchase_invoice" class="form-label">Invoice</label>
                <input type="file" class="form-control form-control-sm p-1" name="purchase_invoice"
                    id="purchase_invoice" {{ isset($isEditable) && !$isEditable ? 'disabled' : '' }}>
                @if (!empty($purchase->purchase_invoice))
                    <div class="mt-1"><a href="{{ asset($purchase->purchase_invoice) }}" target="_blank">Current
                            Invoice</a></div>
                @endif
            </div>
        </div>

        <hr class="my-3">
        {{-- order details --}}
        <div class="d-flex justify-content-between p-3">

            <div class="d-flex gap-2">
                <h1 class="text-md lh-1 fw-semibold mt-1 p-2">Order Details</h1>
                <button class="btn btn-outline-dark  border btn-sm px-1 py-0 ">Items :
                    <span id="total_items">3</span></button>
            </div>
            <div>
                <button type="button" class="btn btn-outline-danger empty-cart btn-xs py-1 text-xs">Clear all</button>
            </div>

        </div>

        {{-- Items table (leave placeholder; JS will populate purchase_items tbody) --}}
        <div class="p-3" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-borderless text-gray scrollable-table">
                <thead class="text-sm  fw-semibold">
                    <tr class="table-light rounded-3 px-1">
                        <th scope="col" class="text-center">Item</th>
                        <th scope="col" class="text-center">Stock</th>
                        <th scope="col" class="text-center">Quantity</th>
                        <th scope="col" class="text-center">Price</th>
                        <th scope="col" class="text-center">Cost</th>
                        <th scope="col" class="text-center"></th>
                    </tr>
                </thead>
                <tbody class="text-sm " id="purchase_items">
                    {{-- JS fills rows from localStorage cart --}}
                </tbody>
            </table>
        </div>


        <div class="p-3">
            <table class="table table-sm table-borderless  text-gray">
                <thead class="text-sm fw-semibold">
                    <tr class=" rounded-3  px-1">

                        <th scope="col">Payment Summary</th>


                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr>

                        <td class="text-secondary">
                            <span>Subtotal</span>

                        </td>
                        <td class="text-secondary" id="subtotal">$2300</td>

                    </tr>
                    <tr>

                        <td class="text-secondary">
                            <span>Shippping</span>

                            <!-- Modal -->

                        </td>
                        <td class="text-secondary "><input type="number" id="shipping" min="0"
                                class="border-1 rounded px-1 " value="0" style="width:70px; height:30px">
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            <span>Discount</span>
                            <button type="button" class="bg-light rounded-4 border-0 p-1 icon"
                                data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                                <iconify-icon icon="flowbite:edit-outline" class="menu-icon fs-7"></iconify-icon>
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-lg" id="exampleModalCenterTitle">
                                                Discount
                                            </h5>
                                            <button type="button" class="btn-close " data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-3">
                                            <label for="" class="form-label">Discount Type</label>
                                            <select class="form-control " name="discount_type" id="discount-type">
                                                <option value="flat">Flat</option>
                                                <option value="percentage" selected>Percentage</option>
                                            </select>
                                            <label for="" class="form-label mt-3">Amount</label>
                                            <input type="number" min="0" class="form-control "
                                                id="discount-input">
                                        </div>
                                        <div class="modal-footer ">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" data-bs-toggle="modal" class="btn btn-dark btn-sm"
                                                id="discount-save">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td> <input type="number" min="0" value="0" disabled id="discount"
                                class="text-danger " style="width: 80px"></td>

                    </tr>



                    <tr>

                        <td>
                            <hr class="mt-2" />
                        </td>
                        <td>
                            <hr class="mt-2" />
                        </td>
                    </tr>
                    <tr class="fw-semibold">
                        <td>Total Amount</td>
                        <td id="total_amount" class="">$2360</td>
                    </tr>
                </tbody>
            </table>

        </div>

        {{-- Summary and checkout modal trigger (keep same IDs as create) --}}
        <div class="d-flex justify-content-center mt-3 mb-3">
            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                data-bs-whatever="@getbootstrap"
                class="btn btn-danger payment-modal border p-3 col-lg-12">Purchase</button>
        </div>

</form>


{{-- checkout form modal --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Checkout</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="mb-3 col-lg-4">
                            <label for="total_amount" class="col-form-label">Total
                                Amount</label>
                            <input type="number" name="total_amount" disabled class="form-control form-control-sm"
                                id="checkout_total_amount" value="0">
                        </div>
                        <div class="mb-3 col-lg-4">
                            <label for="paid_amount" class="col-form-label">Paid
                                Amount</label>
                            <input type="number" name="paid_amount" min="0"
                                class="form-control form-control-sm" id="paid_amount" value="0">
                        </div>
                        <div class="mb-3 col-lg-4">
                            <label for="due_amount" class="col-form-label">Due Amount</label>
                            <input type="number" name="due_amount" disabled class="form-control form-control-sm"
                                id="due_amount" value="0">
                        </div>
                    </div>
                    <div class="mb-3 ">
                        <label for="payment_type" class="col-form-label">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control form-control-sm">
                            <option value="cash" selected>Cash</option>
                            <option value="card">Card</option>
                            <option value="bkash">Bkash</option>
                        </select>
                    </div>
                    <div class="mb-3 ">
                        <label for="payment_receiver" class="col-form-label">Payment
                            Receiver</label>
                        <input type="text" class="form-control form-control-sm" name="payment_receiver"
                            id="recipient-name">
                    </div>
                    <div class="mb-3 ">
                        <label for="payment_note" class="col-form-label">Payment Note</label>
                        <textarea rows="2" name="payment_note" id="payment_note" class="col-md-12 rounded-3"></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm rounded-4"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submit_purchase_btn"
                    class="btn btn-dark btn-sm rounded-4 submit-purchase">Submit</button>
            </div>
        </div>
    </div>
</div>
