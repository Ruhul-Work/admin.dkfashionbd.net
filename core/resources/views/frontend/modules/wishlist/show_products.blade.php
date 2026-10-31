

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
                        <h1 class="breadcrumb__content--title mb-25">উইশ লিস্ট</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a href="{{ route('home') }}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span>উইশ লিস্ট</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->


    <!-- cart section start -->
    <section class="cart__section section--padding">
        <div class="container">
            <div class="cart__section--inner">


                <form action="#">

                    <h2 class="cart__title mb-40">উইশ লিস্ট তালিকা</h2> <!-- Wishlist title -->
                    <div class="cart__table">
                        <table class="cart__table--inner">
                            <thead class="cart__table--header">
                            <tr class="cart__table--header__items">
                                <th class="cart__table--header__list">পণ্য</th>
                                <th class="cart__table--header__list">মূল্য</th>
                                <th class="cart__table--header__list text-center">স্টক</th>
                                <th class="cart__table--header__list text-right">কার্টে যোগ করুন</th>
                            </tr>
                            </thead>
                            <tbody class="cart__table--body">
                            @if ($wishlistItems->count() > 0)
                                @foreach ($wishlistItems as $item)
                                    <tr class="cart__table--body__items">
                                        <td class="cart__table--body__list">
                                            <div class="cart__product d-flex align-items-center">
                                                <button class="cart__remove--btn remove-from-wishlist" aria-label="remove button" type="button" data-product-id="{{$item->product->id}}">
                                                    <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16px" height="16px"><path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"/></svg>
                                                </button>
                                                <div class="cart__thumbnail">
                                                    <a href="{{ route('product.details', $item->product->slug) }}">
                                                        <img class="border-radius-5" src="{{ image($item->product->thumb_image) }}" alt="cart-product">
                                                    </a>
                                                </div>
                                                <div class="cart__content">
                                                    <h3 class="product__description--name h4">
                                                        <a href="{{ route('product.details', $item->product->slug) }}">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </h3>
                                                    @foreach($item->product->authors as $author)
                                                        {{ $loop->first ? '' : ', ' }}{{ $author->name }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart__table--body__list">
                                            <span class="cart__price">৳{{ number_format($item->product->current_price, 2) }}</span>
                                        </td>
                                        <td class="cart__table--body__list text-center">
                                            <span class="in__stock text__secondary"> 
                                            @if($item->product->stock_status === 'in_stock')
                                                    In Stock
                                                @elseif($item->product->stock_status === 'upcoming')
                                                   Upcoming
                                                   
                                                   @elseif($item->product->stock_status === 'next_edition')
                                                  wait for next edition
                                                @else
                                                   Out Of Stock
                                                @endif</span>
                                        </td>
                                        <td class="cart__table--body__list text-right">
                                            <a class="wishlist__cart--btn primary__btn add-to-cart"  data-product-id="{{$item->product->id}}" data-quantity="1">অর্ডার করুন</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-red-english-moja"><p>কোনো পণ্য পাওয়া যায়নি</p></td> <!-- No products found -->
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="continue__shopping d-flex justify-content-between">
                            <a class="continue__shopping--link" href="{{ route('home') }}">শপিং চালিয়ে যান</a> <!-- Continue shopping -->
                            <a class="continue__shopping--clear" href="{{ route('home') }}">সব পণ্য দেখুন</a> <!-- View All Products -->
                        </div>
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

