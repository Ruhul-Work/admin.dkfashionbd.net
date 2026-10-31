@extends('backend.layouts.master')
@section('content')
    @if (auth()->user()->isSuper() && !current_branch_id())
        <div class="alert alert-warning">
            Please select a branch before creating a POS sale.
        </div>
    @endif
    <div class="row ">

        <div class="product-div col-lg-7 bg-gray " style="height: 80vh; overflow-y: auto;">
            <div class="row justify-content-between">
                {{-- welcome div --}}
                <div class="col-lg-4">
                    <h6 class="text-xl lh-1 fw-semibold">Welcome, {{ Auth::user()->name }}</h6>
                    <p class="text-sm">{{ now()->format('l, d M Y') }} </p>
                </div>
                {{-- input and buttons --}}
                <div class="col-lg-6 d-flex justify-content-end align-items-center gap-2">
                    <input class="border  px-3 fst-italic rounded bg-light form-control form-control-sm" id="product-search-input"
                        type="text" placeholder="search product" style="width: 240px;">

                    <button class="btn btn-primary btn-sm px-3 text-xs">Featured</button>
                </div>


            </div>
            {{-- categories  --}}
            <div class="g-3 py-1 overflow-x-auto d-flex mt-3 category-nav" style="white-space: nowrap;">
                <button class="btn nav-btn active rounded-4 py-1 text-md" data-category-id="">All
                    Categories
                </button>
                @foreach ($categories as $category)
                    <button class="btn nav-btn  rounded-4 py-1 text-md"data-category-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
            {{-- products --}}
            <div class="products-div mt-3 row gap-space-between h-auto align-item-center justify-content-center"
                id="product-list">

                {{-- @include('backend.modules.products.product_list', ['products' => $products]) --}}


            </div>


        </div>


        <div class="order-div col-lg-5 bg-white rounded-3 " style="height: 80vh; overflow-y: auto;">

            <div class="d-flex p-1 justify-content-between mt-3">
                <h1 class="text-xl lh-1 fw-semibold p-1">Order List</h1>

                {{-- <div>
                    <h6 class="text-xs   p-1 px-3 bg-dark text-white rounded-pill">#ord1247</h6>
                </div> --}}
            </div>
            <hr class=" px-3" style="border-top: 1px dashed #000;">
            <div class="p-1 mt-3">
                <h1 class="text-lg lh-1 fw-semibold p-1">Customer's Information</h1>
                <div class="d-flex gap-2">

                    <div class="col-lg-12 d-flex gap-2">
                        <div class="mt-1 col-lg-11">
                            <select class="form-control form-control-sm  js-s2-ajax" name="customer_id" id="customer"
                                data-url="{{ route('customer.select2') }}" data-placeholder="Select Customer">
                                <option id="recent" value="1" selected>Walk In Customer</option>

                            </select>
                            <div class="invalid-feedback d-block category_id-error" style="display:none"> </div>
                        </div>
                        <div class=" my-1 p-2">
                            <button class="btn btn-success rounded-1 btn-sm" data-bs-toggle="modal"
                                data-bs-target="#customerModal"> <iconify-icon icon="flowbite:users-outline"
                                    class="menu-icon"></iconify-icon>
                            </button>
                            {{-- modal --}}
                            <div class="modal fade" id="customerModal" tabindex="-1"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-lg" id="exampleModalCenterTitle">Add Customer
                                            </h5>
                                            <button type="button" class="btn-close " data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form id="customer_form">
                                            <div class="modal-body p-3">
                                                <label for="" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="" name="name">
                                                <label for="" class="form-label mt-3">Email</label>
                                                <input type="email" min="0" class="form-control" id=""
                                                    name="email">
                                                <label for="" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="" name="phone">
                                                <label for="" class="form-label mt-3">Adress</label>
                                                <input type="text" min="0" class="form-control" id=""
                                                    name="address">
                                            </div>
                                            <div class="modal-footer ">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-dark btn-sm"
                                                    id="customer_submit">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- modal ends --}}


                        </div>
                    </div>
                </div>

                {{-- barcode input --}}
                <div class="d-flex gap-2 ">
                    <div class="col-md-11 col-lg-11">
                        <input type="text" id="barcodeInput" class="form-control form-control-sm" autocomplete="off"
                            placeholder="Scan barcode" />
                    </div>
                    <div class="my-1">
                        <button id="btn-scan" class="btn btn-primary rounded-1 btn-md"><iconify-icon
                                icon="mdi:qrcode-scan" class="menu-icon"></iconify-icon></button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3 mb-0 rounded-3 p-3 "
                    style="background:#FFEEE9;border:1px solid #E04F16">
                    <div class="">
                        <h1 class="text-md lh-1 fw-semibold " id="customer_point">No Registered Customer Selected</h1>
                        <p class="text-sm lh-1">Loyalty Point: <span class="bg-info rounded-3 fw-semibold p-1 text-white"
                                id="loyalty_point">0</span>
                            |
                            Discount: <span class="bg-success rounded-3 fw-semibold p-1 text-white"><input
                                    style="width: 40px" disabled type="number" value="0"
                                    id="avail_loyalty_discount"> </span></p>
                    </div>
                    <div> <button class="btn btn-danger btn-xs py-1 text-xs " id="redeem_point">Apply</button></div>

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
                        <button class="btn btn-outline-danger empty-cart btn-xs py-1 text-xs">Clear all</button>
                    </div>

                </div>
                {{-- table --}}
                <div class="p-3">
                    <table class="table table-sm table-borderless  text-gray">
                        <thead class="text-sm  fw-semibold">
                            <tr class="table-light rounded-3 px-1">

                                <th scope="col" class="text-center">Item</th>
                                <th scope="col" class="text-center">Quantity</th>
                                <th scope="col" class="text-center">Price</th>
                                <th scope="col" class="text-center">MRP</th>
                                <th scope="col" class="text-center">Total</th>
                                <th scope="col" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="text-sm " id="cart_items">


                        </tbody>
                    </table>


                </div>

                {{-- <div class="d-flex justify-content-between mt-3 mb-0 rounded-3 p-3 "
                    style="background:#e9e0ef;border:1px solid #8035ba">
                    <div class="">
                        <h1 class="text-md lh-1 fw-semibold ">Discount 5%</h1>
                        <p class="text-sm ">For 2000 tk Minimum Purchase, all Items</p>
                    </div>
                    <div> <button class="btn btn-danger btn-xs py-1 text-xs">Apply</button></div>

                </div> --}}

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
                                    <button class="bg-light rounded-4 border-0 p-1 icon" data-bs-toggle="modal"
                                        data-bs-target="#shippingModal">
                                        <iconify-icon icon="flowbite:edit-outline" class="menu-icon fs-7"></iconify-icon>
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="shippingModal" tabindex="-1"
                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-lg" id="exampleModalCenterTitle">Shipping
                                                        Charge</h5>
                                                    <button type="button" class="btn-close " data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-3">

                                                    <label for="" class="form-label ">Amount</label>
                                                    <input type="number" min="0" class="form-control"
                                                        id="shipping-input">
                                                </div>
                                                <div class="modal-footer ">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-dark btn-sm"
                                                        id="shipping-save">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary"><input type="number" disabled id="shipping" min="0"
                                        value="0" style="width: 80px"></td>

                            </tr>
                            <tr>

                                <td class="text-secondary">
                                    <span>Coupon</span>
                                    <button class="bg-light rounded-4 border-0 p-1 icon" data-bs-toggle="modal"
                                        data-bs-target="#couponModal">
                                        <iconify-icon icon="flowbite:edit-outline" class="menu-icon fs-7"></iconify-icon>
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="couponModal" tabindex="-1"
                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-lg" id="exampleModalCenterTitle">Coupon
                                                    </h5>
                                                    <button type="button" class="btn-close " data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-3">

                                                    <label for="" class="form-label ">Available Coupon</label>
                                                    <select class="form-control form-control-sm  js-s2-ajax"
                                                        name="coupon_id" id="coupon-select"
                                                        data-url="{{ route('coupon.select2') }}"
                                                        data-placeholder="Select Coupon">

                                                    </select>

                                                    <input type="hidden" id="applied_coupon_id" value="">
                                                    <input type="hidden" id="applied_coupon_code" value="">
                                                    <input type="hidden" id="applied_coupon_discount" value="0">

                                                    <div class="invalid-feedback d-block category_id-error"
                                                        style="display:none"> </div>
                                                </div>
                                                <div class="modal-footer ">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-dark btn-sm"
                                                        id="coupon-save">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-danger"><input type="number" disabled id="coupon_discount"
                                        min="0" value="0" style="width: 80px"></td>

                            </tr>
                            <tr>

                                <td class="text-secondary">
                                    <span>Discount</span>
                                    <button type="button" class="bg-light rounded-4 border-0 p-1 icon"
                                        data-bs-toggle="modal" data-bs-target="#discountModal">
                                        <iconify-icon icon="flowbite:edit-outline" class="menu-icon fs-7"></iconify-icon>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="discountModal" tabindex="-1"
                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-lg" id="exampleModalCenterTitle">Discount
                                                    </h5>
                                                    <button type="button" class="btn-close " data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-3">
                                                    <label for="" class="form-label">Discount Type</label>
                                                    <select class="form-control " name="discount_type"
                                                        id="discount-type">
                                                        <option value="flat">Flat</option>
                                                        <option value="percentage" selected>Percentage</option>
                                                    </select>
                                                    <label for="" class="form-label mt-3">Amount</label>
                                                    <input type="number" min="0" class="form-control"
                                                        id="discount-input">
                                                </div>
                                                <div class="modal-footer ">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-dark btn-sm"
                                                        id="discount-save">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td> <input type="number" min="0" value="0" disabled id="discount"
                                        class="text-danger" style="width: 80px"></td>

                            </tr>
                            <tr>

                                <td class="text-secondary">
                                    <span>Redeem Point</span>

                                </td>
                                <td class="text-danger"> <input type="number" min="0" value="0.00" disabled
                                        id="loyalty_discount" class="text-danger" style="width: 80px"></td>

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
                                <td id="total_amount">$2360</td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                {{-- payment button and modal --}}


                <div class="p-3 border border-danger bg-light rounded">
                    <h1 class="text-md lh-1 fw-semibold">Select Payment Method</h1>

                    <div class="row gap-3 mt-5 px-3 justify-content-center">
                        @foreach ($paymentTypes as $type)
                            <button type="button" class="btn col-lg-3 payment-btn payment-modal-btn btn-sm"
                                data-bs-toggle="modal" data-bs-target="#paymentModal" data-method="{{ $type->slug }}">
                                {{ $type->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- payment modal starts here --}}
                    <div class="modal fade" id="paymentModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h6 class="modal-title">Checkout</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <form>

                                        {{-- TOTAL --}}
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Total Amount</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="checkout_total_amount" disabled>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Change</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="change_amount" value="0.00" readonly>
                                            </div>
                                        </div>


                                        {{-- MULTIPLE PAYMENTS --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle" id="paymentTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="45%">Payment Method</th>
                                                        <th width="35%">Amount</th>
                                                        <th width="20%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary mb-3"
                                            id="addPaymentRow">
                                            + Add Payment
                                        </button>

                                        <hr>

                                        {{-- SALE STATUS --}}
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Sale Status</label>
                                                <select id="sale_status" class="form-control form-control-sm">
                                                    <option value="delivered" selected>Delivered</option>
                                                    <option value="hold">Hold</option>
                                                    <option value="draft">Draft</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Payment Receiver</label>
                                                <input type="text" id="payment_receiver"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        {{-- NOTES (kept as-is) --}}
                                        <div class="row">

                                            <div class="col-md-12">
                                                <label class="form-label">Sale Note</label>
                                                <textarea id="sale_note" rows="2" class="form-control form-control-md"></textarea>
                                            </div>
                                        </div>

                                    </form>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                                        Cancel
                                    </button>

                                    <button type="button" id="pos-submit-sale" class="btn btn-dark btn-sm">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

                <div class="d-flex mt-3 gap-2 mb-3">
                    {{-- <button style="padding: 10px"
                        class="btn btn-light border col-lg-6   d-flex align-items-center justify-content-center gap-2"><iconify-icon
                            icon="flowbite:printer-outline payment-modal-btn"
                            class="menu-icon fs-5 "></iconify-icon><span>Print
                            Order</span>
                    </button> --}}
                    <button data-bs-toggle="modal" data-bs-target="#paymentModal"
                        class="btn btn-danger border payment-modal-btn   col-lg-12 p-3 d-flex align-items-center justify-content-center gap-2"><iconify-icon
                            icon="flowbite:cart-outline" class="menu-icon fs-5 "></iconify-icon><span>Place
                            Order</span>
                    </button>


                </div>

            </div>
        </div>

    </div>
    <div class="footer-pos d-flex justify-content-center align-items-center gap-3 mt-3 position-absoulte bg-white p-3 "
        style="position:absoulte;width:100vw">
        <div class="p-0">
            <button id="hold-sale"
                class="btn btn-danger btn-sm rounded-4 d-flex align-item-center justify-content-center">
                <iconify-icon icon="flowbite:pause-outline" class="menu-icon fs-5"></iconify-icon>
                Hold
            </button>
        </div>
        <div class="p-0">
            <button id="view-hold-sales"
                class="btn btn-secondary btn-sm rounded-4 d-flex align-item-center justify-content-center">
                <iconify-icon icon="mdi:pause-circle-outline" class="menu-icon fs-5"></iconify-icon>
                Hold Orders
            </button>
        </div>

        <div class="p-0">

            {{-- <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
                tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-body justify-content-center ">
                            <div class="d-flex justify-content-center">
                                <iconify-icon icon="mdi:check-circle" class="menu-icon fs-5 text-success " width="40"
                                    height="40"></iconify-icon>
                            </div>

                            <h1 class="text-xl lh-1 text-dark my-3">Payment Completed</h1>
                            <p class="text-sm lh-1 text-secondary">Do you want to Print Receipt for the Completed Order</p>

                        </div>

                        <div class="modal-footer d-flex justify-content-center">
                            <button class="btn btn-dark btn-sm rounded-4" data-bs-target="#exampleModalToggle2"
                                data-bs-toggle="modal">Print Recipt</button>
                            <button class="btn btn-warning btn-sm rounded-4" data-bs-target="#exampleModalToggle2"
                                data-bs-toggle="modal">Next Order</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true"
                aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-body text-dark p-0 px-3">
                            <img src="http://localhost/pos-application/theme/admin/assets/images/logo1.png" class="mt-0"
                                alt="img" style="height: 32px">
                            <h1 class="text-xl mt-3">DK International Private Ltd.</h1>
                            <p class="lh-base text-sm mt-1 text-secondary fw-normal">
                            
                                <span>Phone Number: 01598929775</span><br>
                                <span>Email Address: dk@email.com</span><br>
                            </p>
                            <hr class="my-1">
                            <h1 class="text-md  ">Payment Invoice</h1>
                            <div class="row lh-1 text-sm mt-1 text-start px-3 text-secondary fw-normal ">
                                <p class="col-lg-6">Name: John Doe</p>
                                <p class="col-lg-6">Invoice No: #05418485</p><br>
                                <p class="col-lg-6">Customer Id: 45612</p>
                                <p class="col-lg-6">Date: {{ now() }}</p>
                            </div>
                            <hr>
                            <div class="px-5 ">
                                <table class="table text-start table-borderless text-sm table-sm ">
                                    <thead>
                                        <tr class="py-1">
                                            <th class=""># Item</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class=" fw-normal">
                                        @for ($i = 1; $i < 10; $i++)
                                            <tr class="px-1  lh-lg text-start">
                                                <td class="text-secondary">{{ $i }}. Formal dress shirt</td>
                                                <td class="text-secondary">
                                                    1600
                                                </td>
                                                <td class="text-secondary">
                                                    2
                                                </td>
                                                <td class="text-secondary">
                                                    3200
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <hr>

                            <div class="row px-5 fw-semibold mt-3">
                                <div class="d-flex justify-content-between px-3">
                                    <h1 class="text-md">Subtotal</h1>
                                    <h1 class="text-md px-3">3200</h1>
                                </div>
                                <div class="d-flex justify-content-between px-3 text-danger">
                                    <h1 class="text-md">Discount</h1>
                                    <h1 class="text-md  px-3">- 320</h1>
                                </div>
                                <div class="d-flex justify-content-between px-3">
                                    <h1 class="text-md">Tax</h1>
                                    <h1 class="text-md  px-3">50</h1>
                                </div>

                            </div>
                            <hr class="my-3">

                            <p class="lh-base">**VAT against this challan is payable through central registration.
                                Thank you for your business!</p>
                            <hr>

                            <p>Thank You For Shopping With Us. Please Come Again</p>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-dark btn-sm rounded-4" data-bs-target="#exampleModalToggle"
                                data-bs-toggle="modal">Print Recipt</button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <button
                class="btn btn-success btn-sm payment-modal-btn  rounded-4 d-flex align-item-center justify-content-center"
                data-bs-toggle="modal" data-bs-target="#paymentModal">
                <iconify-icon icon="flowbite:cash-outline" class="menu-icon fs-5 "></iconify-icon>Payment</button>
        </div>
        <div class="p-0">
            <button class="btn btn-dark btn-sm rounded-4 d-flex align-item-center justify-content-center"
                id="btn-view-orders"><iconify-icon icon="flowbite:cart-outline"
                    class="menu-icon fs-5 "></iconify-icon>View Orders</button>
        </div>
        <div class="p-0">
            <button class="btn btn-warning btn-sm rounded-4 d-flex align-item-center justify-content-center"
                id="pos-reset-btn"><iconify-icon icon="mdi:reload" class="menu-icon fs-5 "></iconify-icon>New
                Order</button>
        </div>
        <div class="p-0">
            <button
                class="btn btn-info btn-sm rounded-4 d-flex align-item-center justify-content-center btn-transactions"><iconify-icon
                    icon="mdi:swap-horizontal" class="menu-icon fs-5 "></iconify-icon>Transactions</button>
        </div>

        <!-- ******* Modal ********-->

        <!-- hold sales modal -->
        <div class="modal fade" id="holdSalesModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header ">
                        <h6 class="modal-title d-flex align-items-center gap-2"><iconify-icon
                                icon="mdi:pause-circle-outline" class="menu-icon fs-5 "></iconify-icon> Hold Orders</h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-sm table-hover mb-0 text-sm text-center text-semibold">
                            <thead>
                                <tr>
                                    <th class="text-center">Invoice</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="hold-sales-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <!-- end hold sales modal -->

        <!-- today orders modal start-->
        <div class="modal fade" id="todayOrdersModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header ">
                        <h6 class="modal-title d-flex align-items-center gap-2">
                            <iconify-icon icon="mdi:clipboard-list-outline" class="fs-5"></iconify-icon>
                            Today Orders
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light text-sm fw-semibold ">
                                <tr>
                                    <th class="text-center">Invoice</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="todayOrdersBody">
                                <tr>
                                    <td colspan="6" class="text-center p-3">
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <!-- today orders modal end-->

        <!-- order details modal start-->

        {{-- // Included via separate Blade file use global AJAXViewModal handler and show details in modal --}}

        <!-- order details modal end -->

        <!-- transactions modal start-->
        <div class="modal fade" id="transactionsModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h6 class="modal-title d-flex align-items-center gap-2">
                            <iconify-icon icon="mdi:swap-horizontal" class="fs-5"></iconify-icon>
                            Today Transactions
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0" style="height: 750px !important; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light text-sm  fw-semibold sticky-top">
                                <tr>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Invoice</th>
                                    <th class="text-center">Method</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Branch</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>

                            <tbody id="transactionsBody">
                                <tr>
                                    <td colspan="6" class="text-center p-3">Loading...</td>
                                </tr>
                            </tbody>

                            <tfoot class="table-light fw-semibold sticky-bottom">
                                <tr class="table-success fs-6">
                                    <td colspan="5" class="text-end">Cash Total</td>
                                    <td class="text-end"> <span id="cashTotal">0.00</span></td>
                                </tr>
                                <tr class="table-primary fs-6">
                                    <td colspan="5" class="text-end">Card Total</td>
                                    <td class="text-end"> <span id="cardTotal">0.00</span></td>
                                </tr>
                                <tr class="table-danger fs-6">
                                    <td colspan="5" class="text-end">bKash Total</td>
                                    <td class="text-end"> <span id="bkashTotal">0.00</span></td>
                                </tr>
                                <tr class="table-warning fs-6">
                                    <td colspan="5" class="text-end">Grand Total</td>
                                    <td class="text-end"> <span id="grandTotal">0.00</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- end transactions modal -->

    </div>
@endsection

@section('script')
    <script>
        // ====== tab-specific helpers ======
        // Robust synchronous tab helpers with safe duplicate handling + cart-migration
        (function() {
            // utils
            function randId() {
                return Math.random().toString(36).slice(2, 9);
            }

            function makeTabId() {
                return 'tab_' + Date.now() + '_' + randId();
            }

            // keys
            const SESSION_KEY = 'pos_tab_id';
            const PING_KEY = 'pos_tab_ping';
            const CONFLICT_KEY = 'pos_tab_conflict';

            // read existing session tab id (may have been copied on duplication)
            let sessionTabId = sessionStorage.getItem(SESSION_KEY);

            // If none, create and persist immediately (synchronous)
            if (!sessionTabId) {
                sessionTabId = makeTabId();
                sessionStorage.setItem(SESSION_KEY, sessionTabId);
            }

            // derive storage keys (based on current sessionTabId)
            function cartKeyFor(id) {
                return 'cart_' + id;
            }

            function discountKeyFor(id) {
                return 'discountPos_' + id;
            }

            function shippingKeyFor(id) {
                return 'shippingChargePos_' + id;
            }

            // Synchronous API (available immediately)
            window.getPosTabId = function() {
                return sessionStorage.getItem(SESSION_KEY);
            };
            window.getCart = function() {
                try {
                    return JSON.parse(localStorage.getItem(cartKeyFor(window.getPosTabId()))) || [];
                } catch (e) {
                    return [];
                }
            };
            window.setCart = function(cart) {
                localStorage.setItem(cartKeyFor(window.getPosTabId()), JSON.stringify(cart || []));
                window.dispatchEvent(new CustomEvent('pos:cart:updated', {
                    detail: {
                        tabKey: cartKeyFor(window.getPosTabId()),
                        cart
                    }
                }));
            };
            window.clearCartForTab = function() {
                localStorage.removeItem(cartKeyFor(window.getPosTabId()));
                window.dispatchEvent(new CustomEvent('pos:cart:cleared', {
                    detail: {
                        tabKey: cartKeyFor(window.getPosTabId())
                    }
                }));
            };
            window.getDiscountKey = function() {
                return discountKeyFor(window.getPosTabId());
            };
            window.getShippingKey = function() {
                return shippingKeyFor(window.getPosTabId());
            };

            // --- Duplicate detection in background (non-blocking) ---
            // If page was opened via "Duplicate tab" the sessionTabId value is copied.
            // We ping localStorage to see if another tab claims same sessionTabId.
            (function backgroundDuplicateCheck() {
                const instanceId = randId();
                // listen for pings: if another tab sends ping about same sessionTabId, we will respond indicating presence
                window.addEventListener('storage', function(e) {
                    if (!e.key) return;
                    try {
                        if (e.key === PING_KEY && e.newValue) {
                            const ping = JSON.parse(e.newValue);
                            if (ping && ping.tabId === sessionTabId && ping.instanceId !== instanceId) {
                                // somebody pinged with same tabId -> indicate conflict
                                localStorage.setItem(CONFLICT_KEY, JSON.stringify({
                                    tabId: sessionTabId,
                                    responder: instanceId,
                                    ts: Date.now()
                                }));
                                setTimeout(() => localStorage.removeItem(CONFLICT_KEY), 100);
                            }
                        }
                        // if we see conflict and it references our tabId, it means another tab exists with same tabId
                        if (e.key === CONFLICT_KEY && e.newValue) {
                            const conf = JSON.parse(e.newValue);
                            if (conf && conf.tabId === sessionTabId) {
                                // perform migration: copy current copied-cart (from sessionTabId) into a NEW unique tab id for this tab
                                migrateToNewTabId();
                            }
                        }
                    } catch (err) {}
                });

                // send ping shortly after load to probe others
                try {
                    localStorage.setItem(PING_KEY, JSON.stringify({
                        tabId: sessionTabId,
                        instanceId: instanceId,
                        ts: Date.now()
                    }));
                    setTimeout(() => localStorage.removeItem(PING_KEY), 100);
                } catch (e) {}

                // also listen shortly for conflict key in case another tab responded
                // give some time (200ms) for responders - if conflict arises, the storage listener above will trigger migration
            })();

            // If conflict detected for this tabId -> create a new tabId and migrate cart (copy) so user doesn't lose data
            function migrateToNewTabId() {
                try {
                    const oldId = sessionStorage.getItem(SESSION_KEY);
                    const oldCartKey = cartKeyFor(oldId);
                    const oldCartRaw = localStorage.getItem(oldCartKey);
                    // generate new id and persist
                    const newId = makeTabId();
                    sessionStorage.setItem(SESSION_KEY, newId);
                    // copy cart data (if any) into new key so reload keeps it
                    if (oldCartRaw !== null) {
                        localStorage.setItem(cartKeyFor(newId), oldCartRaw);
                    } else {
                        // ensure new key exists (empty array)
                        localStorage.setItem(cartKeyFor(newId), JSON.stringify([]));
                    }
                    // optionally remove old key? NO — do not remove old key (it belongs to other tab)
                    // update window-level functions to refer to new id (they read from sessionStorage each call so OK)
                    // emit event so UI can refresh
                    window.dispatchEvent(new CustomEvent('pos:tab:migrated', {
                        detail: {
                            oldId,
                            newId
                        }
                    }));
                    // refresh UI so cart reflects migrated key
                    try {
                        if (typeof loadCartItems === 'function') loadCartItems();
                    } catch (e) {}
                } catch (e) {}
            }

            // finished helper init
        })();
        // end helper


        // ====== POS UI Logic Global functions ======

        // loadCartItems
        function loadCartItems() {
            let cartItems = window.getCart() || [];
            let count = 0;
            let cartItemsHtml = '';
            cartItems.forEach(function(item) {
                count += 1;

                cartItemsHtml += `
                   <tr>
                                <td class="d-flex align-items-center text-center justify-content-start gap-2 text-secondary " style="padding-top:10px">
                                    <span class="order-item" title="${item.name}">${stringShortner(item.name,20)}
                                        </span>
                                </td>
                                <td class=" align-items-center justify-content-center text-center gap-2 text-secondary">
                                    <button class="bg-light  rounded-4 border-0 p-1 qty-button btn-minus">
                                        <iconify-icon icon="flowbite:minus-outline"
                                            class="menu-icon fs-7"></iconify-icon>
                                    </button>

                                    <span class="mx-3"><input class="form-control qty-input text-center text-black px-1 " type="number" value="${item.quantity}" min="1" style="min-width:30px !important; max-width:80px !important; height:35px; display:block; "></span>

                                    <button class="bg-light rounded-4 p-1 qty-button btn-plus">
                                        <iconify-icon icon="flowbite:plus-outline"
                                            class="menu-icon fs-7"></iconify-icon>
                                    </button>
                                </td>
                                <td class="" style="padding-top:10px">
                                    ${item.price}
                                </td>
                                <td class="" style="padding-top:10px">
                                    ${item.mrp}
                                </td>
                                <td class="product-price" style="padding-top:10px">
                                    ${(item.price * item.quantity).toFixed(2)}
                                </td>

                                <td style="padding-top:10px">
                                    <button class="icon product-delete" data-product_id="${item.id}"><iconify-icon icon="mdi:delete"
                                            class="menu-icon fs-7 text-danger"></button>
                                            </td>

                            </tr>`;
            });
            $('#cart_items').html(cartItemsHtml);
            $('#total_items').text(count);
            calculateSubtotal();
        } // loadCartItems

        // deleteFromCart
        function deleteFromCart(productId) {
            let cart = window.getCart() || [];
            let updatedCart = cart.filter(function(item) {
                return item.id !== productId;
            });
            window.setCart(updatedCart);
            calculateSubtotal();
        }

        // emptyCart (tab-specific)
        function emptyCart() {
            window.clearCartForTab();
            // clear tab-specific discount/shipping
            sessionStorage.removeItem(window.getDiscountKey());
            sessionStorage.removeItem(window.getShippingKey());
            window.coupon = null;
            loadCartItems();
            $('.product-card').removeClass('active');
        }

        // updateQuantityInCart
        function updateQuantityInCart(productId, newQuantity) {
            let cart = window.getCart() || [];
            cart.forEach(function(item) {
                if (item.id === productId) {
                    item.quantity = newQuantity;
                }
            });
            window.setCart(cart);
        }


        //new coupon function
        function previewCoupon(subtotal) {
            const couponId = window.coupon;
            if (!couponId || window.getCart().length === 0) {
                $('#coupon_discount').val('0.00');
                return Promise.resolve(0);
            }

            return $.ajax({
                url: "{{ route('coupon.preview') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    coupon_id: couponId,
                    user_id: $('#customer').val() || null,
                    cart: window.getCart(),
                    subtotal: subtotal
                }
            }).then(res => {
                let discount = 0;
                if (res.eligible) {
                    discount = res.discount;
                    $('#coupon_discount').val(discount.toFixed(2));

                    Swal.fire({
                        icon: 'success',
                        title: 'Coupon Applied',
                        text: `Coupon applied successfully`,
                        timer: 500,

                    });


                } else {
                    $('#coupon_discount').val('0.00');
                    // alert(res.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });

                    window.coupon = null;
                }
                return discount;
            });
        }


        // calculateSubtotal (uses tab-specific discount/shipping)
        async function calculateSubtotal() {

            let cart = window.getCart() || [];
            let discountObj = JSON.parse(sessionStorage.getItem(window.getDiscountKey())) || {
                type: 'flat',
                value: 0.00
            };

            let shipping = parseFloat(sessionStorage.getItem(window.getShippingKey())) || 0;
            let subtotal = 0;

            cart.forEach(item => {
                subtotal += (item.price * item.quantity);
            });

            // 🔹 coupon preview (ONLY once)
            let couponDiscount = await previewCoupon(subtotal) || 0;

            let discount = 0;
            let loyaltyPoints = $('#loyalty_discount').val() || 0;
            let total = 0;

            if (discountObj.type === 'percentage') {
                discount = (subtotal * discountObj.value / 100);
            } else {
                discount = parseFloat(discountObj.value || 0);
            }

            total = subtotal + shipping - discount - couponDiscount - loyaltyPoints;


            total = Math.round(total * 100) / 100;
            // UI update
            $('#discount').val(discount.toFixed(2));
            $('#shipping').val(shipping.toFixed(2));
            $('#subtotal').text(subtotal.toFixed(2));
            $('#total_amount').text(total.toFixed(2));

            return total; // ✅ final payable amount
        }


        // stringShortner
        function stringShortner(name, length) {
            if (!name) return '';
            if (name.length > length) return name.slice(0, length) + '...';
            else return name;
        }

        function productExistsInCart(productId) {
            let cart = window.getCart() || [];
            let exists = false;

            cart.forEach(item => {
                if (
                    item.id == productId || // direct / barcode
                    item.product_id == productId || // barcode safe
                    item.parent_id == productId // child product logic
                ) {
                    exists = true;
                }
            });

            return exists;
        }

        // ---------- Document Ready Start----------
        $(document).ready(function() {

            loadCartItems();

            // delete individual cart item
            $(document).off('click', '.product-delete').on('click', '.product-delete', function() {
                let productId = $(this).data('product_id');
                let cart = window.getCart() || [];
                let exists = cart.some(i => i.id === productId);
                if (exists) {
                    deleteFromCart(productId);
                    $(this).closest('tr').remove();
                } else {
                    // alert('Product not found in cart!');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Product not found in cart!'
                    });
                }
            });

            // clear cart
            $(document).off('click', '.empty-cart').on('click', '.empty-cart', function() {
                emptyCart();
            });

            // update cart on quantity change with input
            $(document).on('input', '.qty-input', function() {
                let productId = $(this).closest('tr').find('.product-delete').data('product_id');
                let newQuantity = parseInt($(this).val());
                if (isNaN(newQuantity) || newQuantity < 1) {
                    $(this).val(1);
                    return;
                }

                updateQuantityInCart(productId, newQuantity);

                let cart = window.getCart() || [];
                let product = cart.find(item => item.id === productId);
                if (product) {
                    let newPrice = product.price * product.quantity;
                    $(this).closest('tr').find('.product-price').html(`${newPrice.toFixed(2)}`);
                }
                calculateSubtotal();
            });

            // increase quantity with + button
            $(document).off('click', '.btn-plus').on('click', '.btn-plus', function() {
                let productId = $(this).closest('tr').find('.product-delete').data('product_id');
                let i = $(this).closest('tr').find('.qty-input');
                let priceEl = $(this).closest('tr').find('.product-price');
                let cart = window.getCart() || [];
                let product = cart.find(item => item.id === productId);
                if (!product) return;
                let newQty = (parseInt(i.val()) || product.quantity) + 1;
                i.val(newQty);
                updateQuantityInCart(productId, newQty);
                priceEl.html((product.price * newQty).toFixed(2));
                calculateSubtotal();
            });

            // decrease quantity with - button
            $(document).off('click', '.btn-minus').on('click', '.btn-minus', function() {
                let productId = $(this).closest('tr').find('.product-delete').data('product_id');
                let i = $(this).closest('tr').find('.qty-input');
                if (i.val() > 1) {
                    let cart = window.getCart() || [];
                    let product = cart.find(item => item.id === productId);
                    if (!product) return;
                    let newQty = product.quantity - 1;
                    updateQuantityInCart(productId, newQty);
                    i.val(newQty);
                    $(this).closest('tr').find('.product-price').html(
                        `${(product.price * newQty).toFixed(2)}`);
                }
                calculateSubtotal();
            });

            // pagination links delegate
            $(document).on('click', '.page-link-btn', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                if (!url || url === '#') return;
                loadProducts(url);
            });

            // Search
            $('#product-search-input').on('input', function() {
                let query = $(this).val();
                if (query.length > 0) {
                    let url = "{{ route('product.productsSearch', ':name') }}".replace(':name', query);
                    loadProducts(url);
                } else {
                    loadProducts("{{ route('product.productsList') }}");
                }
            });

            // category filter
            $('.nav-btn').on('click', function() {
                if ($(this).data('category-id') == '') {
                    loadProducts("{{ route('product.productsList') }}");
                    return;
                }
                let categoryId = $(this).data('category-id');
                let url = "{{ route('product.productsByCategory', ':category') }}".replace(':category',
                    categoryId);
                loadProducts(url);
            });




            // initial load
            loadProducts("{{ route('product.productsList') }}");
            loadCartItems();

            function updatePrice(element, price) {
                a = $(element).closest('tr').find('.product-price');
                a.html(price);
            }



            function loadProducts(url) {
                let productsHtml = '';
                // show loading spinner (optional)
                $('#product-list').html('<div class="w-100 text-center p-4">Loading products...</div>');

                $.ajax({
                    url: url,
                    type: 'GET',
                    timeout: 10000,
                    success: function(res) {
                        // defensive checks
                        if (!res || (typeof res !== 'object')) {
                            console.error('Invalid response from products endpoint:', res);
                            $('#product-list').html(
                                '<div class="w-100 text-center text-danger p-4">Could not load products (invalid response).</div>'
                            );
                            return;
                        }

                        // if paginator structure is different, try res.products or res.data
                        let products = res.products && res.products.data ? res.products.data : (res
                            .products || res.data || null);

                        if (!products || !Array.isArray(products)) {
                            console.error('Products array missing in response:', res);
                            $('#product-list').html(
                                '<div class="w-100 text-center text-danger p-4">No products available.</div>'
                            );
                            return;
                        }

                        products.forEach(function(product) {
                            let a = "{{ image('') }}";
                            let p = product.image || '';
                            let image = a.replace('theme/frontend/assets/img/default/book.png',
                                p || 'theme/frontend/assets/img/default/book.png');

                            productsHtml += `<div class="product-card  bg-white rounded-3 m-3 d-flex p-3 " data-product_id="${product.id}"
                    style="height: 110px; width:20%; cursor: pointer;">
                   <img class="img-fluid h-75 rounded col-lg-5  product-img" src="${image}" alt="img">
                         <div class="px-3 flex-grow-1">
                             <p class="lh-sm text-sm fw-semibold">${stringShortner(product.name,15)}</p>
                             <h1 class="text-sm lh-1 fw-semibold px-1 mt-1">${product.price}</h1>
                        </div>
                </div> `;
                        });

                        $('#product-list').html(productsHtml);
                        // (re)bind click handlers safely
                        $(document).off('click', '.product-card').on('click', '.product-card',
                            function() {
                                let productId = $(this).data('product_id');
                                if (productExistsInCart(productId)) {
                                    let cart = window.getCart() || [];
                                    let newCart = cart.filter(item => !(item.parent_id ===
                                        productId || item.id === productId));
                                    window.setCart(newCart);
                                    loadCartItems();
                                    $(this).removeClass('active');
                                } else {
                                    addToCart(productId);
                                    $(this).addClass('active');
                                }
                            });
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        // log detailed error for debugging
                        console.error('Products AJAX error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            textStatus: textStatus,
                            errorThrown: errorThrown
                        });

                        // show message to user
                        $('#product-list').html(
                            '<div class="w-100 text-center text-danger p-4">Failed to load products. <button id="retryProducts" class="btn btn-sm btn-outline-primary ms-2">Retry</button></div>'
                        );

                        safeLoadProducts(url);
                        // optional: show server error body in console if JSON
                        try {
                            const body = JSON.parse(xhr.responseText);
                            console.error('Server JSON error body:', body);
                        } catch (e) {
                            // not JSON — maybe HTML error page
                        }

                        // Attach retry handler
                        $(document).off('click', '#retryProducts').on('click', '#retryProducts',
                            function() {
                                loadProducts(url);

                            });
                    }
                });
            }

            // ================= safeLoadProducts with auto-retry ===================
            function safeLoadProducts(url, options = {}) {
                // options: retries (number), timeoutMs (number), backoffMs (number)
                const retries = typeof options.retries === 'number' ? options.retries :
                    2; // try total up to retries
                const timeoutMs = typeof options.timeoutMs === 'number' ? options.timeoutMs : 10000;
                const backoffMs = typeof options.backoffMs === 'number' ? options.backoffMs : 350;

                // internal recursive attempt
                function attempt(tryNo) {
                    // show loading UI on first try (you can customize)
                    if (tryNo === 0) {
                        $('#product-list').html('<div class="w-100 text-center p-4">Loading products...</div>');
                    } else {
                        // subtle loader when retrying
                        $('#product-list').prepend(
                            `<div id="product-retry-notice" class="w-100 text-center p-2 small text-muted">Retrying... (${tryNo}/${retries})</div>`
                        );
                    }

                    $.ajax({
                        url: url,
                        type: 'GET',
                        timeout: timeoutMs,
                        success: function(res) {
                            // remove retry notice if present
                            $('#product-retry-notice').remove();

                            // defensive parsing of response
                            let products = null;
                            if (!res || typeof res !== 'object') {
                                console.error('Invalid products response:', res);
                                loadProductsFailureUI(url, tryNo, null, 'Invalid response format');
                                return;
                            }
                            products = res.products && res.products.data ? res.products.data : (res
                                .products || res.data || null);

                            if (!Array.isArray(products)) {
                                console.error('Products array missing in response:', res);
                                loadProductsFailureUI(url, tryNo, null,
                                    'Products not found in response');
                                return;
                            }

                            // build html
                            let productsHtml = '';
                            products.forEach(function(product) {
                                let a = "{{ image('') }}";
                                let p = product.image || '';
                                let image = a.replace(
                                    'theme/frontend/assets/img/default/book.png', p ||
                                    'theme/frontend/assets/img/default/book.png');

                                productsHtml += `<div class="product-card  bg-white rounded-3 m-3 d-flex p-3 " data-product_id="${product.id}"
                        style="height: 110px; width:20%; cursor: pointer;">
                       <img class="img-fluid h-75 rounded col-lg-5  product-img" src="${image}" alt="img">
                             <div class="px-3 flex-grow-1">
                                 <p class="lh-sm text-sm fw-semibold">${stringShortner(product.name,15)}</p>
                                 <h1 class="text-sm lh-1 fw-semibold px-1 mt-1">${product.price}</h1>
                            </div>
                    </div> `;
                            });

                            $('#product-list').html(productsHtml);

                            // bind click handlers (same as before)
                            $(document).off('click', '.product-card').on('click', '.product-card',
                                function() {
                                    let productId = $(this).data('product_id');
                                    if (productExistsInCart(productId)) {
                                        let cart = window.getCart() || [];
                                        let newCart = cart.filter(item => !(item.parent_id ===
                                            productId || item.id === productId));
                                        window.setCart(newCart);
                                        loadCartItems();
                                        $(this).removeClass('active');
                                    } else {
                                        addToCart(productId);
                                        $(this).addClass('active');
                                    }
                                });

                        },
                        error: function(xhr, textStatus, errorThrown) {
                            // remove retry notice if present
                            $('#product-retry-notice').remove();

                            console.error('Products AJAX error:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                textStatus: textStatus,
                                errorThrown: errorThrown
                            });

                            // If server error (5xx) or timeout, retry a bit (transient)
                            const isServerError = xhr.status >= 500 && xhr.status < 600;
                            const isTimeout = textStatus === 'timeout';
                            const shouldRetry = isServerError || isTimeout;

                            if (shouldRetry && tryNo < retries) {
                                // exponential-ish backoff
                                const wait = backoffMs * (tryNo + 1);
                                console.warn(
                                    `Transient error detected. Retrying loadProducts in ${wait}ms (attempt ${tryNo+1}/${retries})`
                                );
                                setTimeout(function() {
                                    attempt(tryNo + 1);
                                }, wait);
                                return;
                            }

                            // final failure -> show friendly UI with Retry button
                            loadProductsFailureUI(url, tryNo, xhr, errorThrown);
                        }
                    });
                } // attempt

                // start attempts
                attempt(0);
            }

            // helper: show error UI and attach click retry
            function loadProductsFailureUI(url, tryNo, xhr, err) {
                let message = 'Failed to load products.';
                if (xhr && xhr.status) {
                    message += ` (Server returned ${xhr.status})`;
                } else if (err) {
                    message += ` (${err})`;
                }
                const html = `
                    <div class="w-100 text-center text-danger p-4">
                        ${message}
                        <div class="mt-2">
                        <button id="retryProductsBtn" class="btn btn-sm btn-outline-primary">Retry</button>
                        </div>
                    </div>
                    `;
                $('#product-list').html(html);

                // wire retry click
                $(document).off('click', '#retryProductsBtn').on('click', '#retryProductsBtn', function() {
                    safeLoadProducts(url, {
                        retries: 2,
                        timeoutMs: 10000,
                        backoffMs: 400
                    });
                });

                // optional: also log server body if present
                try {
                    if (xhr && xhr.responseText) {
                        console.error('Server response body:', xhr.responseText);
                    }
                } catch (e) {}
            }
            // check product exists in cart
            // function productExistsInCart(productId) {
            //     let cart = window.getCart() || [];
            //     let exists = false;
            //     cart.forEach(element => {
            //         if (element.parent_id == productId || element.id == productId) {
            //             exists = true;
            //         }

            //     });
            //     return exists;
            // }




            // total items in cart
            function totalItemsInCart() {
                let cart = window.getCart() || [];
                $('#total_items').text(cart.length);
            }

            // addToCart (child products flow)
            function addToCart(productId) {
                let url = "{{ route('product.childProductList', ':parentId') }}".replace(':parentId', productId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        let products = res.products;
                        let cart = window.getCart() || [];
                        let newItems = products.map(function(product) {
                            product['quantity'] = 1;
                            return product;
                        });

                        // concat and set
                        window.setCart(cart.concat(newItems));
                        // append rows dynamically
                        newItems.forEach(function(product) {
                            let cartItemsHtml = `
                       <tr>
                                    <td class="d-flex align-items-center justify-content-start gap-2 text-secondary " style="padding-top:12px">
                                        <span class="order-item" title="${product.name}">${stringShortner(product.name,20)}
                                            </span>
                                    </td>
                                    <td class=" align-items-center text-center justify-content-center gap-2 text-secondary" style="padding-top:12px">
                                        <button class="bg-light  rounded-4 border-0 p-1 qty-button btn-minus">
                                            <iconify-icon icon="flowbite:minus-outline"
                                                class="menu-icon fs-7"></iconify-icon>
                                        </button>

                                        <span class="mx-3"><input class="form-control qty-input text-center text-black px-1 " type="number" value="${product.quantity}" min="1" style="min-width:30px !important; max-width:80px !important; height:35px; display:block; "></span>

                                        <button class="bg-light rounded-4 p-1 qty-button btn-plus">
                                            <iconify-icon icon="flowbite:plus-outline"
                                                class="menu-icon fs-7"></iconify-icon>
                                        </button>
                                    </td>
                                    <td class='' style="padding-top:12px">
                                        ${product.price}
                                    </td>
                                    <td class='' style="padding-top:12px">
                                        ${product.mrp}
                                    </td>
                                    <td class='product-price' style="padding-top:12px">
                                        ${(product.price * product.quantity).toFixed(2)}
                                    </td>

                                    <td style="padding-top:12px">
                                    <button class="icon product-delete" data-product_id="${product.id}"><iconify-icon icon="mdi:delete"
                                            class="menu-icon fs-7 text-danger"></button>
                                            </td>

                                </tr>`;
                            $('#cart_items').append(cartItemsHtml);
                        });

                        calculateSubtotal();
                        totalItemsInCart();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } // addToCart




            //=====coupon apply function=====
            $(document).on('click', '#coupon-save', async function() {

                const couponId = $('#coupon-select').val();
                if (!couponId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select Coupon',
                        text: 'Please select a coupon first.'
                    });
                    return;
                }

                // store globally (you already use window.coupon)
                window.coupon = couponId;
                const couponText = $('#coupon-select option:selected').text();
                // 🔹 set hidden fields
                $('#applied_coupon_id').val(couponId);
                $('#applied_coupon_code').val(couponText);

                const total = await calculateSubtotal();

                const couponDiscount = parseFloat($('#coupon_discount').val()) || 0;
                $('#applied_coupon_discount').val(couponDiscount);

                // close modal
                $('#couponModal').modal('hide');


            });



            $('#customer').on('change', customerLoyaltyPointsCalculate);

            window.redeem_point = false;
            // loyalty point apply
            $('#redeem_point').on('click', function() {

                let loyatly_discount = parseInt($('#avail_loyalty_discount').val()) || 0;

                $('#loyalty_discount').val(loyatly_discount);
                calculateSubtotal();



                if (loyatly_discount > 0) {
                    window.redeem_point = true;
                } else {
                    window.redeem_point = false;
                }
            });


            //===== discount save (tab-specific)====
            $('#discount-save').on('click', function() {
                let discountType = $('#discount-type').val();
                let discountAmount = parseFloat($('#discount-input').val()) || 0;
                sessionStorage.setItem(window.getDiscountKey(), JSON.stringify({
                    'type': discountType,
                    'value': discountAmount
                }));
                calculateSubtotal();
                $('#discountModal').modal('hide');
            });

            // shipping save (tab-specific)
            $('#shipping-save').on('click', function() {
                let shippingCharge = parseFloat($('#shipping-input').val()) || 0;
                $('#shipping').val(shippingCharge);
                sessionStorage.setItem(window.getShippingKey(), shippingCharge);
                calculateSubtotal();
                $('#shippingModal').modal('hide');
            });

            //===== checkout modal open setup======

            // ensure subtotal recalculated instantly and modal receives correct value
            // $(document).on('click', '.payment-modal-btn', function() {
            //     // recalc just before opening
            //     const total = parseFloat(calculateSubtotal()) || 0;
            //     $('#checkout_total_amount').val(total.toFixed(2));
            //     // reset paid and change
            //     $('#paid_amount').val('');
            //     $('#change_amount').val('0.00');
            //     // set payment_method from button data if present
            //     const payment = $(this).data('payment_method') ?? 'cash';
            //     $('#payment_method').val(payment);
            //     // optionally store current cart snapshot id for trace
            //     $('#paymentModal').data('pos_tab_id', window.getPosTabId());
            // });


            let selectedMethod = 'cash';

            $(document).on('click', '.payment-modal-btn', async function() {
                const total = await calculateSubtotal();
                $('#checkout_total_amount').val(total.toFixed(2));

                selectedMethod = $(this).data('method');
                $('#paymentTable tbody').html('');
                addPaymentRow(selectedMethod, total);
                recalcChangeAmount();

                $('#paymentModal').data('pos_tab_id', window.getPosTabId());
            });


            //=======add payment  row function=====
            window.PAYMENT_TYPES = @json($paymentTypes);

            function addPaymentRow(selectedMethod = 'cash') {

                let options = '';
                window.PAYMENT_TYPES.forEach(pt => {
                    options += `<option value="${pt.id}" 
                    ${pt.slug === selectedMethod ? 'selected' : ''}>
                    ${pt.name}
                    </option>`;
                });

                $('#paymentTable tbody').append(`
                                <tr>
                                    <td>
                                        <select class="form-control form-control-sm payment-method">
                                            ${options}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm payment-amount"
                                            min="0" step="0.01">
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-row">×</button>
                                    </td>
                                </tr>
                            `);
            }

            //change amount re calculate function
            function recalcChangeAmount() {

                const saleTotal = parseFloat($('#checkout_total_amount').val()) || 0;

                let totalPaid = 0;

                $('#paymentTable tbody tr').each(function() {
                    const amt = parseFloat($(this).find('.payment-amount').val()) || 0;
                    totalPaid += amt;
                });

                let change = totalPaid - saleTotal;
                // if (change < 0) change = 0;

                $('#change_amount').val(change.toFixed(2));
            }

            $(document).on('input', '.payment-amount', function() {
                recalcChangeAmount();
            });


            $('#addPaymentRow').on('click', function() {
                addPaymentRow();
                recalcChangeAmount();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcChangeAmount();
            });


            // customer create (unchanged)
            $('#customer_form').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: '{{ route('customer.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        $('#customer').append(new Option(res.name, res.id, true, true)).trigger(
                            'change');
                        $('#customerModal').modal('hide');
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            });

            // listen cross-tab if you want (optional): we won't sync cart across tabs, but you may listen to other custom events
            window.addEventListener('storage', function(e) {
                // if someone else cleared or updated the same tab-key (unlikely), reload UI
                if (e.key && e.key.startsWith('cart_')) {
                    // only refresh if the key belongs to this tab to avoid cross-tab pollution
                    if (e.key === ('cart_' + window.getPosTabId())) {
                        loadCartItems();
                    }
                }
            });

            // ================= Resume Sale if resume_sale_id present ===================
            const params = new URLSearchParams(window.location.search);
            const resumeSaleId = params.get('resume_sale_id');

            if (resumeSaleId) {
                resumeSale(resumeSaleId);
            }

        }); // document.ready



        // small helpers outside ready
        $(document).on('click', '.nav-btn', function(e) {
            $('.nav-btn').removeClass('active');
            $(this).addClass('active');
        });
        $(document).on('click', '.product-card', function(e) {
            $(this).toggleClass('active');
        });

        window.S2 && window.S2.auto && window.S2.auto();




        // ================= POS SALE SUBMISSION ===================
        window.AUTH_BRANCH_ID = {{ auth()->user()->branch_id ?? 1 }};
        window.AUTH_WAREHOUSE_ID = {{ auth()->user()->warehouse_id ?? 1 }};


        $(document).on('click', '#pos-submit-sale', function() {

            // -----------------------------
            // 1️⃣ Build ITEMS from cart
            // -----------------------------
            let cart = window.getCart() || [];

            if (cart.length === 0) {
                // alert('Cart is empty');
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Cart',
                    text: 'Cannot proceed with an empty cart.'
                });
                return;
            }

            let items = cart.map(item => {
                return {
                    product_id: item.product_id ?? item.id,
                    product_variant_id: item.product_variant_id ?? null,
                    unit_id: item.unit_id ?? null,
                    quantity: parseFloat(item.quantity),
                    unit_price: parseFloat(item.price),
                    discount_amount: 0,
                    tax_amount: 0
                };
            });

            // -----------------------------
            // 2️⃣ Build PAYMENTS (METHOD BASED)
            // -----------------------------
            let total = parseFloat($('#checkout_total_amount').val()) || 0;
            let couponId = $('#applied_coupon_id').val() || null;
            let couponCode = $('#applied_coupon_code').val() || null;
            let couponDiscount = parseFloat($('#applied_coupon_discount').val()) || 0;

            let payments = [];

            $('#paymentTable tbody tr').each(function() {
                const method = $(this).find('.payment-method').val(); // cash/bkash/card
                const amt = parseFloat($(this).find('.payment-amount').val()) || 0;

                if (method && amt > 0) {
                    payments.push({
                        method: method,
                        amount: amt,
                        received_by: $('#payment_receiver').val() || 'POS',
                        note: ''
                    });
                }
            });

            // -----------------------------
            // 3️⃣ Totals
            // -----------------------------
            let payload = {
                branch_id: window.AUTH_BRANCH_ID,
                warehouse_id: window.AUTH_WAREHOUSE_ID,

                resume_sale_id: window.CURRENT_RESUME_SALE_ID ?? null,
                customer_id: $('#customer').val() || null,

                sale_type: 'retail',
                status: $('#sale_status').val(),

                subtotal: parseFloat($('#subtotal').text()),
                discount: parseFloat($('#discount').val()) || 0,

                coupon_id: couponId,
                coupon_code: couponCode,
                coupon_discount: couponDiscount,
                redeem_point: window.redeem_point ?? false,

                tax_amount: 0,
                shipping_charge: parseFloat($('#shipping').val()) || 0,

                total: total,

                sale_note: $('#sale_note').val() || 'POS sale',

                items: items,
                payments: payments
            };

            // -----------------------------
            // 4️⃣ Submit AJAX
            // -----------------------------
            $.ajax({
                url: "{{ route('pos.sales.store') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-POS-SESSION': window.getPosTabId()
                },
                data: JSON.stringify(payload),
                contentType: "application/json",
                success: function(res) {
                    customerLoyaltyPointsCalculate();
                    // alert('Sale completed. Invoice: ' + res.invoice);
                    Swal.fire({
                        title: 'Sale Completed',
                        html: `Invoice: <strong>${res.invoice}</strong>`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Print Invoice',
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let invoiceUrl = "{{ route('pos.sales.invoice', ':id') }}".replace(
                                ':id', res.id);

                            window.open(invoiceUrl, '_blank');
                        }
                    });

                    // -----------------------------
                    // 5️⃣ Reset POS
                    // -----------------------------
                    window.clearCartForTab();
                    $('#paid_amount').val('');
                    $('#checkout_total_amount').val('');
                    $('#change_amount').val('0.00');

                    $('#paymentModal').modal('hide');

                    // reload UI
                    loadCartItems();
                    emptyCart();
                },
                error: function(xhr) {

                    let msg = 'Something went wrong';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // ================= POS HOLD SALE ===================

        $(document).on('click', '#hold-sale', function() {

            let cart = window.getCart() || [];
            if (cart.length === 0) {
                Swal.fire('Empty Cart', 'Nothing to hold', 'info');
                return;
            }

            let payload = {
                branch_id: window.AUTH_BRANCH_ID,
                warehouse_id: window.AUTH_WAREHOUSE_ID,
                customer_id: $('#customer').val() || null,
                subtotal: $('#subtotal').text(),
                discount: $('#discount').val() || 0,
                tax_amount: 0,
                shipping_charge: $('#shipping').val() || 0,
                total: $('#total_amount').text(),
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.price
                }))
            };

            $.ajax({
                url: "{{ route('pos.sales.hold') }}",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {

                    Swal.fire('On Hold', 'Sale has been put on hold', 'success');

                    // reset POS
                    window.clearCartForTab();
                    loadCartItems();
                    emptyCart();
                    $('#customer').val('1').trigger('change');
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed', 'error');
                }
            });
        });

        $(document).on('click', '#view-hold-sales', function() {

            $('#holdSalesModal').modal('show');

            $.get("{{ route('pos.sales.hold.list') }}", function(res) {

                let html = '';

                if (res.data.length === 0) {
                    html = `<tr><td colspan="4" class="text-center text-muted">No hold orders</td></tr>`;
                }

                res.data.forEach(row => {
                    html += `
                <tr>
                    <td class="text-center fw-semibold">${row.invoice_no}</td>
                    <td>${row.customer ?? 'Walk-in'}</td>
                    <td>${row.quantity}</td>
                    <td>${row.unit_price}</td>
                    <td class="text-center fw-semibold">${row.total}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-success  mt-4 btn-resume-sale"
                            data-id="${row.id}" title="Resume Sale"><iconify-icon icon="mdi:play-circle"
                                class="menu-icon fs-5"></iconify-icon>
                            
                        </button>
                    </td>
                </tr>
            `;
                });

                $('#hold-sales-body').html(html);
            });
        });

        // $(document).on('click', '.resume-sale', function() {

        //     let saleId = $(this).data('id');
        //     window.CURRENT_RESUME_SALE_ID = saleId;

        //     $.get("{{ route('pos.sales.resume', ':id') }}".replace(':id', saleId), function(res) {

        //         let sale = res.sale;

        //         // clear current cart first
        //         window.clearCartForTab();

        //         // rebuild cart
        //         let items = sale.items.map(i => ({
        //             id: i.product_id,
        //             product_id: i.product_id,
        //             name: i.product?.name ?? 'Unknown Product',
        //             price: parseFloat(i.unit_price),
        //             mrp: parseFloat(i.product?.mrp ?? i.unit_price),
        //             quantity: parseFloat(i.quantity)
        //         }));

        //         window.setCart(items);
        //         loadCartItems();

        //         // 3️⃣ ✅ Restore customer (SELECT2 – CORRECT WAY)
        //         if (sale.customer_id && sale.customer_name) {

        //             let option = new Option(
        //                 sale.customer_name, // text
        //                 sale.customer_id, // value
        //                 true, 
        //                 true 
        //             );

        //             $('#customer')
        //                 .append(option)
        //                 .trigger('change'); 
        //         } else {
        //             // Walk-in customer
        //             $('#customer').val(null).trigger('change');
        //         }
        //         // restore discount
        //         sessionStorage.setItem(
        //             window.getDiscountKey(),
        //             JSON.stringify({
        //                 type: 'flat', // or percentage if you store it
        //                 value: parseFloat(sale.discount || 0)
        //             })
        //         );

        //         // restore shipping
        //         sessionStorage.setItem(
        //             window.getShippingKey(),
        //             parseFloat(sale.shipping_charge || 0)
        //         );

        //         // recalc totals
        //         calculateSubtotal();

        //         $('#holdSalesModal').modal('hide');
        //         $('#todayOrdersModal').modal('hide');

        //         Swal.fire('Resumed', 'Sale loaded successfully', 'success');

        //     });

        // });

        // ================= RESUME SALE ===================
        $(document).on('click', '.btn-resume-sale', function() {

            let saleId = $(this).data('id');
            window.CURRENT_RESUME_SALE_ID = saleId;
            resumeSale(saleId);
        });

        function resumeSale(saleId) {

            $.get("{{ route('pos.sales.resume', ':id') }}".replace(':id', saleId), function(res) {

                let sale = res.sale;
                window.CURRENT_RESUME_SALE_ID = saleId;
                // 1️⃣ Clear current cart
                window.clearCartForTab();


                // 2️⃣ Restore cart items
                let items = sale.items.map(i => ({

                    id: i.product_id,
                    parent_id: i.product?.parent_id ?? null, // 🔥 ADD THIS
                    product_id: i.product_id,

                    name: i.product?.name ?? 'Unknown Product',
                    price: parseFloat(i.unit_price),
                    mrp: parseFloat(i.product?.mrp ?? i.unit_price),

                    quantity: parseFloat(i.quantity)
                }));

                window.setCart(items);
                loadCartItems();

                // 3️⃣ ✅ Restore customer (SELECT2 – CORRECT WAY)
                if (sale.customer_id && sale.customer_name) {

                    let option = new Option(
                        sale.customer_name, // text
                        sale.customer_id, // value
                        true,
                        true
                    );

                    $('#customer')
                        .append(option)
                        .trigger('change');
                } else {
                    // Walk-in customer
                    $('#customer').val(null).trigger('change');
                }

                // 4️⃣ Restore discount & shipping
                sessionStorage.setItem(window.getDiscountKey(), JSON.stringify({
                    type: 'flat',
                    value: sale.discount
                }));

                sessionStorage.setItem(window.getShippingKey(), sale.shipping_charge);

                calculateSubtotal();
                $('#holdSalesModal').modal('hide');
                $('#todayOrdersModal').modal('hide');


                Swal.fire('Resumed', 'Sale loaded into POS', 'success');
            });
        }


        // ================= VIEW TODAY'S ORDERS ===================
        $(document).on('click', '#btn-view-orders', function() {

            $('#todayOrdersModal').modal('show');

            $('#todayOrdersBody').html(`
                    <tr>
                        <td colspan="6" class="text-center p-3">Loading...</td>
                    </tr>
                `);

            $.get("{{ route('pos.sales.today') }}", function(res) {

                if (!res.success || res.sales.length === 0) {
                    $('#todayOrdersBody').html(`
                            <tr>
                                <td colspan="6" class="text-center p-3">No orders today 💤</td>
                            </tr>
                        `);
                    return;
                }

                let rows = '';

                res.sales.forEach(sale => {

                    let actions = '';

                    actions += `
                            <button
                                class="btn btn-sm btn-outline-info AjaxViewModal"
                                data-ajax-modal="{{ route('pos.sales.show', ':id') }}"
                                data-id="${sale.id}"
                                data-size="lg"
                                title="View Details">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        `.replace(':id', sale.id);

                    if (sale.status === 'hold') {
                        actions += `
                            <button class="btn btn-sm btn-outline-primary btn-resume-sale"
                                data-id="${sale.id}"
                                title="Resume Sale">
                                <iconify-icon icon="mdi:play-circle-outline"></iconify-icon>
                            </button>
                        `;
                    }

                    if (sale.status === 'delivered') {
                        actions += `
                            <button class="btn btn-sm btn-outline-danger void-sale"
                                data-id="${sale.id}"
                                title="Void Sale">
                                <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                            </button>
                        `;
                    }

                    actions += `
                            <a class="btn btn-sm btn-outline-secondary"
                            target="_blank"
                            title="Print Invoice"
                            href="{{ route('pos.sales.invoice', ':id') }}">
                            <iconify-icon icon="mdi:printer-outline"></iconify-icon>
                            </a>
                        `.replace(':id', sale.id);

                    let statusBadge = '';

                    if (sale.status === 'hold') {
                        statusBadge = `<span class="badge bg-warning text-dark">HOLD</span>`;
                    } else if (sale.status === 'delivered') {
                        statusBadge = `<span class="badge bg-success">PAID</span>`;
                    }

                    rows += `
                        <tr class="align-middle text-sm">
                            <td class="fw-semibold">${sale.invoice}</td>
                            <td>${sale.customer}</td>
                            <td class="text-center">${sale.time}</td>
                            <td class="text-center fw-semibold">${parseFloat(sale.total).toFixed(2)}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-end  gap-1">
                                ${actions}
                            </td>
                        </tr>
                    `;
                });

                $('#todayOrdersBody').html(rows);
            });
        });

        // ================= VOID A SALE ===================
        $(document).on('click', '.void-sale', function() {

            let saleId = $(this).data('id');

            Swal.fire({
                title: 'Void this sale?',
                text: 'Stock will be reverted. This cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Void it',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('pos.sales.void', ':id') }}".replace(':id', saleId),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Voided!', res.message, 'success');
                        loadTodayOrders(); // reload modal list
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message || 'Unable to void sale',
                            'error'
                        );
                    }
                });
            });
        });

        // ================= POS RESET ===================

        $(document).on('click', '#pos-reset-btn', function() {

            Swal.fire({
                title: 'Start New Order?',
                text: 'Current cart, customer and discounts will be cleared.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Start New',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {

                if (!result.isConfirmed) return;

                // -----------------------------
                // 1️⃣ Clear cart (tab-specific)
                // -----------------------------
                window.clearCartForTab();

                // -----------------------------
                // 2️⃣ Clear customer (Walk-in)
                // -----------------------------
                $('#customer').val(null).trigger('change');


                // -----------------------------
                // 3️⃣ Clear discount & shipping
                // -----------------------------
                sessionStorage.removeItem(window.getDiscountKey());
                sessionStorage.removeItem(window.getShippingKey());

                $('#discount').val('0.00');
                $('#shipping').val('0.00');
                $('#discount-input').val('');
                $('#shipping-input').val('');

                // -----------------------------
                // 4️⃣ Reset totals UI
                // -----------------------------
                $('#subtotal').text('0.00');
                $('#total_amount').text('0.00');
                $('#total_items').text('0');

                // -----------------------------
                // 5️⃣ Reset payment modal fields
                // -----------------------------
                $('#paid_amount').val('');
                $('#checkout_total_amount').val('');
                $('#change_amount').val('0.00');

                // -----------------------------
                // 6️⃣ Reset product active state
                // -----------------------------
                $('.product-card').removeClass('active');

                // -----------------------------
                // 7️⃣ Reload cart UI (safe)
                // -----------------------------
                if (typeof loadCartItems === 'function') {
                    loadCartItems();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Ready',
                    text: 'New order started',
                    timer: 1000,
                    showConfirmButton: false
                });
            });
        });

        // ================= VIEW SALE DETAILS ===================
        // use global AJAXViewModal handler and show details in modal//


        // ================= VIEW TODAY'S TRANSACTIONS ===================
        $(document).on('click', '.btn-transactions', function() {

            $('#transactionsModal').modal('show');

            $('#transactionsBody').html(`
        <tr>
            <td colspan="4" class="text-center p-3">Loading...</td>
        </tr>
    `);

            $.get("{{ route('pos.transactions.today') }}", function(res) {

                if (!res.success || res.transactions.length === 0) {
                    $('#transactionsBody').html(`
                <tr>
                    <td colspan="6" class="text-center p-3">No transactions today</td>
                </tr>
            `);
                    return;
                }

                let rows = '';
                let cash = 0,
                    card = 0,
                    bkash = 0,
                    grand = 0;

                res.transactions.forEach(t => {

                    let amt = parseFloat(t.amount);
                    grand += amt;

                    if (t.method === 'cash') cash += amt;
                    if (t.method === 'card') card += amt;
                    if (t.method === 'bkash') bkash += amt;

                    rows += `
                <tr class="text-sm">
                    <td>${t.time}</td>
                    <td>${t.invoice}</td>
                    <td class="text-capitalize">${t.method}</td>
                    <td>${t.user}</td>
                    <td>${t.branch}</td>
                    <td class="text-end fw-semibold"> ${amt.toFixed(2)}</td>
                </tr>
            `;
                });

                $('#transactionsBody').html(rows);

                $('#cashTotal').text(cash.toFixed(2));
                $('#cardTotal').text(card.toFixed(2));
                $('#bkashTotal').text(bkash.toFixed(2));
                $('#grandTotal').text(grand.toFixed(2));
            });
        });

        // ================= SCAN BARCODE ===================
        let beep = new Audio('/sounds/beep.mp3');

        // Scan button → focus only
        $(document).on('click', '#btn-scan', function() {
            $('#barcodeInput').val('').focus();
        });

        // Scanner input
        $(document).on('keydown', '#barcodeInput', function(e) {

            if (e.key !== 'Enter') return;

            e.preventDefault();

            let barcode = $(this).val().trim();
            if (!barcode) return;

            handleBarcodeScan(barcode);
        });

        function customerLoyaltyPointsCalculate() {
            let customerId = $('#customer').val();
            console.log('Selected customer ID:', customerId); // Debug log
            $.ajax({
                url: "{{ route('loyalty.userLoyaltyPoints', ':customerId') }}".replace(
                    ':customerId', customerId),
                method: 'GET',
                success: function(res) {
                    $('#loyalty_point').text(res.points || 0);
                    $('#avail_loyalty_discount').val(parseInt(res.discount) || 0);
                    $('#customer_point').text($('#customer option:selected').text() || '');
                    $('#loyalty_discount').val(0);
                    calculateSubtotal();

                },
                error: function(err) {
                    console.log(err);
                }
            });

        }

        // Core handler
        function handleBarcodeScan(barcode) {

            $.get("{{ route('pos.product.byBarcode') }}", {
                    barcode
                }, function(res) {

                    if (!res || !res.success || !res.product) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Found',
                            text: 'Product not found for barcode: ' + barcode
                        });
                        $('#barcodeInput').val('').focus();
                        return;
                    }

                    let product = res.product;

                    if (productExistsInCart(product.id)) {
                        incrementQty(product.id);
                    } else {
                        addProductToCart(product);
                    }

                    loadCartItems();
                    beep.play();

                    // Ready for next scan
                    $('#barcodeInput').val('').focus();
                })
                .fail(function() {
                    Swal.fire('Error', 'Server error while scanning', 'error');
                    $('#barcodeInput').val('').focus();
                });
        }

        // Increment qty
        function incrementQty(productId) {
            let cart = window.getCart() || [];

            cart.forEach(item => {
                if (
                    item.id == productId ||
                    item.product_id == productId ||
                    item.parent_id == productId
                ) {
                    item.quantity += 1;
                }
            });

            window.setCart(cart);
        }

        // Add new product
        function addProductToCart(product) {
            let cart = window.getCart() || [];

            cart.push({
                id: product.id, // for cart
                product_id: product.id, // 🔥 IMPORTANT (POS consistency)
                name: product.name,
                price: parseFloat(product.price),
                mrp: parseFloat(product.mrp),
                unit_id: product.unit_id ?? null,
                quantity: 1
            });

            window.setCart(cart);
        }
    </script>
@endsection
