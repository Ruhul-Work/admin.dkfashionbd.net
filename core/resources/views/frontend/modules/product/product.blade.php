


{{--@foreach($products as $product)--}}


    <div class="col mb-30-custom mb-30">

        @if($product->stock_status == 'in_stock')
            <div class="box">

                        <span class="wdp-ribbon">
                    <!--<img src="{{image('theme/frontend/assets/img/icon/live-1.png')}}" alt="Live">-->
                    <h3 class="custom-btn btn-11">LIVE <div class="dot"></div></h3>
                    <div class="video__icon">
                        <!--<div class="circle--outer"></div>-->
                        <div class="circle--inner"></div>
                    </div>
                     </span>


            </div>
        @endif

          @php
                    $discountInfo = calculateDiscount($product);
                @endphp
       
           @if($product->show_discount ==1  && $discountInfo['discountPercentage']>0 )


             
                
               

            <div class="box-2">
                    <span class="discount-ribbon">

                            {{number_format($discountInfo['discountPercentage'], 0)}}% ছাড়

                    </span>
            </div>
        @endif
       
        <div class="product__items">
            <div class="product__items--thumbnail">


                <a class="book product__items--link"  href="{{ route('product.details', ['slug_or_id' => $product->slug]) }}">

                    <img class="product__items--img product__primary--img lazy-load" data-loaded="false" data-src="{{ image($product->thumb_image)}}" src="{{ asset('theme/frontend/assets/img/default/book.png') }}" alt="product-img">

                </a>
                @if($product->stock_status =='out_of_stock')
                    <div class="overlay-custom" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'"></div>
                    <div class="stock-out-ribbon" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'">
                        <img src="{{image('theme/frontend/assets/img/product/sold_out.png')}}" alt="Sold Out">
                    </div>
                @endif
                @if($product->stock_status == 'upcoming')
                    <div class="overlay-custom" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'"></div>
                    <div class="next-edition-ribbon" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'">
                        <img src="{{image('theme/frontend/assets/img/product/upcoming.png')}}" alt="Upcoming">
                    </div>
                @endif
                 @if($product->stock_status == 'next_edition')
                    <div class="overlay-custom" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'"></div>
                    <div class="next-edition-ribbon" onclick="window.location.href='{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}'">
                        <img src="{{image('theme/frontend/assets/img/product/next-edition.png')}}" alt="Upcoming">
                    </div>
                @endif
                <div class="wishlist-product">
                    
                    
                     <button class="wishlist-button add-to-wishlist 
        {{ isProductInWishlist($product->id) ? 'active' : '' }}" 
        data-product-id="{{ $product->id }}">
        
        <i class="ri-heart-3-line"></i> <!-- Default empty heart -->
        <i class="ri-heart-3-fill"></i> <!-- Filled heart, visible when active -->
    </button>

                    <!--<button class="wishlist-button add-to-wishlist"   {{ isProductInWishlist($product->id) ? 'active' : '' }}" -->
                    <!--data-product-id="{{ $product->id }}">-->
                    <!--    <i class="ri-heart-3-line"></i>-->
                    <!--    <i class="ri-heart-3-fill"></i>-->
                    <!--</button>-->
                </div>



            </div>

            <div class="product__items--content text-center">
               <h3 class="product__items--content__title h4 name_height mb-0">
    <a href="{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}">
        {{ Str::limit($product->bangla_name, 50) }}
    </a>
</h3>


                @if($product->product_type=='book')

                    @if($product->authors->isNotEmpty())
                       
                        @foreach($product->authors->take(2) as $author)
                                <a class="product__Author_name" href="{{ route('author.single',  ['slug' => $author->slug ??$author->id]) }}">
                                     <i class="ri-user-2-fill"></i> {{ $author->name }}
                                </a>
                                @if(!$loop->last), @endif

                        @endforeach
                    @endif

                @else
                 @if($product->categories->isNotEmpty())
                        <span class="product__Author_name">
                              <i class="ri-grid-fill"></i>
                    @foreach($product->categories->take(2) as $category)

                                <a class="product__Author_name" href="{{ route('category.single', ['slug' => $category->slug ?? $category->id]) }}">
                     {{ $category->name }}
                            </a>
                                @if(!$loop->last), @endif
                            @endforeach
                     </span>
                    @endif
                @endif
                <div class="product__items--price">
                    <span class="current__price">{{ formatPrice(priceAfterDiscount($product)) }}</span>
                </div>
                <hr>
                    @if($product->stock_status =='in_stock')
                <ul class="product__items--action d-flex justify-content-between">
                    
                     
                    <li class="product__items--action__list">
                        <a class="product__items--action__btn add__to--cart add-to-cart " data-product-id="{{$product->id}}" data-quantity="1" >
                            <i class="ri-shopping-cart-line"></i>
                            <span class="add__to--cart__text">অর্ডার করুন</span>
                        </a>
                    </li>
          
                    
                    
                    

                    <li class="product__items--action__list">
                        <a class="product__items--action__btn" data-open="modal1"  href="{{ route('product.details', ['slug_or_id' => $product->slug ?? $product->id]) }}">
                            <i class="ri-book-open-line"></i>
                            <span class="mobile-hide">বিস্তারিত</span>
                        </a>
                    </li>
                </ul>
                
                          @else
                          
                           <ul class="product__items--action d-flex justify-content-center">
                    
                     
           

                    <li class="product__items--action__list">
                        <a class="product__items--action__btn" data-open="modal1"  href="{{ route('product.details', ['slug_or_id' => $product->slug ?? $product->id]) }}">
                            <i class="ri-book-open-line"></i>
                            <span class="mobile-hide">বিস্তারিত</span>
                        </a>
                    </li>
                </ul>
                @endif
                          
            </div>
        </div>
    </div>


{{--@endforeach--}}
