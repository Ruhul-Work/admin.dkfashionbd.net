@extends('backend.layouts.master')

@section('meta')
    <title>Edit Offer </title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Offer Management</h6>
            <p class="fw-semibold mb-0">Edit Offer: {{ $offer->name }}</p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">
                <a href="{{ route('offers.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="mdi:gift" class="menu-icon"></iconify-icon>
                    Offers
                </a>
            </li>
        </ul>

        <div class="text-end w-100">
            <a href="{{ route('offers.index') }}" class="btn btn-secondary bg-dark">
                <i data-feather="arrow-left" class="me-2"></i>Back to Offers
            </a>
        </div>
    </div>

    <form id="offerForm" action="{{ route('offers.update', encrypt($offer->id)) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- General Information -->
                    <div class="col-lg-12">
                        <h6 class="fw-semibold mb-3">General Information</h6>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Offer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $offer->name }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Offer Type</label>
                            <input type="text" class="form-control form-control-sm" value="{{ ucfirst($offer->offer_type) }}" readonly>
                            <input type="hidden" name="offer_type" id="offer_type" value="{{ $offer->offer_type }}">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control form-control-sm"
                                value="{{ $offer->start_date->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control form-control-sm"
                                value="{{ $offer->end_date->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label fw-bold mb-1">Header Announcement Bar</label>
                        <p class="text-muted text-sm mb-0">Leave blank to follow Offer Start/End. Use buttons to quickly show/hide the bar without changing offer duration.</p>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Announcement Visible From</label>
                            <input type="datetime-local" name="announcement_start_at" id="announcement_start_at" class="form-control form-control-sm"
                                value="{{ $offer->announcement_start_at?->format('Y-m-d\\TH:i') }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Announcement Visible To</label>
                            <input type="datetime-local" name="announcement_end_at" id="announcement_end_at" class="form-control form-control-sm"
                                value="{{ $offer->announcement_end_at?->format('Y-m-d\\TH:i') }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 d-flex align-items-end gap-2 pb-3">
                        <button type="button" class="btn btn-primary btn-sm px-12 py-8 radius-8" id="announcement_show_now">Show Now</button>
                        <button type="button" class="btn btn-outline-danger btn-sm px-12 py-8 radius-8" id="announcement_hide_now">Hide Now</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-12 py-8 radius-8" id="announcement_clear">Clear</button>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Status</label>
                                    <select name="is_active" id="is_active" class="form-control form-control-sm">
                                <option value="1" {{ $offer->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$offer->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>                
                    <div class="col-lg-8 col-sm-12 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control form-control editorBasic" rows="6">{{ $offer->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="mb-3 add-product">
                            <label class="form-label">Banner Image (Leave blank to keep current)</label>
                            <div id="banner_image"></div>
                            @if ($offer->banner_image)
                                <div class="mt-2">
                                    <label class="form-label">Current Banner:</label>
                                    <img src="{{ image($offer->banner_image) }}" alt="Banner"
                                        style="height: 100px; border-radius: 5px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Type Specific Fields -->
                    <div id="typeSpecificFields" class="col-lg-12 mt-4">
                        <hr>
                        <h6 class="fw-semibold mt-3 mb-3" id="typeTitle">
                            {{-- @if ($offer->offer_type == 'gift')
                                Gift Offer Details (Buy X Get Y)
                            @elseif($offer->offer_type == 'bundle')
                                Bundle Offer Details (Buy One Get Multiple) --}}
                            @if($offer->offer_type == 'shipping')
                                Shipping Offer Details
                            @elseif($offer->offer_type == 'discount')
                                Discount Offer Details
                            @endif
                        </h6>

                        <!-- Gift Fields -->
                        @if ($offer->offer_type == 'gift')
                            <div id="giftFields" class="row g-3 type-fields">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Buy Product <span class="text-danger">*</span></label>
                                        <select name="buy_product_id" id="buy_product_id" class="form-control select2 radius-8">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $offer->gift->buy_product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Buy Qty <span class="text-danger">*</span></label>
                                        <input type="number" name="buy_quantity" class="form-control form-control-sm radius-8"
                                            value="{{ $offer->gift->buy_quantity }}" min="1">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Gift Type <span class="text-danger">*</span></label>
                                        <select name="gift_type" id="gift_type" class="form-control form-control-sm radius-8">
                                            <option value="same_product"
                                                {{ $offer->gift->gift_type == 'same_product' ? 'selected' : '' }}>Same
                                                Product</option>
                                            <option value="different_product"
                                                {{ $offer->gift->gift_type == 'different_product' ? 'selected' : '' }}>
                                                Different Product</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="freeProductDiv" class="col-lg-2 col-sm-6 col-12"
                                    style="{{ $offer->gift->gift_type == 'different_product' ? '' : 'display:none;' }}">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Free Product <span class="text-danger">*</span></label>
                                        <select name="free_product_id" id="free_product_id" class="form-control select2 radius-8">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $offer->gift->free_product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Free Qty <span class="text-danger">*</span></label>
                                        <input type="number" name="free_quantity" class="form-control form-control-sm radius-8"
                                            value="{{ $offer->gift->free_quantity }}" min="1">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Bundle Fields (Buy One Get Multiple) -->
                        @if ($offer->offer_type == 'bundle')
                            @php $firstItem = $offer->giftItems->first(); @endphp
                            <div id="bundleFields" class="row g-3 type-fields">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Buy Product <span class="text-danger">*</span></label>
                                        <select name="bundle_buy_product_id" id="bundle_buy_product_id" class="form-control select2">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $firstItem && $firstItem->buy_product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Buy Qty <span class="text-danger">*</span></label>
                                        <input type="number" name="bundle_buy_quantity" class="form-control form-control-sm"
                                            value="{{ $firstItem ? $firstItem->buy_quantity : 1 }}" min="1">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label fw-semibold">Free Items <span class="text-danger">*</span></label>
                                        <div id="bundleItemsContainer">
                                            @foreach ($offer->giftItems as $index => $item)
                                                <div class="bundle-item-row row align-items-center g-2 mb-2">
                                                    <div class="col-lg-6 col-sm-6">
                                                        <select name="bundle_items[{{ $index }}][free_product_id]" class="form-control select2-bundle">
                                                            <option value="">Select Free Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}"
                                                                    {{ $item->free_product_id == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-4">
                                                        <input type="number" name="bundle_items[{{ $index }}][free_quantity]"
                                                            class="form-control form-control-sm" value="{{ $item->free_quantity }}" min="1"
                                                            placeholder="Free Qty">
                                                    </div>
                                                    <div class="col-lg-2 col-sm-2 d-flex align-items-center">
                                                        <button type="button" class="btn btn-danger px-4 py-2 radius-8 remove-bundle-item">
                                                            <iconify-icon icon="mdi:delete" style="font-size: 20px;"></iconify-icon>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-12 py-8 radius-8 mt-3 d-inline-flex align-items-center gap-1" id="addBundleItem">
                                            <iconify-icon icon="mdi:plus"></iconify-icon> Add Free Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Shipping Fields -->
                        @if($offer->offer_type == 'shipping')
                        <div id="shippingFields" class="row g-3 type-fields">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Free Shipping On <span class="text-danger">*</span></label>
                                    <select name="free_shipping_on" id="free_shipping_on" class="form-control select2">
                                        <option value="all_methods" {{ $offer->shipping->free_shipping_on == 'all_methods' ? 'selected' : '' }}>All Methods</option>
                                        <option value="bkash" {{ $offer->shipping->free_shipping_on == 'bkash' ? 'selected' : '' }}>bKash Only</option>
                                        <option value="cod" {{ $offer->shipping->free_shipping_on == 'cod' ? 'selected' : '' }}>COD Only</option>
                                        <option value="both" {{ $offer->shipping->free_shipping_on == 'both' ? 'selected' : '' }}>bKash & COD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Applies To <span class="text-danger">*</span></label>
                                    <select name="shipping_applies_to" id="shipping_applies_to" class="form-control select2">
                                        <option value="all_products" {{ $offer->shipping->shipping_applies_to == 'all_products' ? 'selected' : '' }}>All Products</option>
                                        <option value="specific_products" {{ $offer->shipping->shipping_applies_to == 'specific_products' ? 'selected' : '' }}>Specific Products</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Min Order Amount</label>
                                    <input type="number" step="0.01" name="min_order_amount_shipping" class="form-control form-control-sm" value="{{ $offer->shipping->min_order_amount }}">
                                </div>
                            </div>
                            <div id="shippingSpecificProductsDiv" class="col-lg-12 col-12" style="{{ $offer->shipping->shipping_applies_to == 'specific_products' ? '' : 'display:none;' }}">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Select Products for Free Shipping <span class="text-danger">*</span></label>
                                    <select name="shipping_product_ids[]" id="shipping_product_ids" class="form-control select2" multiple="multiple">
                                        @php $selectedShippingProducts = $offer->shipping->products->pluck('product_id')->toArray(); @endphp
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ in_array($product->id, $selectedShippingProducts) ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="customAreaDiv" class="col-lg-12 col-12" style="{{ $offer->shipping->applicable_area == 'custom' ? '' : 'display:none;' }}">
                                <div class="mb-3 add-product">
                                    <label class="form-label">Select Districts <span class="text-danger">*</span></label>
                                    <select name="area_ids[]" id="area_ids" class="form-control select2" multiple="multiple">
                                        @php $selectedAreas = $offer->shipping->customAreas->pluck('area_id')->toArray(); @endphp
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ in_array($city->id, $selectedAreas) ? 'selected' : '' }}>
                                                {{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Discount Fields -->
                        @if ($offer->offer_type == 'discount')
                            <div id="discountFields" class="row g-3 type-fields">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                        <select name="discount_type" id="discount_type" class="form-control form-control-sm select2">
                                            <option value="percentage"
                                                {{ $offer->discount->discount_type == 'percentage' ? 'selected' : '' }}>
                                                Percentage (%)</option>
                                            <option value="fixed"
                                                {{ $offer->discount->discount_type == 'fixed' ? 'selected' : '' }}>Fixed
                                                Amount</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="discount_value" class="form-control form-control-sm"
                                            value="{{ $offer->discount->discount_value }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Min Order Amount</label>
                                        <input type="number" step="0.01" name="min_order_amount_discount"
                                            class="form-control form-control-sm" value="{{ $offer->discount->min_order_amount }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Applies To</label>
                                        <select name="applies_to" id="applies_to" class="form-control form-control-sm select2">
                                            <option value="all_products"
                                                {{ $offer->discount->applies_to == 'all_products' ? 'selected' : '' }}>All
                                                Products</option>
                                            <option value="specific_products"
                                                {{ $offer->discount->applies_to == 'specific_products' ? 'selected' : '' }}>
                                                Specific Products</option>
                                            <option value="specific_categories"
                                                {{ $offer->discount->applies_to == 'specific_categories' ? 'selected' : '' }}>
                                                Specific Categories</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="specificProductsDiv" class="col-lg-12 col-12"
                                    style="{{ $offer->discount->applies_to == 'specific_products' ? '' : 'display:none;' }}">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Select Products <span class="text-danger">*</span></label>
                                        <select name="product_ids[]" id="product_ids" class="form-control select2" multiple="multiple">
                                            @php $selectedProducts = $offer->discount->products->pluck('product_id')->toArray(); @endphp
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ in_array($product->id, $selectedProducts) ? 'selected' : '' }}>
                                                    {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="specificCategoriesDiv" class="col-lg-12 col-12"
                                    style="{{ $offer->discount->applies_to == 'specific_categories' ? '' : 'display:none;' }}">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Select Categories <span class="text-danger">*</span></label>
                                        <select name="category_ids[]" id="category_ids" class="form-control select2" multiple="multiple">
                                            @php $selectedCategories = $offer->discount->categories->pluck('category_id')->toArray(); @endphp
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-primary btn-sm btn-submit">Update</button>
            <a href="{{ route('offers.index') }}" class="btn btn-outline-danger btn-sm btn-cancel ">Cancel</a>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').each(function() {
                $(this).select2({
                    placeholder: $(this).attr('placeholder') || 'Choose',
                    width: '100%',
                    allowClear: true
                });
            });

            $('.select2-bundle').each(function() {
                $(this).select2({
                    placeholder: 'Select Free Product',
                    width: '100%',
                    allowClear: true
                });
            });

            $('#gift_type').change(function() {
                if ($(this).val() == 'different_product') {
                    $('#freeProductDiv').show();
                } else {
                    $('#freeProductDiv').hide();
                }
            });

            $('#shipping_applies_to').change(function() {
                if ($(this).val() == 'specific_products') {
                    $('#shippingSpecificProductsDiv').show();
                } else {
                    $('#shippingSpecificProductsDiv').hide();
                }
            });

            $('#applicable_area').change(function() {
                if ($(this).val() == 'custom') {
                    $('#customAreaDiv').show();
                } else {
                    $('#customAreaDiv').hide();
                }
            });

            $('#applies_to').change(function() {
                var val = $(this).val();
                $('#specificProductsDiv, #specificCategoriesDiv').hide();
                if (val == 'specific_products') {
                    $('#specificProductsDiv').show();
                } else if (val == 'specific_categories') {
                    $('#specificCategoriesDiv').show();
                }
            });

            // ---- Bundle: Add/Remove Free Item Rows ----
            var productOptions = '';
            @foreach($products as $product)
                productOptions += '<option value="{{ $product->id }}">{{ addslashes($product->name) }}<\/option>';
            @endforeach

            function addBundleItemRow() {
                var index = $('#bundleItemsContainer .bundle-item-row').length;
                var row = $('<div class="bundle-item-row row align-items-center g-2 mb-2">'
                    + '<div class="col-lg-6 col-sm-6"><select name="bundle_items['+index+'][free_product_id]" class="form-control select2-bundle">'
                    + '<option value="">Select Free Product<\/option>' + productOptions + '<\/select><\/div>'
                    + '<div class="col-lg-3 col-sm-4"><input type="number" name="bundle_items['+index+'][free_quantity]" class="form-control form-control-sm" value="1" min="1" placeholder="Free Qty"><\/div>'
                    + '<div class="col-lg-2 col-sm-2 d-flex align-items-center"><button type="button" class="btn btn-danger px-4 py-2 radius-8 remove-bundle-item"><iconify-icon icon="mdi:delete" style="font-size: 20px;"><\/iconify-icon><\/button><\/div>'
                    + '<\/div>');
                $('#bundleItemsContainer').append(row);
                row.find('.select2-bundle').select2({
                    placeholder: 'Select Free Product',
                    width: '100%',
                    allowClear: true
                });
            }

            $('#addBundleItem').on('click', function() {
                addBundleItemRow();
            });

            $(document).on('click', '.remove-bundle-item', function() {
                if ($('#bundleItemsContainer .bundle-item-row').length > 1) {
                    $(this).closest('.bundle-item-row').remove();
                    // Re-index names
                    $('#bundleItemsContainer .bundle-item-row').each(function(i) {
                        $(this).find('[name*="bundle_items["]').each(function() {
                            var name = $(this).attr('name').replace(/bundle_items\[\d+\]/, 'bundle_items['+i+']');
                            $(this).attr('name', name);
                        });
                    });
                } else {
                    alert('At least one free item is required.');
                }
            });

            const localNowValue = () => {
                const now = new Date();
                const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
                return local.toISOString().slice(0, 16);
            };

            $('#announcement_show_now').on('click', function() {
                $('#announcement_start_at').val(localNowValue());
                const offerEnd = $('input[name="end_date"]').val();
                if (offerEnd) $('#announcement_end_at').val(offerEnd);
            });

            $('#announcement_hide_now').on('click', function() {
                $('#announcement_end_at').val(localNowValue());
            });

            $('#announcement_clear').on('click', function() {
                $('#announcement_start_at').val('');
                $('#announcement_end_at').val('');
            });

            if ($('#gift_type').val() == 'different_product') {
                $('#freeProductDiv').show();
            }

            $("#banner_image").spartanMultiImagePicker({
                fieldName: 'banner_image',
                maxCount: 1,
                rowHeight: '200px',
                groupClassName: 'col',
                maxFileSize: '',
                dropFileLabel: "Drop Here",
                onExtensionErr: function(index, file) {
                    console.log(index, file, 'extension err');
                    alert('Please only input png or jpg type file')
                },
                onSizeErr: function(index, file) {
                    console.log(index, file, 'file size too big');
                    alert('File size too big max:250KB');
                }
            });

            $('#offerForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                if ($('#offer_type').val() == 'shipping') {
                    formData.append('min_order_amount', $('input[name="min_order_amount_shipping"]').val());
                } else if ($('#offer_type').val() == 'discount') {
                    formData.append('min_order_amount', $('input[name="min_order_amount_discount"]').val());
                }

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "{{ route('offers.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = "";
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + "\n";
                            });
                            Swal.fire("Error!", errorMsg, "error");
                        } else {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    }
                });
            });
        });
    </script>
@endsection
