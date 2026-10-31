
@extends('frontend.layouts.master')
@section('meta')
    <title>Cart Details | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">শপিং কার্ট</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a href="{{ route('home') }}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span>শপিং কার্ট</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

<!-- cart section start -->
<section class="cart__section section--padding">
    <div class="container-fluid">
        <div class="cart__section--inner">
            <form action="#">
                <!--<h2 class="cart__title mb-40">শপিং কার্ট</h2>-->
                <div class="row">
                      @if(session('cart.items') && count(session('cart.items')) > 0)
                    <div class="col-lg-8">
                        <div class="cart__table">
                          
                                <table class="cart__table--inner">
                                    <thead class="cart__table--header">
                                    <tr class="cart__table--header__items">
                                        <th></th>
                                        <th class="cart__table--header__list">পণ্য</th>
                                        
                                        <th class="cart__table--header__list">পরিমাণ</th>
                                        <th class="cart__table--header__list">মোট</th>
                                    </tr>
                                    </thead>
                                    <tbody class="cart__table--body">
                                    @foreach(session('cart.items', []) as $item)
                                       
                                        <tr class="cart__table--body__items border">
                                            <td>
                                                <button class="cart__remove--btn remove-from-cart" aria-label="remove button" type="button" data-product-id="{{$item['id'] }}">
                                                        <i class="ri-close-fill text-danger"></i>
                                                    </button>
                                            </td>
                                            <td class="cart__table--body__list ">
                                                <div class="cart__product d-flex align-items-center">
                                                    
                                                    <div class="cart__thumbnail">
                                                        <a href="{{ route('product.details', ['slug_or_id' => $item['slug'] ?? $item['id']]) }}"><img class="border-radius-5" src="{{ asset($item['thumb_image']) }}" alt="cart-product"></a>
                                                    </div>
                                                    <div class="cart__content">
                                                        <h3 class="product__description--name h4"><a href="{{ route('product.details', ['slug_or_id' => $item['slug'] ?? $item['id']]) }}">{{ $item['bangla_name'] }}</a></h3>

                                                        @if(!empty($item['authors']))
                                                            <p class="text-red-english-moja">
                                                                
                                                                
                                                                 @foreach(collect($item['authors'])->take(2) as $author)
                                                                   {{ $loop->first ? '' : ', ' }}{{ $author['name'] }}
                                                                 @endforeach
                                                              
                                                            </p>
                                                        @else
                                                            <p class="text-red-english-moja mb-0">
                                                                {{ $item['publisher_name'] }}
                                                            </p>
                                                             <p class="cart__price mb-0">{{ formatPrice($item['current_price']) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                      
                                            <td class="cart__table--body__list">
                                                <div class="quantity__box">
                                                    <button type="button" class="quantity__value quickview__value--quantity  update-cart-btn  decrease " aria-label="quantity value" value="Decrease Value" data-product-id="{{ $item['id'] }}">-</button>
                                                    <label>
                                                        <input type="number" class="quantity__number quickview__value--number" value="{{ $item['quantity'] }}" data-max-quantity="{{get_option('max_order')}}"/>
                                                    </label>
                                                    <button type="button" class="quantity__value quickview__value--quantity update-cart-btn increase " aria-label="quantity value" value="Increase Value" data-product-id="{{ $item['id'] }}">+</button>
                                                </div>
                                            </td>
                                            <td class="cart__table--body__list">
                                                <span class="cart__price end" id="total_{{$item['id']}}">{{ formatPrice($item['current_price']*$item['quantity']) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                  <div class="continue__shopping d-flex justify-content-between">
                                <a class="cart__summary--footer__btn primary__btn cart " href="{{ route('home') }}">শপিং চালিয়ে যান</a>
                               
                                    <button id="clearCartBtn" class="continue__shopping--clear" type="button">কার্ট খালি করুন</button>
                              
                            </div>
                            
                            
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                       
                            <div class="cart__summary border-radius-10">
                                <div class="cart__summary--total mb-20">
                                    <table class="cart__summary--total__table">
                                        <tbody>
                                        <tr class="cart__summary--total__list">
                                            <td class="cart__summary--total__title text-left">সাবটোটাল</td>
                                            <td id="subtotal" class="cart__summary--amount text-right">{{formatPrice($cartTotal)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart__summary--footer">
                                    <p class="cart__summary--footer__desc">চেকআউটে শিপিং, কর এবং অন্যান্য  হিসাব করা হবে</p>
                                    <ul>
                                       <li><a class="cart__summary--footer__btn success__btn fw-bold text-center w-100 checkout" href="{{route('checkout.order.form')}}">চেক আউট</a></li>
                                    </ul>
                                </div>
                            </div>
                      
                    </div>
                @else
                        
                        <div class="col-md-12">
                            
                                    <div class="text-warning text-center" style="padding: 20px;">
                                        <div class="emptycart">
                                            <img src="{{asset('theme/frontend/assets/img/emptycart-1.png')}}" alt="Empty_cart_img">
                                        </div>
                                        <h3 class="mt-5" style="font-size: 24px; font-weight: bold;">কার্টটি খালি!</h3>
                                        <p class="mt-3" style="font-size: 16px; font-weight: bold; color: #FF0000;">দয়া
                                            করে প্রথমে বই নির্বাচন করুন।</p>
                                   
                                    <a class="cart__summary--footer__btn primary__btn cart  mt-3" href="{{ route('home') }}">শপিং
                                        চালিয়ে যান</a>
                                    </div>
                        </div>
                        @endif
                </div>
            </form>


        </div>
    </div>





</section>
<!-- cart section end -->

<!-- Start product section -->
<section class="product__section product__section--style3">
    <div class="container-fluid product3__section--container">
        <div class="section__heading text-center mb-50">
            <h2 class="section__heading--maintitle">নতুন বই</h2>
        </div>
        <div class="product__section--inner product__swiper--column4__activation swiper" id="latest_book">


            <div class="loader" style="display: none;">


            </div>

        </div>
    </div>
</section>
<!-- End product section -->



@endsection
@section('scripts')


    <script>





        $(document).ready(function() {

            function productSlider() {
                var swiper = new Swiper(".product__swiper--column4__activation", {
                    slidesPerView: 5,
                    loop: true,
                    clickable: false,
                    spaceBetween: 30,
                    breakpoints: {
                        1200: {
                            slidesPerView: 5,
                        },
                        992: {
                            slidesPerView: 4,
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                        280: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        0: {
                            slidesPerView: 1,
                        },
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                });

            }

            // Function to fetch latest products
            function fetchLatestProducts() {
                var sectionId = 'latest_book';
                showLoader(sectionId);
                $.ajax({
                    url: '{{ route("product.latest") }}',
                    type: 'GET',
                    success: function(response) {
                        $('#latest_book').html(response);
                        productSlider()
                        observeLazyLoadImages();
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                    },
                    complete: function() {
                        hideLoader(sectionId);
                    }
                });
            }



            // Observer options
            var observerOptions = {
                root: null, // use the viewport
                rootMargin: '0px',
                threshold: 0.1 // trigger when 10% of the element is visible
            };

            // Observer callback
            var observerCallback = function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        if (entry.target.id === 'latest_book') {
                            fetchLatestProducts();
                        }
                        observer.unobserve(entry.target);
                    }
                });
            };

            // Create an Intersection Observer
            var observer = new IntersectionObserver(observerCallback, observerOptions);

            // Observe the elements
            observer.observe(document.getElementById('latest_book'));

        });




    </script>
@endsection
