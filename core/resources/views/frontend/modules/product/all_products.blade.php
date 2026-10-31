@if($products->count()>0)
    <div class="tab_content">
        <div id="product_grid" class="tab_pane active show">
            <div class="product__section--inner product__grid--inner">
                <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-md-3 row-cols-2 mb--n30" >

                    @foreach($products as $product)
                    @include('frontend.modules.product.product')
                    @endforeach


                </div>
            </div>


        </div>

    </div>


    @include('frontend.modules.pagination.pagination_design', ['items' => $products])

@else
    <div class="col-md-12">
        <h2 class="text-center">No Products Found</h2>
    </div>
@endif

