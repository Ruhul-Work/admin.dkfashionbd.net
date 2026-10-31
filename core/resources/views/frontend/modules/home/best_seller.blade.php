
    <div class="swiper-wrapper">

            @forelse($bestSellers as $product)
            <div class="swiper-slide swiper-custom-padding">

                @include('frontend.modules.product.product')
{{--                <div class="col mb-30-custom mb-30">--}}
{{--                    @if($bestSeller->stock_status == 'in_stock')--}}
{{--                        <div class="box">--}}
{{--                            <span class="wdp-ribbon">--}}
{{--                                <img src="{{image('theme/frontend/assets/img/icon/live.png')}}" alt="Live">--}}
{{--                                <div class="video__icon">--}}
{{--                                    <div class="circle--outer"></div>--}}
{{--                                    <div class="circle--inner"></div>--}}
{{--                                </div>--}}
{{--                            </span>--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    @if($bestSeller->show_discount == 1)--}}
{{--                        <div class="box-2">--}}
{{--                            <span class="discount-ribbon">--}}
{{--                                @if($bestSeller->discount_type == 'amount')--}}
{{--                                    ৳{{ number_format($bestSeller->discount_amount, 0) }} ছাড়--}}
{{--                                @else--}}
{{--                                    {{number_format($bestSeller->discount_amount, 0)}} % ছাড়--}}
{{--                                @endif--}}
{{--                            </span>--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    <div class="product__items">--}}
{{--                        <div class="product__items--thumbnail">--}}
{{--                            <a class="book product__items--link" href="product-details.html">--}}
{{--                                <img class="product__items--img product__primary--img lazy-load" data-loaded="false"--}}
{{--                                     data-src="{{image($bestSeller->thumb_image) }}"--}}
{{--                                     src="{{ image('theme/frontend/assets/img/default/book.png') }}" alt="product-img">--}}
{{--                            </a>--}}
{{--                            @if($bestSeller->stock_status == 'out_of_stock')--}}
{{--                                <div class="overlay-custom"></div>--}}
{{--                                <div class="stock-out-ribbon">--}}
{{--                                    <img src="{{image('theme/frontend/assets/img/product/sold_out.png')}}"--}}
{{--                                         alt="Sold Out">--}}
{{--                                </div>--}}
{{--                            @endif--}}

{{--                            @if($bestSeller->stock_status == 'upcoming')--}}
{{--                                <div class="overlay-custom"></div>--}}
{{--                                <div class="next-edition-ribbon">--}}
{{--                                    <img src="{{image('theme/frontend/assets/img/product/next-edition.png')}}"--}}
{{--                                         alt="Upcoming">--}}
{{--                                </div>--}}
{{--                            @endif--}}

{{--                            <div class="wishlist-product">--}}
{{--                                <button class="wishlist-button" onclick="toggleWishlist(this)">--}}
{{--                                    <i class="ri-heart-3-line"></i>--}}
{{--                                    <i class="ri-heart-3-fill"></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="product__items--content text-center">--}}
{{--                            <h3 class="product__items--content__title h4 mb-0">--}}
{{--                                <a href="product-details.html">{{ $bestSeller->bangla_name }}</a>--}}
{{--                            </h3>--}}
{{--                            <a class="product__Author_name" href="">--}}
{{--                                <i class="ri-user-2-fill"></i> {{$bestSeller->authors->pluck('name')->implode(',')}}--}}
{{--                            </a>--}}
{{--                            <div class="product__items--price">--}}
{{--                                <span class="current__price">৳{{ $bestSeller->current_price }}</span>--}}
{{--                            </div>--}}
{{--                            <hr>--}}
{{--                            <ul class="product__items--action d-flex justify-content-center">--}}
{{--                                <li class="product__items--action__list">--}}
{{--                                    <a class="product__items--action__btn add__to--cart" href="cart.html">--}}
{{--                                        <i class="ri-shopping-cart-line"></i>--}}
{{--                                        <span class="add__to--cart__text">অর্ডার করুন</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li class="product__items--action__list">--}}
{{--                                    <a class="product__items--action__btn" data-open="modal1" href="javascript:void(0)">--}}
{{--                                        <i class="ri-book-open-line"></i>--}}
{{--                                        <span class="mobile-hide">বিস্তারিত</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
            @empty

                    <div class="container mt-5">
                        <div class="card card-body d-flex justify-content-center align-items-center" style="height: 200px">
                            <h5 class="card-title">No product available</h5>
                        </div>
                    </div>

            @endforelse

    </div>
    <div class="swiper__nav--btn swiper-button-next"></div>
    <div class="swiper__nav--btn swiper-button-prev"></div>





