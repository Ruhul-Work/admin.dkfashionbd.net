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
                    <input class="border  px-3 fst-italic rounded bg-light form-control form-control-sm" id="product-search-input"
                        type="text" placeholder="search product" style="width: 240px;">

                    <button class="btn btn-dark btn-sm px-3 text-xs d-flex align-items-center justify-content-center gap-1">
                        {{-- <iconify-icon icon="flowbite:tag-outline" class="menu-icon fs-6"></iconify-icon> --}}
                        <span>All Brands</span>
                    </button>

                    {{-- <button class="btn btn-primary btn-sm px-3 text-xs">Featured</button> --}}
                </div>


            </div>
            {{-- categories  --}}
            <div class="g-3 py-1 mt-3 category-nav">
                <button class="btn nav-btn active rounded-4 py-1 text-md" data-category-id="">All
                    Categories
                </button>
                @foreach ($categories->take(3) as $category)
                    <button class="btn nav-btn  rounded-4 py-1 text-md" data-category-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach

                @if ($categories->count() > 3)
                    <div class="dropdown">
                        <button class="btn nav-btn rounded-4 py-1 text-md dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" id="purchaseReturnMoreCategoriesBtn">
                            <span class="category-more-label">More</span>
                        </button>
                        <div class="dropdown-menu shadow-sm border-0">
                            @foreach ($categories->slice(3) as $category)
                                <button class="dropdown-item nav-btn category-overflow-item" type="button"
                                    data-category-id="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- products --}}
            <div class="products-div mt-3 row gap-space-between h-auto align-item-center justify-content-center"
                id="product-list">

            </div>


        </div>


        <div class="order-div col-lg-5 bg-white rounded-3 " style="height: 80vh; overflow-y: auto;">

            {{-- purchase form --}}
            @include('backend.modules.purchase_returns.form', [
                'purchase' => $purchase ?? null,
                'isEditable' => $isEditable ?? true,
            ])

        </div>

    </div>

    </div>
