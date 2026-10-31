@extends('backend.layouts.master')

@section('meta')
    <title>View Product </title>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Product Management</h6>
            <p class="fw-semibold mb-0">View Child Product</p>
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
        @method('PUT')


        <div class=" ">
            <div class="  ">
                <div class="accordion-card-one accordion mb-6 shadow-sm rounded" id="accordionExample">
                    {{-- <div class="accordion-item"> --}}
                    {{-- <div class="accordion-header" id="headingOne">
                            <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                aria-controls="collapseOne">
                                <div class="addproduct-icon">
                                    <h6><i data-feather="info" class="add-info"></i><span>Basic Information</span></h6>
                                    <a href="javascript:void(0);"><i data-feather="chevron-down"
                                            class="chevron-down-add"></i></a>
                                </div>
                            </div>
                        </div> --}}
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            {{-- <div class="row">


                                    <div class="col-lg-5 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $product->name }}" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="mb-3 ">
                                            <label class="form-label">Slug<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ $product->slug }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <label for="sku" class="form-label">Product SKU</label>
                                        <input type="text" name="sku" class="form-control" placeholder="e.g. TS001"
                                            id="sku" value="{{ $product->sku }}" readonly>

                                    </div>

                                    <div class="col-md-3 mb-16">
                                        <label class="form-label text-sm mb-8">Category Type</label>
                                        <select name="category_type_id" id="category_type"
                                            class="form-control js-category-select">
                                            <option value="{{ $product->category_type_id }}" selected>
                                                {{ $product->category_type->name }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block category_id-error" style="display:none">
                                        </div>
                                    </div>
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



                                  
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Product Type</label>
                                        <select name="product_type_id" id="product_type"
                                            class="form-control js-product-type-select">
                                            <option value="{{ $product->product_type_id }}" selected>
                                                {{ $product->product_type->name }}</option>
                                        </select>
                                        <div class="invalid-feedback d-block product_type_id-error" style="display:none">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Unit</label>
                                        <select name="unit_id" id="unit"
                                            class="form-control js-product-type-select">
                                            <option value="{{ $product->unit_id }}" selected>{{ $product->unit->name }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback d-block brand_type_id-error" style="display:none">
                                        </div>
                                    </div>



                                    {{-- <div class="row"> --}}
                            {{-- <div class="col-lg-3 col-sm-6 col-12">
                                        <label class="form-label text-sm mb-8">Brand</label>
                                        <select name="brand_id" id="brand"
                                            class="form-control js-product-type-select">
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
                                                value="{{ $product->cost_price }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 add-product">
                                            <label class="form-label">MRP Price<span class="star-sign">*</span></label>
                                            <input type="number"  class="form-control" min="0"
                                                value="{{ $product->mrp }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label">Discount</label>
                                        <div class="input-group">
                                            <select name="discount_type" class="form-select" id="discount_type">
                                                <option value="{{ $product->discount_type }}">
                                                   {{ $product->discount_type === 'flat' ? 'Flat' : ($product->discount_type === 'percent' ? 'Percentage(%)' : 'No discount') }}  </option>
                                               
                                            </select>
                                            <input type="number" class="form-control" 
                                                value="{{ $product->discount_value }}" required readonly>
                                        </div>
                                    </div>



                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Sale Price<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" name="price" id="sale_price"
                                                value="{{ $product->price }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3 ">
                                            <label class="form-label">Materials<span class="star-sign">*</span></label>
                                            <input type="text" class="form-control" name="material"
                                                value="{{ $product->material }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6 col-12 col">
                                        <label for="has_variant" class="form-label">Has Variant?</label>
                                        <select class="form-select " name="has_variant" id="has_variant" >
                                            <option >{{ $product->has_variants == 0 ? 'NO' : 'YES' }}
                                            </option>
                                           

                                        </select>

                                    </div>  --}}


                            <div class="col-12 row shadow-lg m-auto rounded mb-3 dark-mode p-3 ">

                                <h6>All Variants</h6>
                                @foreach ($child_products as $product)
                                    <div class="col-lg-12 row mb-2 g-2">
                                        <div class="col-lg-2">
                                            <label class="form-label">Name</label>
                                            <input class="form-control" value="{{ $product->name }}" readonly>

                                        </div>
                                        @if ($product->color_id)
                                            <div class="col-lg-2">
                                                <label class="form-label">Color</label>
                                                <input class="form-control" value="{{ $product->color->name ?? null }}"
                                                    readonly>

                                            </div>
                                        @endif
                                        @if ($product->size_id)
                                            <div class="col-lg-2">
                                                <label class="form-label">Size</label>
                                                <input class="form-control" value="{{ $product->size->name ?? null }}"
                                                    readonly>

                                            </div>
                                        @endif
                                        @if ($product->paper_id)
                                            <div class="col-lg-2">
                                                <label class="form-label">Size</label>
                                                <input class="form-control"
                                                    value="{{ $product->paper_quality->name ?? null }}" readonly>

                                            </div>
                                        @endif



                                        <div class="col-lg-2">
                                            <label class="form-label">SKU</label>
                                            <input class="form-control" value="{{ $product->sku }}" readonly>
                                        </div>

                                        <div class="col-lg-2">
                                            <label class="form-label">Price</label>
                                            <input type="number" min="0" class="form-control" name="child_price[]"
                                                value="{{ $product->price }}" readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>




                        </div>

                    </div>
                </div>
            </div>
            {{-- </div> --}}

        </div>
        {{-- </div>
        </div> --}}

    </form>
@endsection
