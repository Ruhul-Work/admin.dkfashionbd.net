@extends('backend.layouts.master')

@section('meta')
    <title>Edit Product </title>
@endsection



@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Product Management</h6>
            <p class="fw-semibold mb-0">Edit Product</p>
        </div>

        <ul class="d-flex align-items-center gap-2 mb-0">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Product</li>
        </ul>

        {{-- Button on a new line (right aligned) --}}
        <div class="text-end w-100 ">
            <a href="{{ route('product.products.index') }}" class="btn btn-secondary bg-dark "><i data-feather="arrow-left"
                    class="me-2"></i>Back to
                Products</a>
        </div>
    </div>



    <form id="product_form" action="{{ route('product.products.update', $product->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Whoops! Something went wrong.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class=" ">
            <div class="  ">
                <div class="accordion-card-one accordion mb-6 shadow-sm rounded-circle" id="accordionExample">
                    <div class="accordion-item">
                        <div class="accordion-header" id="headingOne">
                            <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                aria-controls="collapseOne">
                                <div class="addproduct-icon">
                                    <h6><i data-feather="info" class="add-info"></i><span>Basic Information</span></h6>
                                    <a href="javascript:void(0);"><i data-feather="chevron-down"
                                            class="chevron-down-add"></i></a>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <div class="row">


                                    <div class="col-lg-5 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $product->name }}">

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="mb-3 ">
                                            <label class="form-label">Slug<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ $product->slug }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <label for="sku" class="form-label">Product SKU</label>
                                        <input type="text" name="sku" class="form-control" placeholder="e.g. TS001"
                                            id="sku" value="{{ $product->sku }}">

                                    </div>

                                    {{-- <div class="col-md-3 mb-16">
                                        <label class="form-label text-sm mb-8">Category Type</label>
                                        <select name="category_type_id" id="category_type"
                                            class="form-control js-category-select">
                                            <option value="{{ $product->category_type_id }}" selected>
                                                {{ $product->category_type->name }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block category_id-error" style="display:none">
                                        </div>
                                    </div> --}}
                                    <div class="col-md-3 mb-16">
                                        <label class="form-label text-sm mb-8">Category</label>
                                        <select name="category_id" id="category" class="form-control js-category-select">
                                            <option value="{{ $product->category_id }}" selected>
                                                {{ $product->category->name }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block category_id-error" style="display:none">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-16">
                                        <label class="form-label text-sm mb-8">Sub-Category</label>
                                        <select name="subcategory_id" id="subcategory"
                                            class="form-control js-category-select">
                                            <option value="{{ $product->subcategory_id ?? '' }}" selected>
                                                {{ $product->subcategory?->name ?? 'No Sub-Category' }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block category_id-error" style="display:none">
                                        </div>
                                    </div>



                                    {{-- <div class="row"> --}}
                                    {{-- <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Product Type</label>
                                        <select name="product_type_id" id="product_type"
                                            class="form-control js-product-type-select">
                                            <option value="{{ $product->product_type_id }}" selected>
                                                {{ $product->product_type->name }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block product_type_id-error" style="display:none">
                                        </div>
                                    </div> --}}

                                    {{-- <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Unit</label>
                                        <select name="unit_id" id="unit"
                                            class="form-control js-product-type-select">
                                            <option value="{{ $product->unit_id }}" selected>{{ $product->unit->name }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback d-block brand_type_id-error" style="display:none">
                                        </div>
                                    </div> --}}



                                    {{-- <div class="row"> --}}
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Brand</label>
                                        <select name="brand_id" id="brand" class="form-control js-product-type-select">
                                            <option value="{{ $product->brand_id }}" selected>{{ $product->brand->name }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback d-block brand_type_id-error" style="display:none">
                                        </div>
                                    </div>







                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 ">
                                            <label class="form-label">Purchase Price<span
                                                    class="star-sign">*</span></label>
                                            <input type="number" class="form-control" name="cost_price"
                                                value="{{ $product->cost_price }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 add-product">
                                            <label class="form-label">MRP Price<span class="star-sign">*</span></label>
                                            <input type="number" step="0.01" class="form-control" min="0"
                                                name="mrp" id="mrp" value="{{ $product->mrp }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Discount</label>
                                        <div class="input-group">
                                            <select name="discount_type" class="form-select" id="discount_type">
                                                <option value="" {{ old('discount_type', $product->discount_type) === null ? 'selected' : '' }}>No discount</option>
                                                <option value="percent"
                                                    {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>
                                                    Percentage(%)</option>
                                                <option value="flat"
                                                    {{ old('discount_type', $product->discount_type) === 'flat' ? 'selected' : '' }}>Flat
                                                </option>
                                            </select>
                                            <input type="number" name="discount_value" min="0"
                                                class="form-control" placeholder="Enter discount" id="discount_value"
                                                value="{{ old('discount_value', $product->discount_value ?? 0) }}" required>
                                        </div>
                                    </div>



                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Sale Price<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" name="price" id="sale_price"
                                                value="{{ $product->price }}" readonly>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 ">
                                            <label class="form-label">Materials<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" name="material"
                                                value="{{ $product->material }}">
                                        </div>
                                    </div> --}}

                                    <div class="col-lg-3 col-sm-6 col-12 col">
                                        <label for="has_variant" class="form-label">Has Variant?</label>
                                        <select class="form-select " name="has_variant" id="has_variant">
                                            <option value="0" {{ $product->has_variants == 0 ? 'selected' : '' }}
                                                {{ $product->has_variants == 1 ? 'disabled' : '' }}>No
                                            </option>
                                            <option value="1" {{ $product->has_variants == 1 ? 'selected' : '' }}>Yes
                                            </option>

                                        </select>

                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12 col" id="variant_type_div">
                                        <label for="has_variant" class="form-label">Variant Type</label>
                                        <select class="form-select " name="variant_type" id="variant_type">
                                            <option value="cloth">Cloth</option>
                                        </select>

                                    </div>

                                    {{-- <div class="row"> --}}
                                    <div class="col-lg-3 col-sm-6 col-12 " id="color_div">
                                        <label class="form-label text-sm mb-8">Color</label>

                                        <select name="color_id[]" id="color" multiple
                                            class="form-control js-color-type-select ">
                                            @if ($colors->isNotEmpty())
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->color_id ?? null }}" selected>
                                                        {{ $color->color->name ?? null }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback d-block color_type_id-error"
                                            style="display:none">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12" id="size_div">
                                        <label class="form-label text-sm mb-8">Size</label>
                                        <select name="size_id[]" id="size" multiple
                                            class="form-control js-color-type-select">
                                            @if ($sizes->isNotEmpty())
                                                    @foreach ($sizes as $size)
                                                        <option value="{{ $size->size_id ?? null }}" selected>
                                                            {{ $size->size->code ?? null }}</option>
                                                    @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback d-block color_type_id-error"
                                            style="display:none">
                                        </div>
                                    </div>
                                    @if ($products->get(0)?->paper_id != null)
                                        <div class="col-lg-3 col-sm-6 col-12" id="paper_div">
                                            <label class="form-label text-sm mb-8">Paper Quality</label>
                                            {{-- @if ($papers->isNotEmpty()) --}}
                                            <select name="paper_id[]" id="paper" multiple
                                                class="form-control js-color-type-select">

                                                @foreach ($products as $product)
                                                    <option value="{{ $product->paper_id ?? null }}" selected>
                                                        {{ $product->paper_quality->name }} </option>
                                                @endforeach
                                            </select>
                                            {{-- @endif --}}
                                            <div class="invalid-feedback d-block color_type_id-error"
                                                style="display:none">
                                            </div>
                                        </div>
                                    @endif

                                    {{-- <div class="col-12 row shadow-lg m-auto rounded mb-3 dark-mode p-3 mt-5">

                                        <h6>Existing Variants</h6>
                                        @foreach ($products as $product)
                                            <div class="col-lg-12 row mb-2 g-2">
                                                <div class="col-lg-2">
                                                    <label class="form-label">Name</label>
                                                    <input class="form-control" name="child_name[]"
                                                        value="{{ $product->name }}" id="child_name">
                                                    <input class="form-control" hidden name="child_id[]"
                                                        value="{{ $product->id }}">
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label">Color</label>
                                                    <input class="form-control" value="{{ $product->color->name ?? null }}"
                                                        readonly>
                                                    <input type="hidden" name="child_color_id[]"
                                                        value="{{ $product->color_id ?? null }}">
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label">Size</label>
                                                    <input class="form-control" value="{{ $product->size->name ?? null }}"
                                                        readonly>
                                                    <input type="hidden" name="child_size_id[]"
                                                        value="{{ $product->size_id ?? null }}">
                                                </div>



                                                <div class="col-lg-2">
                                                    <label class="form-label">SKU</label>
                                                    <input class="form-control" name="child_sku[]"
                                                        value="{{ $product->sku }}" id="child_sku">
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label">Price</label>
                                                    <input type="number" min="0" class="form-control"
                                                        name="child_price[]" value="{{ $product->price }}"
                                                        id="child_price">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div> --}}

                                    <div class="col-12 row shadow-lg m-auto rounded mb-3 dark-mode p-3 mt-5"
                                        id="child_products"></div>


                                    <div class="col-lg-12 row mt-10">
                                        <div class="my-3 text-lg fw-bold text-gray-900">Product Specifications</div>

                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Materials</label>
                                                <input type="text" class="form-control" name="material" value="{{ $product->specifications->material ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Weight(gsm)</label>
                                                <input type="text" class="form-control" name="weight_gsm" value="{{ $product->specifications->weight_gsm ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Fabric Weave</label>
                                                <input type="text" class="form-control" name="fabric_weave" value="{{ $product->specifications->fabric_weave ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Fabric Type</label>
                                                <input type="text" class="form-control" name="fabric_type" value="{{ $product->specifications->fabric_type ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Origin Country</label>
                                                <input type="text" class="form-control" name="origin_country" value="{{ $product->specifications->origin_country ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Care Instraction</label>
                                                <input type="text" class="form-control" name="care_instructions" value="{{ $product->specifications->care_instructions ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-sm-6 col-12 col">
                                            <label for="durability_rating" class="form-label">Durability Rating</label>
                                            <select class="form-select " name="durability_rating">
                                                <option value="Standard" {{ ($product->specifications->durability_rating ?? '') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                                <option value="High" {{ ($product->specifications->durability_rating ?? '') == 'High' ? 'selected' : '' }}>High</option>
                                                <option value="Premium" {{ ($product->specifications->durability_rating ?? '') == 'Premium' ? 'selected' : '' }}>Premium</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Transparency Text</label>
                                                <input type="text" class="form-control" name="transparency_text" value="{{ $product->specifications->transparency_text ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12 ">
                                            <div class="">
                                                <label class="form-label ">Description</label>
                                                <textarea rows="4" cols="5" class="form-control h-100" name="description"
                                                    placeholder="Enter text here">{{ $product->description }}</textarea>

                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-sm-6 col-12 ">
                                            <div class="">
                                                <label class="form-label ">Short Description</label>
                                                <textarea rows="4" cols="5" class="form-control h-100" name="short_description"
                                                    placeholder="Enter text here">{{ $product->short_description }}</textarea>

                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="my-3">Image Section</div>

                                    <div class="col-12 row  m-auto rounded mb-3 dark-mode p-3 mt-5">

                                        
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3 ">
                                                <label class="form-label">Thumbnail Image<span
                                                        class="star-sign">*</span><small class="text-muted">(Max:
                                                        2MB)</small></label>
                                                <input type="hidden" name="remove_thumbnail_image"
                                                    id="remove_thumbnail_image" value="0">
                                                <div class="form-group">
                                                    <div class="row" id="thumbnail_image">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12 ">
                                            <div class="mb-3">
                                                <label class="form-label">Image <small
                                                        class="text-muted">(Max: 2MB)</small></label>
                                                <input type="hidden" name="remove_image" id="remove_image"
                                                    value="0">
                                                <div class="form-group">
                                                    <div class="row" id="image">

                                                    </div>
                                                </div>
                                                <div id="preview"></div>
                                            </div>
                                        </div>

                                        {{-- size chart image --}}
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Measurement Chart<span class="star-sign">*</span><small class="text-muted">(Max: 2MB)</small></label>
                                                <input type="hidden" name="remove_size_chart_image" id="remove_size_chart_image" value="0">
                                                <div class="form-group">
                                                    <div class="row" id="size_chart_image">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 mb-8">
                                                <label class="form-label text-sm mb-8">Active?</label>
                                                <div class="form-switch switch-purple d-flex align-items-center gap-3">
                                                    <input type="hidden" name="is_active" value="0">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        value="1" id="categoryIsActive"
                                                        {{ $product->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="categoryIsActive">Enable this
                                                        product</label>
                                                </div>
                                                <div class="invalid-feedback d-block is_active-error"
                                                    style="display:none">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-card-one accordion shadow-sm rounded-circle" id="accordionExample2">
                        <div class="accordion-item ">
                            <div class="accordion-header" id="headingTwo">
                                <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                    aria-controls="collapseTwo">
                                    <div class="text-editor add-list">
                                        <div class="addproduct-icon list icon">
                                            <h6><i data-feather="life-buoy" class="add-info"></i><span>Meta Section</span>
                                            </h6>
                                            <a href="javascript:void(0);"><i data-feather="chevron-down"
                                                    class="chevron-down-add"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample2">
                                <div class="accordion-body">

                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                            aria-labelledby="pills-home-tab">
                                            <div class="row">


                                                <div class="col-lg-6 col-sm-6 col-12">
                                                    <div class=" ">
                                                        <label class="form-label">Meta Title</label>
                                                        <input type="text" class="form-control" name="meta_title"
                                                            value="{{ $product->meta_title ?? '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-sm-6 col-12 ">
                                                    <div class="">
                                                        <label class="form-label">Meta Keywords</label>
                                                        <input type="text" class="form-control" name="meta_keywords"
                                                            value="{{ $product->meta_keywords ?? '' }}">
                                                    </div>
                                                </div>




                                            </div>
                                            <div class="row mt-3 justify-content-between">
                                                <div class="col-lg-3 col-sm-6 col-12 mt-8">
                                                    <label class="form-label"> Meta Image
                                                        <small class="text-muted">(Max: 2MB)</small></label>
                                                    <input type="hidden" name="remove_meta_image" id="remove_meta_image"
                                                        value="0">
                                                    <div class="form-group">
                                                        <div class="row" id="meta_image">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-sm-6 col-12 mt-2">
                                                    <div class=" list ">
                                                        <label class="form-label "> Meta Description</label>
                                                        <textarea rows="8" cols="5" class="form-control h-100" name="meta_description"
                                                            placeholder="Enter text here">{{ $product->meta_description ?? '' }}</textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-12 mt-3">
                <div class="btn-addproduct mb-4">
                    <button type="submit" class="btn btn-primary ">Update All</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <style>
        .star-sign {
            color: red;
            font-weight: bold;
        }
    </style>
    <script src="{{ asset('theme/admin/assets/plugins/fileupload/spartan-multi-image-picker-min.js') }}"
        type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            function showChildDiv() {
                if ($('#has_variant').val() == 1) {
                    $('#child_products').removeClass('d-none');
                } else {
                    $('#child_products').addClass('d-none');

                }
            }
            showChildDiv();
            $('#has_variant').on('change', showChildDiv);

            const products = @json($products);

            function variantExists(color = null, size = null, paper = null) {
                for (let p of products) {
                    if (p.color_id == color && p.size_id == size && p.paper_id == paper) {
                        return p; // returns from the outer function
                    }
                }
                return null; // not found
            }


            function generateSku() {
                const name = ($('#name').val().slice(0, 3)).toUpperCase();

                $('#sku').val(name ? name + Math.floor(1000000 + Math.random() * 9000000) : '');
            }
            $('#name').on('input', generateSku);

            function variantSkuPart(name, id, fallback) {
                const code = String(name || '')
                    .trim()
                    .replace(/[^a-zA-Z0-9]/g, '')
                    .slice(0, 3)
                    .toUpperCase() || fallback;
                const safeId = String(id ?? '').replace(/[^a-zA-Z0-9]/g, '');

                return safeId ? `${code}-${safeId}` : code;
            }

            function appendChildProduct() {
                $('#child_products').empty();
                $('#child_products').append(`<h6>Product Variants</h6>`);

                const colorIds = $('#color').val() || [];
                const sizeIds = $('#size').val() || [];
                const paperIds = $('#paper').val() || [];


                const colorNames = $('#color option:selected').map(function() {
                    return $(this).text();
                }).get();

                const sizeNames = $('#size option:selected').map(function() {
                    return $(this).text();
                }).get();
                const paperNames = $('#paper option:selected').map(function() {
                    return $(this).text();
                }).get();

                const name = $('#name').val();
                const price = parseFloat($('#sale_price').val());

                const parentSKU = $('#sku').val() || 'SKU'; // fallback if empty

                if (colorIds.length > 0 && sizeIds.length > 0) {
                    colorIds.forEach((colorId, i) => {

                        sizeIds.forEach((sizeId, j) => {
                            const oldChild = variantExists(colorId, sizeId, null) ?? null;
                            const generatedSKU = `${parentSKU}-${variantSkuPart(colorNames[i], colorId, 'C')}-${variantSkuPart(sizeNames[j], sizeId, 'S')}`;
                            const childSKU = oldChild?.sku ?? generatedSKU;
                            $('#child_products').append(`
                    <div class="col-lg-12 row mb-2 g-2">
                        <div class="col-lg-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="child_name[]" value="${name+'-'+colorNames[i].trim()+'-'+sizeNames[j].trim()}">
                              <input type="hidden" name="child_id[]" value="${oldChild?.id ?? ''}">
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">Color</label>
                            <input class="form-control" value="${colorNames[i].trim()}" readonly>
                            <input type="hidden" name="child_color_id[]" value="${colorId}">
                           
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">Size</label>
                            <input class="form-control" value="${sizeNames[j].trim()}" readonly>
                            <input type="hidden" name="child_size_id[]" value="${sizeId}">
                        </div>

                       

                          <div class="col-lg-2">
                            <label class="form-label">SKU</label>
                            <input class="form-control" name="child_sku[]" value="${childSKU}" >
                        </div>

                          <div class="col-lg-2">
                            <label class="form-label">Price</label>
                            <input type="text" min="0" class="form-control price" step="0.01" name="child_price[]" value="${oldChild?.price??price.toFixed(2)}">
                        </div>
                        
                    </div>
                `);
                        });

                    });

                } else if (colorIds.length > 0) {

                    colorIds.forEach((colorId, i) => {
                        const oldChild = variantExists(colorId, null, null) ?? null;
                        const generatedSKU = `${parentSKU}-${variantSkuPart(colorNames[i], colorId, 'C')}`;
                        const childSKU = oldChild?.sku ?? generatedSKU;
                        $('#child_products').append(`
                <div class="col-lg-12 row mb-2 g-2">
                    <div class="col-lg-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="child_name[]" value="${name+'-'+colorNames[i].trim()}">
                          <input type="hidden" name="child_id[]" value="${oldChild?.id ?? ''}">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">Color</label>
                        <input class="form-control" value="${colorNames[i].trim()}" readonly>
                        <input type="hidden" name="child_color_id[]" value="${colorId}">
                    </div>


                  

                      <div class="col-lg-2">
                        <label class="form-label">SKU</label>
                        <input class="form-control" name="child_sku[]" value="${childSKU}" >
                    </div>

                      <div class="col-lg-2">
                        <label class="form-label">Price</label>
                        <input type="number" min="0" class="form-control price" step="0.01" name="child_price[]" value="${oldChild?.price?? price}">
                    </div>

                </div>
            `);
                    });

                } else if (sizeIds.length > 0) {
                    sizeIds.forEach((sizeId, j) => {
                        const oldChild = variantExists(null, sizeId, null) ?? null;
                        const generatedSKU = `${parentSKU}-${variantSkuPart(sizeNames[j], sizeId, 'S')}`;
                        const childSKU = oldChild?.sku ?? generatedSKU;
                        $('#child_products').append(`
                <div class="col-lg-12 row mb-2 g-2">
                    <div class="col-lg-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="child_name[]" value="${name+'-'+sizeNames[j].trim()}">
                          <input type="hidden" name="child_id[]" value="${oldChild?.id ?? ''}">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">Size</label>
                        <input class="form-control" value="${sizeNames[j].trim()}" readonly>
                        <input type="hidden" name="child_size_id[]" value="${sizeId}">
                    </div>

                    

                    <div class="col-lg-2">
                        <label class="form-label">SKU</label>
                        <input class="form-control" name="child_sku[]" value="${childSKU}" >
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">Price</label>
                        <input type="number" min="0" class="form-control price" step="0.01" name="child_price[]" value="${oldChild?.price??price}">
                    </div>
                </div>
            `);
                    });
                } else if (paperIds.length > 0) {
                    console.log(paperIds.length);
                    paperIds.forEach((paper, j) => {
                        const oldChild = variantExists(null, null, paper) ?? null;
                        const generatedSKU = `${parentSKU}-${variantSkuPart(paperNames[j], paper, 'P')}`;
                        const childSKU = oldChild?.sku ?? generatedSKU;
                        $('#child_products').append(`
                <div class="col-lg-12 row mb-2 g-2">
                    <div class="col-lg-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="child_name[]" value="${name+'-'+paperNames[j].trim()}">
                         <input type="hidden" name="child_id[]" value="${oldChild?.id ?? ''}">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">Paper Quality</label>
                        <input class="form-control" value="${paperNames[j].trim()}" readonly>
                        <input type="hidden" name="child_paper_id[]" value="${paper}">
                    </div>

                    

                    <div class="col-lg-2">
                        <label class="form-label">SKU</label>
                        <input class="form-control" name="child_sku[]" value="${childSKU}" >
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">Price</label>
                        <input type="number" min="0" step="0.01" class="form-control price" name="child_price[]" value="${oldChild?.price??price}">
                    </div>
                </div>`);

                    });
                }
                // console.log(paperIds.length);
            }

            appendChildProduct();
            //    , #discount_value,#discount_type, #sku, #name,#variant_type,#paper,#sale_price

            // Trigger updates dynamically
            $('#color, #size,#paper,#name').on(
                'change input', appendChildProduct);

            $('#mrp,#discount_type,#discount_value').on('input change', function() {
                setTimeout(changePrice, 0); // wait one microtask
            });


            function changePrice() {
                $('.price').val((parseFloat($('#sale_price').val())).toFixed(2));
            }



            function setDiscountRange() {
                const type = $('#discount_type').val();
                const mrp = parseInt($('#mrp').val());
                if (type === 'flat') {
                    $('#discount_value').attr('max', mrp);
                } else if (type === 'percent') {
                    $('#discount_value').attr('max', 100);
                } else {
                    $('#discount_value').removeAttr('max');
                }
            }
            setDiscountRange();
            $('#discount_type,#mrp').on('input', setDiscountRange);


            function calculatePrice() {
                const discountType = $('#discount_type').val(); // string
                const mrp = parseFloat($('#mrp').val()) || 0;
                const discount = parseFloat($('#discount_value').val()) || 0;

                let salePrice = mrp;

                if (discountType === 'percent') {
                    salePrice = mrp - (mrp * discount / 100);
                } else if (discountType === 'flat') {
                    salePrice = mrp - discount;
                }

                $('#sale_price').val(salePrice.toFixed(2));
            };

            $('#mrp,#discount_type,#discount_value').on('input', calculatePrice);

            function handleVariant() {
                if ($('#has_variant').val() == '0') {
                    $('#color_div,#size_div,#paper_div,#variant_type_div').addClass('d-none');
                    $('#size,color,paper').attr('disabled', 'disabled');
                } else {
                    $('#variant_type_div,#color_div,#size_div').removeClass('d-none');
                    $('#color,#size').removeAttr('disabled');
                    $('#paper_div').addClass('d-none');
                    $('#paper').attr('disabled', 'disabled');
                }

                showChildDiv();
                appendChildProduct();
            }
            handleVariant();

            $('#has_variant').on('change', handleVariant);


            $('#category_type').select2({
                placeholder: 'Search category type...',
                allowClear: true,
                width: '100%',
                // important for AJAX search
                ajax: {
                    url: "{{ route('category.types.select2') }}", // Laravel route
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // search query
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

        });
        $('#category').select2({
            placeholder: 'Search category...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('category.cat.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        type: $('#category_type').val() // search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('#subcategory').select2({
            placeholder: 'Select Subcategory...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('subcategory.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        type: $('#category').val() // search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('#product_type').select2({
            placeholder: 'Select Product Type...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('product-type.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('#unit').select2({
            placeholder: 'Select Unit...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('units.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
        $('#brand').select2({
            placeholder: 'Select Brand...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('brand.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        $('#color').select2({
            placeholder: 'Select Color...',
            allowClear: true,
            width: '100%',

            // important for AJAX search
            ajax: {
                url: "{{ route('color.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results

                    };
                },
                cache: true
            }
        });
        $('#size').select2({
            width: '100%',
            placeholder: 'Select Size...',
            allowClear: true,


            // important for AJAX search
            ajax: {
                url: "{{ route('sizes.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
        $('#paper').select2({
            width: '100%',
            placeholder: 'Select Paper Type...',
            allowClear: true,


            // important for AJAX search
            ajax: {
                url: "{{ route('paper_quality.select2') }}", // Laravel route
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });






        // Event listener for name field
        $('#name').on('input', function() {
            var name = $(this).val();
            var slug = name ? slugify(name) : null; // Generate slug only if name is not empty
            $('#slug').val(slug);
        });

        // $('#product_form').submit(function(e) {
        //     e.preventDefault(); // Prevent the form from submitting normally
        //     var formData = new FormData(this);

        //     // Make AJAX request
        //     $.ajax({
        //         url: '{{ route('product.products.update', $product->id) }}',
        //         method: 'PUT',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Success!',
        //                 text: response.message,
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     window.location.href =
        //                         '{{ route('product.products.index') }}';
        //                 }
        //             });
        //         },
        //         error: function(xhr, status, error) {
        //             // Parse the JSON response from the server
        //             try {
        //                 var responseObj = JSON.parse(xhr.responseText);
        //                 var errorMessages = responseObj.errors ? Object.values(responseObj
        //                     .errors).flat() : [responseObj.message];
        //                 var errorMessageHTML = '<ul>' + errorMessages.map(errorMessage =>
        //                     '<li>' + errorMessage + '</li>').join('') + '</ul>';

        //                 // Show error messages using SweetAlert
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Error!',
        //                     html: errorMessageHTML,
        //                 });
        //             } catch (e) {
        //                 console.error('Error parsing JSON response:', e);
        //                 // Show default error message using SweetAlert
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Error!',
        //                     text: 'An error occurred while processing your request. Please try again later.',
        //                 });
        //             }
        //         }

        //     });
        // });


        @if ($errors->any())
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`
                });
            });
        @endif

        // @if (!empty($product->image))



        //     var previousImageUrl = "{{ image($product->image) }}"; // Blade variable
        //     if (previousImageUrl) {
        //         $("#preview").append(
        //             '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
        //             '<img src="' + previousImageUrl +
        //             '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
        //             '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;" data-remove="remove_image">✕</button>' +
        //             '</div>'
        //         );
        //     }
        // @endif


        // $("#image").spartanMultiImagePicker({
        //     fieldName: 'image',
        //     maxCount: 1,
        //     rowHeight: '200px',
        //     groupClassName: 'col',
        //     maxFileSize: '',
        //     dropFileLabel: "Drop Here",
        //     onExtensionErr: function(index, file) {
        //         console.log(index, file, 'extension err');
        //         alert('Please only input png or jpg type file')
        //     },
        //     onSizeErr: function(index, file) {
        //         console.log(index, file, 'file size too big');
        //         alert('File size too big max:2MB');
        //     }
        // });

        @if (!empty($product->thumbnail_image))



            var previousImageUrl = "{{ image($product->thumbnail_image) }}"; // Blade variable
            if (previousImageUrl) {
                $("#thumbnail_image").append(
                    '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                    '<img src="' + previousImageUrl +
                    '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                    '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;" data-remove="remove_thumbnail_image">✕</button>' +
                    '</div>'
                );
            }
        @endif

        $("#thumbnail_image").spartanMultiImagePicker({
            fieldName: 'thumbnail_image',
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
                alert('File size too big max:2MB');
            }
        });

        $("#image").spartanMultiImagePicker({
            fieldName: 'image[]',
            maxCount: 3,
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
                alert('File size too big max:2MB');
            }
        });

        @if (!empty($product->images))

            var existingImages = @json($product->images);
            @foreach (json_decode($product->images) as $image)
                $("#preview").append(
                    '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                    '<input type="hidden" name="existing_images[]" value="{{ $image }}">' +
                    '<img src="{{ image($image) }}" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                    '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;">✕</button>' +
                    '</div>'
                );
            @endforeach
        @endif



        @if (!empty($product->size_chart_image))



            var previousImageUrl = "{{ image($product->size_chart_image) }}"; // Blade variable
            if (previousImageUrl) {
                $("#size_chart_image").append(
                    '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                    '<img src="' + previousImageUrl +
                    '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                    '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;" data-remove="remove_size_chart_image">✕</button>' +
                    '</div>'
                );
            }
        @endif

        $("#size_chart_image").spartanMultiImagePicker({
            fieldName: 'size_chart_image',
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
                alert('File size too big max:2MB');
            }
        });

        @if (!empty($product->meta_image))
            var previousImageUrl = "{{ image($product->meta_image) }}"; // Blade variable
            if (previousImageUrl) {
                $("#meta_image").append(
                    '<div class="existing-image-wrapper" style="position:relative; display:inline-block; margin:5px;">' +
                    '<img src="' + previousImageUrl +
                    '" style="height:200px; width:auto; border:1px solid #ccc; border-radius:5px;">' +
                    '<button type="button" class="btn-cancel-old bg-red px-1 py-0 " style="position:absolute; top:5px; right:5px;" data-remove="remove_meta_image">✕</button>' +
                    '</div>'
                );
            }
        @endif

        $("#meta_image").spartanMultiImagePicker({
            fieldName: 'meta_image',
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
                alert('File size too big max:2MB');
            }
        });

        // Universal handler for canceling old images
        $(document).on('click', '.btn-cancel-old', function() {
            var removeField = $(this).data('remove');
            if (removeField) {
                $('#' + removeField).val(1);
            }
            $(this).closest('.existing-image-wrapper').remove();
        });
    </script>
@endsection