@endsection
@section('script')
    <script>
        $(function() {
            // -----------------------
            // CONFIG / ROUTES / TOKENS
            // -----------------------
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const PURCHASE_RETURN_STORE_URL = "{{ route('purchase.returnPurchaseOrder') }}";
            const stockCurrentsBulkUrl = "{{ route('inventory.adjustments.stock.currents.bulk') }}";

            window.routes = {
                productsList: "{{ route('product.productsList') }}",
                productsSearch: "{{ route('product.productsSearch', ':name') }}",
                productsByCategory: "{{ route('product.productsByCategory', ':category') }}",
                childProductList: "{{ route('product.childProductList', ':parentId') }}",
                supplierRecent: "{{ route('supplier.recent') }}",
            };

            const $warehouse = $('#warehouse');
            const $branchSelect = $('#branchSelect');
            const $branchHidden = $('input[name="branch"]');

            // debounce util
            function debounce(fn, delay) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // -----------------------
            // CONTEXT: edit mode seed
            // -----------------------
            @if (isset($purchase))
                window.EDIT_ORDER_ID = {{ $purchase->id }};
                window.EDIT_ORDER = @json($purchase);
                window.IS_EDITABLE = {{ $isEditable ? 'true' : 'false' }};
            @else
                window.EDIT_ORDER_ID = null;
                window.EDIT_ORDER = null;
                window.IS_EDITABLE = true;
            @endif

            (function seedEditContext() {
                if (!window.EDIT_ORDER) return;

                const serverItems = (window.EDIT_ORDER.items || []).map(i => ({
                    id: i.product_id,
                    name: i.product ? i.product.name : ('Product ' + i.product_id),
                    cost_price: parseFloat(i.unit_cost) || 0,
                    quantity: parseFloat(i.quantity) || 1,
                    // keep whatever was saved, but we will request fresh sys qty after render
                    stock_quantity: (i.product && i.product.stock_quantity) ? i.product
                        .stock_quantity : undefined,
                    sku: i.sku ?? null,
                    description: i.description ?? null
                }));

                localStorage.setItem('purchaseCart', JSON.stringify(serverItems));

                if (window.EDIT_ORDER.supplier_id) {
                    $('#supplier').append(new Option(window.EDIT_ORDER.supplier?.name || 'Supplier',
                        window.EDIT_ORDER.supplier_id, true, true)).trigger('change');
                }
                if (window.EDIT_ORDER.warehouse_id) {
                    $('#warehouse').append(new Option(window.EDIT_ORDER.warehouse?.name || 'Warehouse',
                        window.EDIT_ORDER.warehouse_id, true, true)).trigger('change');
                }
                if (window.EDIT_ORDER.branch_id) {
                    $('#branchSelect').append(new Option(window.EDIT_ORDER.branch?.name || 'Branch',
                        window.EDIT_ORDER.branch_id, true, true)).trigger('change');
                }

                if (window.EDIT_ORDER.order_date) {
                    $('input[name="purchase_date"]').val(window.EDIT_ORDER.order_date.split('T')[0]);
                }
                $('input[name="reference"]').val(window.EDIT_ORDER.reference || '');
                localStorage.setItem('shippingCharge', parseFloat(window.EDIT_ORDER.shipping_amount || 0));
                localStorage.setItem('discount', JSON.stringify({
                    type: window.EDIT_ORDER.discount_type ?? 'flat',
                    value: window.EDIT_ORDER.discount_value ?? 0
                }));

                loadPurchaseItems();
                if (!window.IS_EDITABLE) {
                    $('#purchase-main-form :input').prop('disabled', true);
                }
            })();

            // -----------------------
            // Helpers: branch / warehouse
            // -----------------------
            function getCurrentBranchId() {
                if ($branchSelect.length && $branchSelect.val()) return +$branchSelect.val();
                if ($branchHidden.length && $branchHidden.val()) return +$branchHidden.val();
                return 0;
            }

            function getCurrentWarehouseId() {
                return $warehouse.val() ? +$warehouse.val() : 0;
            }

            // -----------------------
            // STOCK QTY: refresh helpers
            // -----------------------
            function maybeBulkRefresh() {
                // Always do a full refresh for safety
                triggerSystemQtyRefresh();
            }

            function triggerSystemQtyRefresh() {
                const pids = [];
                $('#purchase_items').find('tr').each(function() {
                    const vid = String($(this).data('vid') || $(this).data('product_id') || '');
                    if (vid) pids.push(vid);
                });
                if (!pids.length) return;
                // debug
                console.log('triggerSystemQtyRefresh -> fetching pids', pids, 'wid', getCurrentWarehouseId(), 'bid',
                    getCurrentBranchId());
                bulkFetchSystemQty(pids);
            }

            function bulkFetchSystemQty(productIds) {
                const wid = getCurrentWarehouseId();
                const bid = getCurrentBranchId();
                const $rowsContainer = $('#purchase_items');

                // if no warehouse chosen, skip fetching and preserve existing values
                if (!wid) {
                    console.warn('bulkFetchSystemQty skipped: no warehouse selected');
                    return;
                }

                productIds = Array.from(new Set(productIds.map(String)));
                console.log('bulkFetchSystemQty -> sending', {
                    productIds,
                    wid,
                    bid
                });

                $.ajax({
                    url: stockCurrentsBulkUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        product_ids: productIds,
                        warehouse_id: wid,
                        branch_id: bid,
                        _token: CSRF_TOKEN
                    },
                    success(res) {
                        const raw = res.data || {};
                        const map = Object.fromEntries(Object.entries(raw).map(([k, v]) => [String(k), v]));
                        console.log('bulkFetchSystemQty: response map', map);

                        // Update only rows for which server returned values; preserve others
                        productIds.forEach(pid => {
                            const $row = $rowsContainer.find(`tr[data-vid="${pid}"]`);
                            if (!$row.length) return;

                            if (Object.prototype.hasOwnProperty.call(map, pid)) {
                                const val = map[pid];
                                const sysVal = (val !== undefined && val !== null && !isNaN(
                                    parseFloat(val))) ? parseFloat(val).toFixed(2) : '0.00';
                                $row.find('.sys-qty').text(sysVal);
                            
                                $row.find('.row-warehouse').val(wid);
                                $row.find('.row-branch').val(bid);
                            } else {
                                console.warn(
                                    `bulkFetchSystemQty: no value from server for pid ${pid} — preserving existing`
                                );
                            }
                        });
                    },
                    error(xhr, status, err) {
                        console.error('bulkFetchSystemQty failed', status, err, xhr);
                        // keep placeholders as-is or set only placeholders to 0.00
                        $rowsContainer.find('.sys-qty').each(function() {
                            const txt = $(this).text().trim();
                            if (txt === '__SYS__' || txt === '') $(this).text('0.00');
                        });
                    }
                });
            }

            // -----------------------
            // CART: load / save / UI
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

            function loadPurchaseItems() {
                const purchaseItems = JSON.parse(localStorage.getItem('purchaseCart')) || [];
                let count = 0;
                let purchaseItemsHtml = '';

                purchaseItems.forEach(function(item) {
                    count += 1;
                    const qty = parseFloat(item.quantity) || 1;
                    const unit = parseFloat(item.cost_price) || 0;
                    const line = (unit * qty).toFixed(2);

                    // keep sys as placeholder so we always refresh from server when possible
                    purchaseItemsHtml += `
                        <tr data-vid="${item.id}" data-product_id="${item.id}">
                            <td class="d-flex align-items-center text-center justify-content-start gap-2 text-secondary " style='padding-top:8px;'>
                                <span class="order-item" title="${item.name}">${shortName(item.name)}</span>
                               
                            </td>

                            <td class="text-center text-muted sys-qty" style='padding-top:8px;'>${(typeof item.stock_quantity !== 'undefined') ? (parseFloat(item.stock_quantity).toFixed(2)) : '0.00'}</td>

                            <td class=" align-items-center justify-content-center text-center gap-2 text-secondary">
                                <button class="bg-light rounded-4 border-0 p-1 qty-button btn-minus" type="button">
                                    <iconify-icon icon="flowbite:minus-outline" class="menu-icon fs-7"></iconify-icon>
                                </button>
                                <span class="mx-1">
                                    <input class="form-control form-control-sm qty-input text-center text-black px-1 py-3" type="number" value="${qty}" min="1" style="min-width:30px; max-width:80px; height:30px; display:block;">
                                </span>
                                <button class="bg-light rounded-4 p-1 qty-button btn-plus" type="button">
                                    <iconify-icon icon="flowbite:plus-outline" class="menu-icon fs-7"></iconify-icon>
                                </button>
                            </td>

                            <td>
                                <input type="number" name="item_price" class="item_price form-control form-control-sm border" value="${unit}" style="width:100px; height:35px;">
                            </td>
                            <td class="product-price text-sm" style="padding-top:9px;"> 
                                ${line}
                                </td>
                                <td>
                                 <button class="icon product-delete " data-product_id="${item.id}" type="button" style='padding-top:6px;'>
                                    <iconify-icon icon="solar:trash-bin-trash-outline" class="menu-icon text-lg text-danger"></iconify-icon>
                                </button>
                                </td>

                            <input type="hidden" class="row-warehouse" value="">
                            <input type="hidden" class="row-branch" value="">
                        </tr>`;
                });

                $('#purchase_items').html(purchaseItemsHtml);
                $('#total_items').text(count);
                calculateSubtotal();

                // request system qtys after render (full safe refresh)
                maybeBulkRefresh();
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

                if (getCurrentWarehouseId()) {
                    // ensure DOM finished rendering then refresh
                    setTimeout(() => triggerSystemQtyRefresh(), 80);
                }
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
            // totals
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
                    if (discountObj.type === 'percentage') discountAmount = (subtotal * parseFloat(discountObj
                        .value) / 100);
                    else discountAmount = parseFloat(discountObj.value) || 0;
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
            // Product list and selection flows
            // -----------------------
            function loadProducts(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        let products = res.products?.data || res.products || [];
                        let productsHtml = '';
                        products.forEach(function(product) {
                            let base = "{{ image('') }}";
                            let p = product.image || '';
                            let image = base.replace(
                                'theme/frontend/assets/img/default/book.png', p || base);
                            productsHtml += `<div class="product-card bg-white rounded-3 m-3 d-flex p-3" data-product_id="${product.id}" style="height:110px; width:20%; cursor:pointer;">
                        <img class="w-50 h-75 rounded product-img img-fill" src="${image}" alt="img">
                        <div class="ps-3 ">
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

            $('#product-search-input').on('input', debounce(function() {
                let query = $(this).val().trim();
                if (query.length > 0) {
                    let url = window.routes.productsSearch.replace(':name', encodeURIComponent(query));
                    loadProducts(url);
                } else loadProducts(window.routes.productsList);
            }, 300));
            
            // category click (delegated)
            $(document).on('click', '.category-nav .nav-btn[data-category-id]', function() {
                const $btn = $(this);
                const cid = $btn.data('category-id');
                const isOverflowItem = $btn.hasClass('category-overflow-item');
                const $moreBtn = $('#purchaseReturnMoreCategoriesBtn');
                const $moreLabel = $('.category-nav .category-more-label');

                $('.category-nav .nav-btn[data-category-id]').removeClass('active');
                $('.category-nav .dropdown-item').removeClass('active');

                if (isOverflowItem) {
                    $btn.addClass('active');
                    $moreBtn.addClass('active');
                    $moreLabel.text($.trim($btn.text()));
                } else {
                    $btn.addClass('active');
                    $moreBtn.removeClass('active');
                    $moreLabel.text('More');
                }

                if (!cid) {
                    loadProducts(window.routes.productsList);
                    return;
                }
                const url = window.routes.productsByCategory.replace(':category', cid);
                loadProducts(url);
            });

            // initial load
                loadProducts(window.routes.productsList);
                loadPurchaseItems();

            // product card click
            $(document).on('click', '.product-card', function() {
                const productId = $(this).data('product_id');
                const $card = $(this);

                if (productExistsInCart(productId)) {
                    let cart = getCart();
                    cart = cart.filter(item => !(item.parent_id == productId || item.id == productId));
                    setCart(cart);
                    loadPurchaseItems();
                    $card.removeClass('active');

                    // safe refresh if ware selected
                    if (getCurrentWarehouseId()) setTimeout(() => triggerSystemQtyRefresh(), 80);
                    return;
                }

                const url = window.routes.childProductList.replace(':parentId', productId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        const products = res.products || res || [];
                        let cart = getCart();
                        const newItems = products.map(p => ({
                            id: p.id,
                            parent_id: productId,
                            name: p.name,
                            cost_price: parseFloat(p.cost_price || 0),
                            quantity: 1,
                            stock_quantity: p.stock_quantity || undefined,
                        }));

                        cart = cart.concat(newItems);
                        setCart(cart);
                        loadPurchaseItems();
                        $card.addClass('active');

                        // Always do a full refresh if warehouse present (safe)
                        if (getCurrentWarehouseId()) {
                            setTimeout(() => triggerSystemQtyRefresh(), 80);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // qty / price handlers
            $(document).on('click', '.btn-plus', function() {
                  if(parseInt($(this).closest('tr').find('.qty-input').prop('max')) === parseInt($(this).closest('tr').find('.qty-input').val())) return;
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

            $(document).on('input', '.qty-input', function() {
                const tr = $(this).closest('tr');
                const productId = tr.data('product_id');
                let newQuantity = parseInt($(this).val());
                if (isNaN(newQuantity) || newQuantity < 1) {
                    $(this).val(1);
                    newQuantity = 1;
                }
                updateQuantityInCart(productId, newQuantity);
                const cart = getCart();
                const product = cart.find(i => i.id === productId);
                if (product) tr.find('.product-price').html((product.cost_price * product.quantity).toFixed(
                    2));
                calculateSubtotal();
            });

            $(document).on('input', '.item_price', function() {
                const tr = $(this).closest('tr');
                const newPrice = parseFloat($(this).val()) || 0;
                const productId = tr.data('product_id');
                const product = updateProductPrice(productId, newPrice);
                if (product) tr.find('.product-price').html((product.quantity * product.cost_price).toFixed(
                    2));
            });

            // delete click
            $(document).on('click', '.product-delete', function() {
                const productId = $(this).data('product_id');
                deleteFromPurchaseCart(productId);
            });

            // empty cart
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

            // discount / shipping UI
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

            $('#shipping').on('input', function() {
                const shippingCharge = parseFloat($(this).val()) || 0;
                localStorage.setItem('shippingCharge', shippingCharge);
                calculateSubtotal();
            });

            $(document).on('click', '.payment-modal', function() {
                const total = calculateSubtotal();
                $('#checkout_total_amount').val(total.toFixed(2));
                const paid = parseFloat($('#paid_amount').val()) || 0;
                $('#due_amount').val((total - paid).toFixed(2));
            });

            $('#paid_amount').on('input', function() {
                const paid = parseFloat($(this).val()) || 0;
                const total = parseFloat($('#checkout_total_amount').val()) || 0;
                $('#due_amount').val((total - paid).toFixed(2));
            });

            // -----------------------
            // Warehouse <-> Branch linkage
            // -----------------------
            const warehouseAjaxUrlTemplate =
                "{{ route('inventory.warehouses.showForAjax', ['warehouse' => ':id']) }}";

            $warehouse.on('select2:select', function(evt) {
                const data = evt.params ? evt.params.data : null;
                const wid = getCurrentWarehouseId();
                let branchFromOption = null;
                if (data && (data.branch_id !== undefined)) branchFromOption = data.branch_id;

                if (branchFromOption !== null) {
                    setBranch(branchFromOption, data.branch_name || null);
                    // full refresh
                    triggerSystemQtyRefresh();
                    return;
                }

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
                        console.warn('warehouse detail ajax failed', err);
                    })
                    .always(function() {
                        triggerSystemQtyRefresh();
                    });
            });

            $warehouse.on('select2:clear', function() {
                setBranch(null);
                triggerSystemQtyRefresh();
            });

            $branchSelect.on('change', function() {
                triggerSystemQtyRefresh();
            });

            function setBranch(branchId, branchName = null) {
                if ($branchSelect.length) {
                    if (branchId) {
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

            // -----------------------
            // Submit / payload
            // -----------------------
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

            // postPurchase function (keeps previous behavior)
            function postPurchase(payload, invoiceFile = null, successCb = null, errorCb = null) {
                const updateUrl = "{{ route('purchase.orders.update', ':id') }}";
                const fd = new FormData();
                fd.append('_token', CSRF_TOKEN);
                fd.append('payload', JSON.stringify(payload));
                if (invoiceFile) fd.append('purchase_invoice', invoiceFile);
                let url = PURCHASE_RETURN_STORE_URL;
                if (window.EDIT_ORDER_ID) {
                    url = updateUrl.replace(':id', window.EDIT_ORDER_ID);
                    fd.append('_method', 'PUT');
                }
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (successCb) successCb(res);
                    },
                    error: function(xhr) {
                        if (errorCb) errorCb(xhr);
                        else console.error(xhr.responseText || xhr);
                    }
                });
            }

            // submit handler
            $(document).on('click', '#submit_purchase_btn, .submit-purchase', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const originalText = $btn.data('orig-text') || $btn.text();

                const cart = getCart();
                if (!cart.length) {
                    Swal.fire('Cart is empty. Add products first.', '', 'warning');
                    return;
                }
                const supplier = $('#supplier').val();
                if (!supplier) {
                    Swal.fire('Warning', 'Please choose a supplier.', 'warning');
                    return;
                }
                const warehouse = $('#warehouse').val();
                if (!warehouse) {
                    Swal.fire('Please choose a warehouse.', '', 'warning');
                    return;
                }

                const payload = assemblePurchasePayload();
                const invoiceInput = document.querySelector('input[name="purchase_invoice"]');
                const invoiceFile = invoiceInput?.files?.[0] ?? null;

                $btn.prop('disabled', true).data('orig-text', originalText).text('Processing...');

                postPurchase(payload, invoiceFile, function(res) {
                    $btn.prop('disabled', false).text(originalText);
                    if (res.success) {
                        Swal.fire('Success', res.message || 'Purchase created successfully.',
                            'success');
                        emptyCart();
                        $('#close-modal').click();
                        if (res.redirect_url) setTimeout(() => window.location.href = res
                            .redirect_url, 600);
                    } else Swal.fire('Error', res.message || 'Unknown response', 'error');
                }, function(xhr) {
                    $btn.prop('disabled', false).text(originalText);
                    const msg = xhr.responseJSON?.message ||
                        'Failed to create purchase. Check console.';
                    swal.fire('Check Paid Amount', msg, 'error');
                    console.error(xhr);
                });
            });

            // small helpers
            window.S2.auto?.();

            function loadSupplier() {
                const url = window.routes.supplierRecent;
                $.get(url).done(function(res) {
                    if (res.supplier) $('#supplier').append(new Option(res.supplier.name, res.supplier.id,
                        true, true)).trigger('change');
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                });
            }
            if ($('#supplier').length) loadSupplier();

            function shortName(name, letterLimit = 14) {
                if (!name) return '';
                if (name.length <= letterLimit) return name;
                return name.substring(0, letterLimit) + " ...";
            }

            // If warehouse already preset at initial page load, trigger one fetch
            if (getCurrentWarehouseId()) {
                // allow select2 to populate branch data if needed and then fetch
                setTimeout(() => {
                    $warehouse.trigger('select2:select');
                    triggerSystemQtyRefresh();
                }, 80);
            }

        }); // end document ready
    </script>
@endsection
