@extends('backend.layouts.master')
@section('content')
    <div class="row ">

        <div class="product-div col-lg-7 bg-gray " style="height: 80vh; overflow-y: auto;">
            <div class="row justify-content-between">
                {{-- welcome div --}}
                <div class="col-lg-4 mt-1">
                    <h6 class="text-xl lh-1 fw-semibold">Welcome, {{ Auth::user()->name }}</h6>
                    <p class="text-sm">{{ now()->format('l, d M Y') }} </p>
                </div>
                {{-- input and buttons --}}
                <div class="col-lg-6 d-flex justify-content-end align-items-center gap-2">
                    <input class="border  px-3 fst-italic rounded bg-light form-control" id="product-search-input"
                        type="text" placeholder="search product" style="width: 240px;">

                    <button class="btn btn-dark btn-sm px-3 text-xs d-flex align-items-center justify-content-center gap-1">
                        {{-- <iconify-icon icon="flowbite:tag-outline" class="menu-icon fs-6"></iconify-icon> --}}
                        <span>All Brands</span>
                    </button>

                    {{-- <button class="btn btn-primary btn-sm px-3 text-xs">Featured</button> --}}
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
                <h1 class="text-xl lh-1 fw-semibold p-1">Purchase List</h1>

                <div>
                    <h6 class="text-xs   p-1 px-3 bg-dark text-white rounded-pill">#ord1247</h6>
                </div>
            </div>
            <hr class=" px-3" style="border-top: 1px dashed #000;">
            <div class="p-1 mt-3">
                <h1 class="text-lg lh-1 fw-semibold p-1">Supplier's Information</h1>
                <div class="d-flex gap-2">

                    <div class="col-lg-9 d-flex gap-2">
                        <div class="mt-1">
                            <select class="form-control form-control-sm col-lg-3 js-s2-ajax" name="supplier_id"
                                id="supplier" data-url="{{ route('supplier.select2') }}"
                                data-placeholder="Select Supplier">
                                <option id="recent" value="" selected></option>

                            </select>
                            <div class="invalid-feedback d-block category_id-error" style="display:none"> </div>
                        </div>
                        <div class="p- my-1 d-flex gap-2">
                            <button class="btn btn-success rounded-1 btn-sm AjaxModal"
                                data-ajax-modal="{{ route('supplier.createModal') }}" data-size="lg"
                                data-onload="CategoryIndex.onLoad" data-onsuccess="CategoryIndex.onSaved"> <iconify-icon
                                    icon="flowbite:users-outline" class="menu-icon"></iconify-icon></button>
                            <button class="btn btn-primary rounded-1 btn-sm"><iconify-icon icon="mdi:qrcode-scan"
                                    class="menu-icon"></iconify-icon></button>
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 py-1 px-3">
                        <label for="warehouse_id" class="form-label">Warehouse</label>
                        <select class="form-control form-control-sm col-lg-3 purchase-ware-select js-s2-ajax"
                            name="warehouse_id" id="warehouse" data-url="{{ route('inventory.warehouses.select2') }}"
                            data-placeholder="Select warehouse">
                            <option id="recent" value="" selected></option>

                        </select>
                        <div class="invalid-feedback d-block category_id-error" style="display:none"> </div>
                    </div>
                    <div class="col-md-6 py-1 px-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select name="branch_id" id="branchSelect" class="form-control purchase-ware-select js-s2-ajax"
                            data-url="{{ route('org.branches.select2') }}" data-placeholder="Select branch">
                        </select>
                    </div>

                    <div class="col-lg-6 py-1 px-3">
                        <label for="warehouse_id" class="form-label">Order Status</label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="draft" selected>Draft</option>
                            {{-- <option value="received">Received</option> --}}
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label for="" class="form-label">Purchase Date</label>
                        <input type="date" lang="en-GB" class="form-control form-control-sm" name="purchase_date">
                    </div>
                    <div class="col-lg-6">
                        <label for="" class="form-label">Reference</label>
                        <input type="text" class="form-control form-control-sm" name="reference"
                            placeholder="Ruhul Amin">
                    </div>
                    <div class="col-lg-6 mt-1">
                        <label for="purchase_invoice" class="form-label">Invoice</label>
                        <input type="file" class="form-control form-control-sm p-1" name="purchase_invoice"
                            id="purchase_invoice">
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
                        <button class="btn btn-outline-danger empty-cart btn-xs py-1 text-xs">Clear all</button>
                    </div>

                </div>
                {{-- table --}}
                <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-borderless text-gray scrollable-table ">
                        <thead class="text-sm  fw-semibold">
                            <tr class="table-light rounded-3 px-1">

                                <th scope="col" class="text-center">Item</th>
                                <th scope="col" class="text-center">Stock Qty</th>
                                <th scope="col" class="text-center">Quantity</th>
                                <th scope="col" class="text-center">Price</th>
                                <th scope="col" class="text-center">Cost</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm " id="purchase_items">


                        </tbody>
                    </table>


                </div>

                {{-- <div class="d-flex justify-content-between mt-3 mb-0 rounded-3 p-3 "
                    style="background:#e9e0ef;border:1px solid #8035ba">
                    <div class="">
                        <h1 class="text-md lh-1 fw-semibold ">Discount 5%</h1>
                        <p class="text-sm ">For $20 Minimum Purchase, all Items</p>
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

                                    <!-- Modal -->

                                </td>
                                <td class="text-secondary "><input type="number" id="shipping" min="0"
                                        class="border-1 rounded py-1 " value="0" style="width:120px; height:35px">
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
                                                    <input type="number" min="0" class="form-control "
                                                        id="discount-input">
                                                </div>
                                                <div class="modal-footer ">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" data-bs-toggle="modal"
                                                        class="btn btn-dark btn-sm" id="discount-save">Save</button>
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

                {{-- <div class="p-3 border border-danger bg-light rounded">
                    <h1 class="text-md lh-1 fw-semibold">Select Payment Method</h1>
                    <div class="row gap-3  mt-5 px-3 justify-content-center">
                       
                        <button type="button" class=" btn col-lg-3 payment-btn btn-sm payment-modal" id="btn-cash" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap" data-payment_mode="cash">Cash</button>
                        <button type="button" class=" btn col-lg-3 payment-btn payment-modal btn-sm" id="btn-cash" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap" data-payment_mode="bkash">Bkash</button>
                        <button type="button" class=" btn col-lg-3 payment-btn payment-modal btn-sm" id="btn-cash" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap" data-payment_mode="card">Card</button>
                       

                    </div>
                </div> --}}


                <div class="d-flex justify-content-center mt-3  mb-3">
                    {{-- <button style="padding: 10px"
                        class="btn btn-light border col-lg-6   d-flex align-items-center justify-content-center gap-2"><iconify-icon
                            icon="flowbite:printer-outline" class="menu-icon fs-5 "></iconify-icon><span>Print
                            Order</span>
                    </button> --}}
                    <button data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap"
                        class="btn btn-danger payment-modal border p-3  col-lg-10  d-flex align-items-center justify-content-center "><iconify-icon
                            icon="flowbite:cart-outline" class="menu-icon fs-5 "></iconify-icon><span>Purchase</span>
                    </button>

                </div>
                {{-- checkout form modal --}}
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Checkout</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="mb-3 col-lg-4">
                                            <label for="total_amount" class="col-form-label">Total
                                                Amount</label>
                                            <input type="number" name="total_amount" disabled
                                                class="form-control form-control-sm" id="checkout_total_amount"
                                                value="0">
                                        </div>
                                        <div class="mb-3 col-lg-4">
                                            <label for="paid_amount" class="col-form-label">Paid
                                                Amount</label>
                                            <input type="number" name="paid_amount" min="0"
                                                class="form-control form-control-sm" id="paid_amount" value="0">
                                        </div>
                                        <div class="mb-3 col-lg-4">
                                            <label for="due_amount" class="col-form-label">Due Amount</label>
                                            <input type="number" name="due_amount" disabled
                                                class="form-control form-control-sm" id="due_amount" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3 ">
                                        <label for="payment_type" class="col-form-label">Payment Type</label>
                                        <select name="payment_type" id="payment_type"
                                            class="form-control form-control-sm">
                                            <option value="cash" selected>Cash</option>
                                            <option value="card">Card</option>
                                            <option value="bkash">Bkash</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 ">
                                        <label for="payment_receiver" class="col-form-label">Payment
                                            Receiver</label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="payment_receiver" id="recipient-name">
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

            </div>
        </div>

    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            // -----------------------
            // CONFIG: replace if needed
            // -----------------------
            const PURCHASE_STORE_URL =
                "{{ route('purchase.orders.store') }}"; // <-- change to your real store route (e.g. "{{ route('purchase.orders.store') }}")
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                ''; // ensure <meta name="csrf-token" content="{{ csrf_token() }}">
            // -----------------------

            // helper: route config (you can inject real routes here)
            window.routes = {
                productsList: "{{ route('product.productsList') }}",
                productsSearch: "{{ route('product.productsSearch', ':name') }}",
                productsByCategory: "{{ route('product.productsByCategory', ':category') }}",
                childProductList: "{{ route('product.childProductList', ':parentId') }}",
                supplierRecent: "{{ route('supplier.recent') }}",
                // add more if you want
            };

            // debounce util
            function debounce(fn, delay) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // -----------------------
            // CART: canonical fields
            // We standardize on `cost_price` and `quantity`
            // Cart stored in localStorage as array of items
            // -----------------------

            function loadPurchaseItems() {
                let purchaseItems = JSON.parse(localStorage.getItem('purchaseCart')) || [];
                let count = 0;
                let purchaseItemsHtml = '';

                purchaseItems.forEach(function(item) {
                    count += 1;
                    // ensure numeric types
                    const qty = parseFloat(item.quantity) || 1;
                    const unit = parseFloat(item.cost_price) || 0;
                    const line = (unit * qty).toFixed(2);

                    purchaseItemsHtml += `
                   <tr data-product_id="${item.id}">
                        <td class="d-flex align-items-center text-center justify-content-start gap-2 text-secondary ">
                            <span class="order-item">${shortName(item.name)}</span>
                            <button class="icon product-delete" data-product_id="${item.id}" type="button">
                                <iconify-icon icon="mdi:delete" class="menu-icon fs-7 "></iconify-icon>
                            </button>
                        </td>
                        <td class="text-center">${item.stock_quantity ?? '-'}</td>
                        <td class=" align-items-center justify-content-center text-center gap-2 text-secondary">
                            <button class="bg-light rounded-4 border-0 p-1 qty-button btn-minus" type="button">
                                <iconify-icon icon="flowbite:minus-outline" class="menu-icon fs-7"></iconify-icon>
                            </button>
                            <span class="mx-3">
                                <input class="form-control qty-input text-center text-black px-1" type="number" value="${qty}" min="1" style="min-width:30px; max-width:80px; height:35px; display:block;">
                            </span>
                            <button class="bg-light rounded-4 p-1 qty-button btn-plus" type="button">
                                <iconify-icon icon="flowbite:plus-outline" class="menu-icon fs-7"></iconify-icon>
                            </button>
                        </td>
                        <td>
                             <input type="number" name="item_price" class="item_price form-control form-control-sm" value="${unit}" style="width:80px;">
                        </td>
                        <td class="product-price">${line}</td>
                   </tr>`;
                });

                $('#purchase_items').html(purchaseItemsHtml);
                $('#total_items').text(count);
                calculateSubtotal();
            }

            // -----------------------
            // CART helpers (atomic, consistent)
            // -----------------------
            function getCart() {
                return JSON.parse(localStorage.getItem('purchaseCart')) || [];
            }

            function setCart(cart) {
                localStorage.setItem('purchaseCart', JSON.stringify(cart));
            }

            function productExistsInCart(productId) {
                const cart = getCart();
                return cart.some(element => (element.parent_id && element.parent_id == productId) || element.id ==
                    productId);
            }

            function totalItemsInCart() {
                const cart = getCart();
                $('#total_items').text(cart.length);
            }

            function updateProductPrice(productId, price) {
                const cart = getCart();
                let product = null;
                const newCart = cart.map(p => {
                    if (p.id === productId) {
                        p.cost_price = parseFloat(price) || 0;
                        product = p;
                    }
                    return p;
                });
                setCart(newCart);
                calculateSubtotal();
                return product;
            }

            function updateQuantityInCart(productId, newQuantity) {
                const cart = getCart();
                const q = Math.max(1, parseInt(newQuantity) || 1);
                cart.forEach(item => {
                    if (item.id === productId) item.quantity = q;
                });
                setCart(cart);
            }

            function deleteFromPurchaseCart(productId) {
                const cart = getCart();
                const updated = cart.filter(i => i.id !== productId);
                setCart(updated);
                calculateSubtotal();
                loadPurchaseItems();
            }

            function emptyCart() {
                localStorage.removeItem('purchaseCart');
                localStorage.setItem('discount', JSON.stringify({
                    type: 'flat',
                    value: 0.00
                }));
                localStorage.setItem('shippingCharge', 0);
                loadPurchaseItems();
                $('.product-card').removeClass('active');
            }

            // -----------------------
            // SUBTOTAL / TOTAL calculation (robust)
            // -----------------------
            function calculateSubtotal() {
                const cart = getCart();
                const discountObj = JSON.parse(localStorage.getItem('discount')) || {
                    type: 'flat',
                    value: 0
                };
                const shipping = parseFloat(localStorage.getItem('shippingCharge')) || 0;
                let subtotal = 0;
                cart.forEach(item => {
                    subtotal += (parseFloat(item.cost_price || 0) * parseFloat(item.quantity || 0));
                });

                let discountAmount = 0;
                if (discountObj && Number(discountObj.value)) {
                    if (discountObj.type === 'percentage') {
                        discountAmount = (subtotal * parseFloat(discountObj.value) / 100);
                    } else {
                        discountAmount = parseFloat(discountObj.value) || 0;
                    }
                }

                let total = subtotal + shipping - discountAmount;
                if (total < 0) total = 0;

                $('#shipping').val(shipping);
                $('#subtotal').text(subtotal.toFixed(2));
                $('#discount').val(discountAmount.toFixed(2));
                $('#total_amount').text(total.toFixed(2));

                return parseFloat(total.toFixed(2));
            }

            // -----------------------
            // Product list loading (AJAX)
            // -----------------------
            function loadProducts(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        let products = res.products?.data || res.products || [];
                        let productsHtml = '';
                        products.forEach(function(product) {
                            let base =
                                "{{ image('') }}"; // keep same approach (blade replacement)
                            let p = product.image || '';
                            let image = base.replace(
                                'theme/frontend/assets/img/default/book.png', p || base);

                            productsHtml += `<div class="product-card bg-white rounded-3 m-3 d-flex p-3" data-product_id="${product.id}" style="height:110px; width:20%; cursor:pointer;">
                                <img class="w-50 h-75 rounded product-img my-1" src="${image}" alt="img">
                                <div class="px-3">
                                    <p class="fw-semibold lh-sm text-sm">${shortName(product.name)}</p>
                                    <hr class="lh-sm">
                                    <h1 class="text-sm lh-1 fw-semibold p-1">${parseFloat(product.cost_price || 0).toFixed(2)}</h1>
                                </div>
                            </div>`;
                        });

                        $('#product-list').html(productsHtml);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // debounced search binding
            $('#product-search-input').on('input', debounce(function() {
                let query = $(this).val().trim();
                if (query.length > 0) {
                    let url = window.routes.productsSearch.replace(':name', encodeURIComponent(query));
                    loadProducts(url);
                } else {
                    loadProducts(window.routes.productsList);
                }
            }, 300));

            // category click (delegated)
            $(document).on('click', '.nav-btn', function() {
                $('.nav-btn').removeClass('active');
                $(this).addClass('active');

                const cid = $(this).data('category-id');
                if (!cid) {
                    loadProducts(window.routes.productsList);
                    return;
                }
                const url = window.routes.productsByCategory.replace(':category', cid);
                loadProducts(url);
            });

            // initial loads
            loadProducts(window.routes.productsList);
            loadPurchaseItems();

            // -----------------------
            // product card click: add/remove child products
            // -----------------------
            $(document).on('click', '.product-card', function() {
                const productId = $(this).data('product_id');
                const $card = $(this);

                if (productExistsInCart(productId)) {
                    // remove all matching items with parent_id === productId OR id === productId
                    let cart = getCart();
                    cart = cart.filter(item => !(item.parent_id == productId || item.id == productId));
                    setCart(cart);
                    loadPurchaseItems();
                    $card.removeClass('active');
                } else {
                    // fetch child products and append
                    const url = window.routes.childProductList.replace(':parentId', productId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(res) {
                            const products = res.products || res || [];
                            let cart = getCart();
                            const newItems = products.map(p => {
                                // normalize to our canonical shape
                                return {
                                    id: p.id,
                                    parent_id: productId,
                                    name: p.name,
                                    cost_price: parseFloat(p.cost_price || 0),
                                    quantity: 1,
                                    stock_quantity: p.stock_quantity || 0,
                                };
                            });
                            cart = cart.concat(newItems);
                            setCart(cart);
                            loadPurchaseItems();
                            $card.addClass('active');
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // -----------------------
            // quantity +/- handlers (use updated cart)
            // -----------------------
            $(document).on('click', '.btn-plus', function() {
                const tr = $(this).closest('tr');
                const productId = tr.data('product_id');
                let cart = getCart();
                const product = cart.find(i => i.id === productId);
                if (!product) return;
                product.quantity = (parseInt(product.quantity) || 0) + 1;
                setCart(cart);
                tr.find('.qty-input').val(product.quantity);
                tr.find('.product-price').html(`${(product.cost_price * product.quantity).toFixed(2)}`);
                calculateSubtotal();
            });

            $(document).on('click', '.btn-minus', function() {
                const tr = $(this).closest('tr');
                const productId = tr.data('product_id');
                let cart = getCart();
                const product = cart.find(i => i.id === productId);
                if (!product) return;
                if (product.quantity > 1) {
                    product.quantity = product.quantity - 1;
                    setCart(cart);
                    tr.find('.qty-input').val(product.quantity);
                    tr.find('.product-price').html(`${(product.cost_price * product.quantity).toFixed(2)}`);
                    calculateSubtotal();
                }
            });

            // qty input change
            $(document).on('input', '.qty-input', function() {
                const tr = $(this).closest('tr');
                const productId = tr.data('product_id');
                let newQuantity = parseInt($(this).val());
                if (isNaN(newQuantity) || newQuantity < 1) {
                    $(this).val(1);
                    newQuantity = 1;
                }
                updateQuantityInCart(productId, newQuantity);
                // update UI price
                const cart = getCart();
                const product = cart.find(i => i.id === productId);
                if (product) {
                    tr.find('.product-price').html((product.cost_price * product.quantity).toFixed(2));
                }
                calculateSubtotal();
            });

            // price input change
            $(document).on('input', '.item_price', function() {
                const tr = $(this).closest('tr');
                const newPrice = parseFloat($(this).val()) || 0;
                const productId = tr.data('product_id');
                const product = updateProductPrice(productId, newPrice);
                if (product) {
                    tr.find('.product-price').html((product.quantity * product.cost_price).toFixed(2));
                }
            });

            // delete individual
            $(document).on('click', '.product-delete', function() {
                const productId = $(this).data('product_id');
                deleteFromPurchaseCart(productId);
            });

            // clear cart
            // $(document).on('click', '.empty-cart', function() {
            //     // if (!confirm('Are you sure you want to empty the purchase cart?')) return;
            //     if(swal.fire({
            //         title: 'Are you sure?',
            //         text: "You won't be able to revert this!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Yes, clear it!'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             emptyCart();
            //             swal.fire('Cleared!', 'Purchase cart has been cleared.', 'success');
            //         }
            //     })) {
            //         return;
            //     }
            //     emptyCart();
            // });

            // clear cart (fixed confirm with SweetAlert)
            $(document).on('click', '.empty-cart', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will clear the purchase cart.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, clear it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        emptyCart();
                        Swal.fire('Cleared!', 'Purchase cart has been cleared.', 'success');
                    }
                });
            });

            // discount save
            $('#discount-save').on('click', function() {
                const discountType = $('#discount-type').val();
                const discountAmount = parseFloat($('#discount-input').val()) || 0;
                localStorage.setItem('discount', JSON.stringify({
                    type: discountType,
                    value: discountAmount
                }));
                calculateSubtotal();
                $('#exampleModalCenter').modal('hide');
            });

            // shipping input
            $('#shipping').on('input', function() {
                const shippingCharge = parseFloat($(this).val()) || 0;
                localStorage.setItem('shippingCharge', shippingCharge);
                calculateSubtotal();
            });

            // checkout modal prepare totals
            $(document).on('click', '.payment-modal', function() {
                const total = calculateSubtotal();
                $('#checkout_total_amount').val(total.toFixed(2));
            });

            $('#paid_amount').on('input', function() {
                const paid = parseFloat($(this).val()) || 0;
                const total = parseFloat($('#checkout_total_amount').val()) || 0;
                $('#due_amount').val((total - paid).toFixed(2));
            });


            // -----------------------
            // Branch-Warehouse linkage logic
            // -----------------------
            // Branch-Warehouse linkage logic
            // Helper: current branch id (returns integer or 0)
            const warehouseAjaxUrlTemplate =
                "{{ route('inventory.warehouses.showForAjax', ['warehouse' => ':id']) }}"; // <-- change to your real route
            const $branchSelect = $('#branchSelect'); // admin branch select (if any)
            const $branchHidden = $('input[name="branch"]'); // hidden branch input (if any)
            const $warehouse = $('#warehouse');

            function getCurrentBranchId() {
                if ($branchSelect.length && $branchSelect.val()) {
                    return +$branchSelect.val();
                }
                if ($branchHidden.length && $branchHidden.val()) {
                    return +$branchHidden.val();
                }
                return 0;
            }

            // Helper: current warehouse id
            function getCurrentWarehouseId() {
                return $warehouse.val() ? +$warehouse.val() : 0;
            }


            // ---- Select2 hook on warehouse: populate branch (from option or AJAX) ----
            $warehouse.on('select2:select', function(evt) {
                const data = evt.params ? evt.params.data : null;
                const wid = getCurrentWarehouseId();
                let branchFromOption = null;
                if (data && (data.branch_id !== undefined)) branchFromOption = data.branch_id;

                if (branchFromOption !== null) {
                    // if branch embedded in select2 option, use it

                    setBranch(branchFromOption, data.branch_name || null);
                    // update all sys-qtys
                    triggerSystemQtyRefresh();
                    return;
                }

                // fallback: call warehouse detail endpoint
                if (!wid) {
                    setBranch(null);
                    triggerSystemQtyRefresh();
                    return;
                }

                const url = warehouseAjaxUrlTemplate.replace(':id', wid);

                $.getJSON(url)
                    .done(function(res) {
                        const bId = res.branch_id ?? 0;
                        setBranch(bId, res.branch_name ?? null);
                    })
                    .fail(function(err) {
                        dbg('warehouse detail ajax failed', err);
                    })
                    .always(function() {
                        triggerSystemQtyRefresh();
                    });
            });

            // if warehouse cleared
            $warehouse.on('select2:clear', function() {
                setBranch(null);
                triggerSystemQtyRefresh();
            });

            // When branch (admin) manual change -> update system qtys
            $branchSelect.on('change', function() {
                triggerSystemQtyRefresh();
            });

            // setBranch: sets either hidden input or select value
            function setBranch(branchId, branchName = null) {
                if ($branchSelect.length) {
                    if (branchId) {
                        // ensure option exists then select
                        if ($branchSelect.find("option[value='" + branchId + "']").length === 0) {
                            const text = branchName ?? ('Branch ' + branchId);
                            const newOpt = new Option(text, branchId, true, true);
                            $branchSelect.append(newOpt);
                        }
                        $branchSelect.val(branchId).trigger('change');
                    } else {
                        $branchSelect.val(null).trigger('change');
                    }
                } else if ($branchHidden.length) {
                    if (branchId) $branchHidden.val(branchId);
                }
            }

            // trigger system qty refresh for all cart items
            function triggerSystemQtyRefresh() {
                const cart = getCart();
                cart.forEach(item => {
                    const pid = item.id;
                    const $tr = $(`#purchase_items tr[data-product_id="${pid}"]`);
                    if ($tr.length) {
                        // trigger a refresh by simulating a quantity input change
                        $tr.find('.qty-input').trigger('input');
                    }
                });
            }


            // -----------------------
            // SUBMIT / ASSEMBLE payload & POST
            // -----------------------
            // This function collects supplier/warehouse/inputs + items + payment and POSTs to server.
            function assemblePurchasePayload() {
                const supplierId = $('#supplier').val() || null;
                const warehouseId = $('#warehouse').val() || null;
                const branchId = $('#branchSelect').val() || null;
                const status = $('#status').val() || 'draft';
                const purchaseDate = $('input[name="purchase_date"]').val() || null;
                const reference = $('input[name="reference"]').val() || null;
                const shipping = parseFloat(localStorage.getItem('shippingCharge')) || 0;
                const discountObj = JSON.parse(localStorage.getItem('discount')) || {
                    type: 'flat',
                    value: 0
                };


                const items = getCart().map(i => ({
                    product_id: i.id,
                    sku: i.sku || null,
                    description: i.description || null,
                    unit_cost: parseFloat(i.cost_price || 0),
                    quantity: parseFloat(i.quantity || 0),
                }));
                const paymentAmount = parseFloat($('#paid_amount').val()) || 0;
                const payment = paymentAmount > 0 ? {
                    amount: paymentAmount,
                    method: $('#payment_type').val() || 'cash',
                    reference: $('#payment_reference').val() || null,
                    payment_date: new Date().toISOString(),
                    notes: $('#payment_note').val() || null,
                } : null;

                return {
                    supplier_id: supplierId,
                    warehouse_id: warehouseId,
                    branch_id: branchId,
                    status: status,
                    order_date: purchaseDate,
                    reference: reference,
                    shipping_amount: shipping,
                    discount: discountObj,
                    items: items,
                    payment: payment
                };
            }



            // Convert payload + file to FormData for multipart
            // function postPurchase(payload, invoiceFile = null, successCb = null, errorCb = null) {
            //     const fd = new FormData();
            //     fd.append('_token', CSRF_TOKEN);
            //     fd.append('payload', JSON.stringify(payload));
            //     if (invoiceFile) fd.append('purchase_invoice', invoiceFile);

            //     for (let pair of fd.entries()) {
            //         console.log('FormData entry:', pair[0], pair[1]);
            //     }

            //     $.ajax({
            //         url: PURCHASE_STORE_URL,
            //         method: 'POST',
            //         data: fd,
            //         processData: false,
            //         contentType: false,
            //         success: function(res) {
            //             if (successCb) successCb(res);
            //         },
            //         error: function(xhr) {
            //             if (errorCb) errorCb(xhr);
            //             else console.error(xhr.responseText || xhr);
            //         }
            //     });
            // }

            function postPurchase(payload, invoiceFile = null, successCb = null, errorCb = null) {
                const fd = new FormData();
                fd.append('_token', CSRF_TOKEN);
                fd.append('payload', JSON.stringify(payload));
                if (invoiceFile) fd.append('purchase_invoice', invoiceFile);

                // Optional: debug — remove in production
                // for (let pair of fd.entries()) {
                //     console.log('FormData entry:', pair[0], pair[1]);
                // }

                $.ajax({
                    url: PURCHASE_STORE_URL,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    success: function(res) {
                        if (successCb) successCb(res);
                    },
                    error: function(xhr) {
                        if (errorCb) errorCb(xhr);
                        else {
                            console.error(xhr.responseText || xhr);
                            const msg = xhr.responseJSON?.message || 'Server error';
                            Swal.fire('Error', msg, 'error');
                        }
                    }
                });
            }

            // Bind submit button on checkout modal to call postPurchase
            // $(document).on('click', '#submit_purchase_btn, .submit-purchase', function(e) {
            //     e.preventDefault();

            //     // basic client validation
            //     const cart = getCart();
            //     if (!cart.length) {
            //         // alert('Cart is empty. Add products first.');
            //         swal.fire('Error', 'Cart is empty. Add products first.', 'error');
            //         return;
            //     }
            //     const supplier = $('#supplier').val();
            //     if (!supplier) {
            //         // alert('Please choose a supplier.');
            //         swal.fire('warning', 'Please choose a supplier.', 'warning');
            //         return;
            //     }
            //     const warehouse = $('#warehouse').val();
            //     if (!warehouse) {
            //         swal.fire('warning', 'Please choose a warehouse.', 'warning');
            //         return;
            //     }

            //     const payload = assemblePurchasePayload();
            //     // invoice file input (if present)
            //     const invoiceInput = document.querySelector('input[name="purchase_invoice"]');
            //     const invoiceFile = invoiceInput?.files?.[0] ?? null;

            //     // disable button while processing
            //     const $btn = $(this);
            //     $btn.prop('disabled', true).text('Processing...');

            //     postPurchase(payload, invoiceFile, function(res) {
            //         // success
            //         $btn.prop('disabled', false).text('Submit');
            //         // clear local cart only if server responded ok
            //         if (res.success) {
            //             swal.fire('Success', res.message || 'Purchase created successfully.',
            //                 'success');
            //             emptyCart();
            //             // optional: redirect to purchase show page if returned
            //             if (res.redirect_url) swal.fire('Success',
            //                 'Redirecting to purchase details...', 'success').then(() => {
            //                 window.location.href = res.redirect_url;
            //             });
            //         } else {
            //             // alert(res.message || 'Server processed but returned no success flag.');
            //             swal.fire('Error', res.message ||
            //                 'Server processed but returned no success flag.', 'error');
            //         }
            //     }, function(xhr) {
            //         $btn.prop('disabled', false).text('Submit');
            //         const msg = xhr.responseJSON?.message ||
            //             'Failed to create purchase. Check console.';
            //         // alert(msg);
            //         swal.fire('Check Paid Amount', msg, 'error');
            //         console.error(xhr.responseText || xhr);
            //     });

            // });

            $(document).on('click', '#submit_purchase_btn, .submit-purchase', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const originalText = $btn.data('orig-text') || $btn.text();

                // basic client validation
                const cart = getCart();
                if (!cart.length) {
                    Swal.fire('Error', 'Cart is empty. Add products first.', 'error');
                    return;
                }
                const supplier = $('#supplier').val();
                if (!supplier) {
                    Swal.fire('Warning', 'Please choose a supplier.', 'warning');
                    return;
                }
                const warehouse = $('#warehouse').val();
                if (!warehouse) {
                    Swal.fire('Warning', 'Please choose a warehouse.', 'warning');
                    return;
                }

                const payload = assemblePurchasePayload();
                const invoiceInput = document.querySelector('input[name="purchase_invoice"]');
                const invoiceFile = invoiceInput?.files?.[0] ?? null;

                // disable button while processing
                $btn.prop('disabled', true).data('orig-text', originalText).text('Processing...');

                postPurchase(payload, invoiceFile, function(res) {
                    $btn.prop('disabled', false).text(originalText);
                    if (res.success) {
                        Swal.fire('Success', res.message || 'Purchase created successfully.',
                            'success');
                        emptyCart();
                        if (res.redirect_url) {
                            // small delay so user sees success
                            setTimeout(() => window.location.href = res.redirect_url, 600);
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Unknown response', 'error');
                    }
                }, function(xhr) {
                    $btn.prop('disabled', false).text(originalText);
                    const msg = xhr.responseJSON?.message ||
                        'Failed to create purchase. Check console.';
                    Swal.fire('Error', msg, 'error');
                    console.error(xhr);
                });
            });
            // -----------------------

            window.S2.auto();

            // small helper to load recent supplier (if you call it)
            function loadSupplier() {
                const url = window.routes.supplierRecent;
                $.get(url).done(function(res) {
                    if (res.supplier) {
                        $('#supplier').append(new Option(res.supplier.name, res.supplier.id, true, true))
                            .trigger('change');
                    }
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                });
            }

            // call loadSupplier on ready (optional)
            if ($('#supplier').length) loadSupplier();

            // helper shortName
            function shortName(name, wordLimit = 3) {
                if (!name) return '';
                let words = name.split(" ");
                if (words.length <= wordLimit) return name;
                return words.slice(0, wordLimit).join(" ") + " ...";
            }



        }); // document ready end
    </script>
@endsection
