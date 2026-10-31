@extends('backend.layouts.master')

@section('meta')
    <title>Create Offer</title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Offer Management</h6>
            <p class="fw-semibold mb-0">Create new offer</p>
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

    <form id="offerForm" action="{{ route('offers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="accordion-card-one accordion mb-6" id="offerBasicAccordion">
            <div class="accordion-item">
                <div class="accordion-header" id="offerBasicHeading">
                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#offerBasicCollapse"
                        aria-controls="offerBasicCollapse">
                        <div class="addproduct-icon">
                            <h6><i data-feather="info" class="add-info"></i><span>General Information</span></h6>
                            <a href="javascript:void(0);"><i data-feather="chevron-down"
                                    class="chevron-down-add"></i></a>
                        </div>
                    </div>
                </div>
                <div id="offerBasicCollapse" class="accordion-collapse collapse show" aria-labelledby="offerBasicHeading"
                    data-bs-parent="#offerBasicAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm radius-8"
                                    placeholder="Summer Sale 2024" required>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Offer Type <span class="text-danger">*</span></label>
                                <select name="offer_type" id="offer_type" class="form-control select2 radius-8" required>
                                    <option value="">Select Offer Type</option>
                                    {{-- <option value="gift">Gift (Buy X Get Y)</option>
                                    <option value="bundle">Bundle (Buy One Get Multiple)</option> --}}
                                    {{-- <option value="shipping">Shipping (Free Delivery)</option> --}}
                                    <option value="discount">Discount (Percentage/Fixed)</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-control select2 radius-8">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" class="form-control form-control-sm radius-8" required>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" class="form-control form-control-sm radius-8" required>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label fw-bold mb-1">Header Announcement Bar</label>
                                <p class="text-muted text-sm mb-0">Leave blank to follow Offer Start/End. Use buttons to quickly show/hide the bar without changing offer duration.</p>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Announcement Visible From</label>
                                <input type="datetime-local" name="announcement_start_at" id="announcement_start_at"
                                    class="form-control form-control-sm radius-8">
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Announcement Visible To</label>
                                <input type="datetime-local" name="announcement_end_at" id="announcement_end_at"
                                    class="form-control form-control-sm radius-8">
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12 d-flex align-items-end gap-2">
                                <button type="button" class="btn btn-primary btn-sm px-12 py-8 radius-8"
                                    id="announcement_show_now">Show Now</button>
                                <button type="button" class="btn btn-outline-danger btn-sm px-12 py-8 radius-8"
                                    id="announcement_hide_now">Hide Now</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-12 py-8 radius-8"
                                    id="announcement_clear">Clear</button>
                            </div>
                            <div class="col-lg-8 col-sm-12 col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control form-control editorBasic radius-8" rows="6"></textarea>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
                                <label class="form-label">Banner Image</label>
                                <div id="banner_image"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-card-one accordion mb-6" id="offerTypeAccordion" style="display:none;">
            <div class="accordion-item">
                <div class="accordion-header" id="offerTypeHeading">
                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#offerTypeCollapse"
                        aria-controls="offerTypeCollapse">
                        <div class="addproduct-icon">
                            <h6><i data-feather="layers" class="add-info"></i><span id="typeTitle">Offer Details</span></h6>
                            <a href="javascript:void(0);"><i data-feather="chevron-down"
                                    class="chevron-down-add"></i></a>
                        </div>
                    </div>
                </div>
                <div id="offerTypeCollapse" class="accordion-collapse collapse show" aria-labelledby="offerTypeHeading"
                    data-bs-parent="#offerTypeAccordion">
                    <div class="accordion-body">
                        <div id="typeSpecificFields" style="display:none;">
                            <div id="giftFields" class="row g-3 type-fields" style="display:none;">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Buy Product <span class="text-danger">*</span></label>
                                    <select name="buy_product_id" class="form-control select2 radius-8">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <label class="form-label">Buy Qty <span class="text-danger">*</span></label>
                                    <input type="number" name="buy_quantity" class="form-control form-control-sm radius-8" value="1" min="1">
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Gift Type <span class="text-danger">*</span></label>
                                    <select name="gift_type" id="gift_type" class="form-control radius-8">
                                        <option value="same_product">Same Product</option>
                                        <option value="different_product">Different Product</option>
                                    </select>
                                </div>
                                <div id="freeProductDiv" class="col-lg-3 col-sm-6 col-12" style="display:none;">
                                    <label class="form-label">Free Product <span class="text-danger">*</span></label>
                                    <select name="free_product_id" class="form-control select2 radius-8">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <label class="form-label">Free Qty <span class="text-danger">*</span></label>
                                    <input type="number" name="free_quantity" class="form-control form-control-sm radius-8" value="1" min="1">
                                </div>
                            </div>

                            <div id="bundleFields" class="row g-3 type-fields" style="display:none;">
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Buy Product <span class="text-danger">*</span></label>
                                    <select name="bundle_buy_product_id" class="form-control select2 radius-8">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-6 col-12">
                                    <label class="form-label">Buy Qty <span class="text-danger">*</span></label>
                                    <input type="number" name="bundle_buy_quantity" class="form-control form-control-sm radius-8" value="1" min="1">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Free Items <span class="text-danger">*</span></label>
                                    <div id="bundleItemsContainer"></div>
                                    <button type="button" class="btn btn-outline-primary btn-sm px-12 py-8 radius-8 mt-3 d-inline-flex align-items-center gap-1"
                                        id="addBundleItem"><iconify-icon icon="mdi:plus"></iconify-icon>Add Free Item</button>
                                </div>
                            </div>

                            <div id="shippingFields" class="row g-3 type-fields" style="display:none;">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Free Shipping On <span class="text-danger">*</span></label>
                                    <select name="free_shipping_on" class="form-control select2 radius-8">
                                        <option value="all_methods">All Methods</option>
                                        <option value="bkash">bKash Only</option>
                                        <option value="cod">COD Only</option>
                                        <option value="both">bKash & COD</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Applies To <span class="text-danger">*</span></label>
                                    <select name="shipping_applies_to" id="shipping_applies_to" class="form-control select2 radius-8">
                                        <option value="all_products">All Products</option>
                                        <option value="specific_products">Specific Products</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Min Order Amount</label>
                                    <input type="number" step="0.01" name="min_order_amount_shipping" class="form-control form-control-sm radius-8" value="0.00">
                                </div>
                                <div id="shippingSpecificProductsDiv" class="col-12" style="display:none;">
                                    <label class="form-label">Select Products for Free Shipping <span class="text-danger">*</span></label>
                                    <select name="shipping_product_ids[]" class="form-control select2 radius-8" multiple="multiple">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="discountFields" class="row g-3 type-fields" style="display:none;">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select name="discount_type" class="form-control select2 radius-8">
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control form-control-sm radius-8">
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Min Order Amount</label>
                                    <input type="number" step="0.01" name="min_order_amount_discount" class="form-control form-control-sm radius-8" value="0.00">
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label class="form-label">Applies To</label>
                                    <select name="applies_to" id="applies_to" class="form-control select2 radius-8">
                                        <option value="all_products">All Products</option>
                                        <option value="specific_products">Specific Products</option>
                                        <option value="specific_categories">Specific Categories</option>
                                    </select>
                                </div>
                                <div id="specificProductsDiv" class="col-12" style="display:none;">
                                    <label class="form-label">Select Products <span class="text-danger">*</span></label>
                                    <select name="product_ids[]" class="form-control select2 radius-8" multiple="multiple">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="specificCategoriesDiv" class="col-12" style="display:none;">
                                    <label class="form-label">Select Categories <span class="text-danger">*</span></label>
                                    <select name="category_ids[]" class="form-control select2 radius-8" multiple="multiple">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2">
            <a href="{{ route('offers.index') }}" class="btn btn-outline-secondary btn-sm px-12 py-8 radius-8">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-12 py-8 radius-8">Submit</button>
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

            $('#offer_type').on('change', function() {
                var type = $(this).val();
                $('.type-fields').hide();
                $('#typeSpecificFields').hide();
                $('#offerTypeAccordion').hide();

                if (!type) {
                    return;
                }

                $('#offerTypeAccordion').show();
                $('#typeSpecificFields').show();

                if (type == 'gift') {
                    $('#giftFields').show();
                    $('#typeTitle').text('Gift Offer Details (Buy X Get Y)');
                } else if (type == 'bundle') {
                    $('#bundleFields').show();
                    $('#typeTitle').text('Bundle Offer Details (Buy One Get Multiple)');
                    if ($('#bundleItemsContainer .bundle-item-row').length === 0) {
                        addBundleItemRow();
                    }
                } else if (type == 'shipping') {
                    $('#shippingFields').show();
                    $('#typeTitle').text('Shipping Offer Details');
                } else if (type == 'discount') {
                    $('#discountFields').show();
                    $('#typeTitle').text('Discount Offer Details');
                }
            });

            $('#gift_type').on('change', function() {
                if ($(this).val() == 'different_product') {
                    $('#freeProductDiv').show();
                } else {
                    $('#freeProductDiv').hide();
                }
            });

            $('#shipping_applies_to').on('change', function() {
                if ($(this).val() == 'specific_products') {
                    $('#shippingSpecificProductsDiv').show();
                } else {
                    $('#shippingSpecificProductsDiv').hide();
                }
            });

            $('#applies_to').on('change', function() {
                var val = $(this).val();
                $('#specificProductsDiv, #specificCategoriesDiv').hide();
                if (val == 'specific_products') {
                    $('#specificProductsDiv').show();
                } else if (val == 'specific_categories') {
                    $('#specificCategoriesDiv').show();
                }
            });

            var productOptions = '';
            @foreach($products as $product)
                productOptions += '<option value="{{ $product->id }}">{{ addslashes($product->name) }}<\/option>';
            @endforeach

            function addBundleItemRow(freeProductId, freeQty) {
                var index = $('#bundleItemsContainer .bundle-item-row').length;
                var row = $('<div class="bundle-item-row row align-items-center g-2 mb-2">'
                    + '<div class="col-lg-6 col-sm-6"><select name="bundle_items['+index+'][free_product_id]" class="form-control select2-bundle radius-8">'
                    + '<option value="">Select Free Product<\/option>' + productOptions + '<\/select><\/div>'
                    + '<div class="col-lg-3 col-sm-4"><input type="number" name="bundle_items['+index+'][free_quantity]" class="form-control radius-8" value="'+(freeQty||1)+'" min="1" placeholder="Free Qty"><\/div>'
                    + '<div class="col-lg-2 col-sm-2 d-flex align-items-center"><button type="button" class="btn btn-danger px-4 py-2 radius-8 remove-bundle-item"><iconify-icon icon="mdi:delete" style="font-size: 20px;"><\/iconify-icon><\/button><\/div>'
                    + '<\/div>');

                $('#bundleItemsContainer').append(row);
                row.find('.select2-bundle').select2({
                    placeholder: 'Select Free Product',
                    width: '100%',
                    allowClear: true
                });

                if (freeProductId) {
                    row.find('.select2-bundle').val(freeProductId).trigger('change');
                }
            }

            $('#addBundleItem').on('click', function() {
                addBundleItemRow();
            });

            $(document).on('click', '.remove-bundle-item', function() {
                if ($('#bundleItemsContainer .bundle-item-row').length > 1) {
                    $(this).closest('.bundle-item-row').remove();
                    $('#bundleItemsContainer .bundle-item-row').each(function(i) {
                        $(this).find('[name*="bundle_items["]').each(function() {
                            var name = $(this).attr('name').replace(/bundle_items\[\d+\]/, 'bundle_items[' + i + ']');
                            $(this).attr('name', name);
                        });
                    });
                } else {
                    alert('At least one free item is required.');
                }
            });

            $('#banner_image').spartanMultiImagePicker({
                fieldName: 'banner_image',
                maxCount: 1,
                rowHeight: '200px',
                groupClassName: 'col',
                maxFileSize: '',
                dropFileLabel: 'Drop Here',
                onExtensionErr: function(index, file) {
                    console.log(index, file, 'extension err');
                    alert('Please only input png or jpg type file');
                },
                onSizeErr: function(index, file) {
                    console.log(index, file, 'file size too big');
                    alert('File size too big max:250KB');
                }
            });

            $('#offerForm').on('submit', function(e) {
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
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "{{ route('offers.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire('Error!', errorMsg, 'error');
                        } else {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    }
                });
            });

            const localNowValue = () => {
                const now = new Date();
                const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
                return local.toISOString().slice(0, 16);
            };

            $('#announcement_show_now').on('click', function() {
                $('#announcement_start_at').val(localNowValue());
                const offerEnd = $('input[name="end_date"]').val();
                if (offerEnd) {
                    $('#announcement_end_at').val(offerEnd);
                }
            });

            $('#announcement_hide_now').on('click', function() {
                $('#announcement_end_at').val(localNowValue());
            });

            $('#announcement_clear').on('click', function() {
                $('#announcement_start_at').val('');
                $('#announcement_end_at').val('');
            });
        });
    </script>
@endsection
