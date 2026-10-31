<div class=" stationary__swiper swiper">
    <div class="swiper-wrapper">


        @foreach($StationaryProducts as $product)
        <div class="swiper-slide swiper-custom-padding">

            @include('frontend.modules.product.product')
{{--            <div class="col mb-20-custom mb-30">--}}


{{--                @if($StationaryProduct->stock_status == 'in_stock')--}}
{{--                    <div class="box">--}}

{{--                        <span class="wdp-ribbon">--}}
{{--                    <img  src="{{image('theme/frontend/assets/img/icon/live.png')}}" alt="Live">--}}
{{--                    <div class="video__icon">--}}
{{--                        <div class="circle--outer"></div>--}}
{{--                        <div class="circle--inner"></div>--}}
{{--                    </div>--}}
{{--                     </span>--}}


{{--                    </div>--}}
{{--                @endif--}}

{{--                @if($StationaryProduct->show_discount ==1)--}}
{{--                    <div class="box-2">--}}
{{--                    <span class="discount-ribbon">--}}
{{--                        @if($StationaryProduct->discount_type=='amount')--}}
{{--                            ৳{{ number_format($StationaryProduct->discount_amount, 0) }} ছাড়--}}
{{--                        @else--}}
{{--                            {{number_format($StationaryProduct->discount_amount, 0)}}% ছাড়--}}
{{--                        @endif--}}
{{--                    </span>--}}
{{--                    </div>--}}
{{--                @endif--}}



{{--                <div class="product__items ">--}}
{{--                    <div class="product__items--thumbnail">--}}
{{--                        <a class="product__items--link" href="product-details.html">--}}
{{--                            <img class="product__items--img product__primary--img lazy-load" data-loaded="false"--}}
{{--                                data-src="{{image($StationaryProduct->thumb_image) }}" src="{{ asset('theme/frontend/assets/img/default/book.png') }}"--}}
{{--                                 alt="product-img">--}}

{{--                        </a>--}}
{{--                        @if($StationaryProduct->stock_status =='out_of_stock')--}}
{{--                        <div class="overlay-custom"></div>--}}
{{--                        <div class="stock-out-ribbon">--}}
{{--                            <img src="{{image('theme/frontend/assets/img/product/sold_out.png')}}" alt="Sold Out">--}}
{{--                        </div>--}}
{{--                        @endif--}}

{{--                        @if($StationaryProduct->stock_status == 'upcoming')--}}
{{--                            <div class="overlay-custom"></div>--}}
{{--                            <div class="next-edition-ribbon">--}}
{{--                                <img src="{{image('theme/frontend/assets/img/product/next-edition.png')}}" alt="Upcoming">--}}
{{--                            </div>--}}
{{--                        @endif--}}



{{--                        <div class="wishlist-product">--}}
{{--                            <button class="wishlist-button" onclick="toggleWishlist(this)">--}}
{{--                                <i class="ri-heart-3-line"></i>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="product__items--content text-center">--}}
{{--                        <h3 class="product__items--content__title h4 mb-0"><a--}}
{{--                                href="product-details.html">{{ $StationaryProduct->bangla_name }}</a></h3>--}}
{{--                        <a class="product__Author_name" href=""><i--}}
{{--                                class="ri-user-2-fill"></i> ষ্টেশনারী</a>--}}
{{--                        <div class="product__items--price">--}}
{{--                            <!-- <span class="old__price">৳278</span> -->--}}
{{--                            <span class="current__price">৳{{ $StationaryProduct->current_price }}</span>--}}
{{--                        </div>--}}


{{--                        <hr>--}}

{{--                        <ul class="product__items--action d-flex justify-content-center">--}}
{{--                            <li class="product__items--action__list">--}}
{{--                                <a class="product__items--action__btn add__to--cart"--}}
{{--                                   href="cart.html">--}}
{{--                                    <i class="ri-shopping-cart-line"></i>--}}
{{--                                    <span class="add__to--cart__text">অর্ডার করুন</span>--}}
{{--                                </a>--}}
{{--                            </li>--}}

{{--                            <li class="product__items--action__list">--}}
{{--                                <a class="product__items--action__btn" data-open="modal1"--}}
{{--                                   href="javascript:void(0)">--}}
{{--                                    <i class="ri-book-open-line"></i>--}}
{{--                                    <span class="mobile-hide">বিস্তারিত</span>--}}
{{--                                </a>--}}
{{--                            </li>--}}

{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}


        </div>

        @endforeach

    </div>
</div>
<div class="swiper__nav--btn style3 swiper-button-next"></div>
<div class="swiper__nav--btn style3 swiper-button-prev"></div>




