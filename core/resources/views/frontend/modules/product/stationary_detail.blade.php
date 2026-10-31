@extends('frontend.layouts.master')
@section('meta')
    <title>{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ get_option('title') }}</title>

    <meta property="og:title" content="{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ strtolower($product->meta_title ?? get_option('title')) }}">
    <meta property="og:description" content="{{ strip_tags($product->meta_description ?? get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ image($product->meta_image ?? get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('company_name') }}">

    <!-- Add more Open Graph tags as needed -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ strtolower($product->meta_title ?? get_option('title')) }}">
    <meta name="twitter:description" content="{{ strip_tags($product->meta_description ?? get_option('description')) }}">
    <meta name="twitter:image" content="{{ image($product->meta_image ?? get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->
@endsection

@section('content')
    <!-- Start product details section -->
    <section class="product__details--section section--padding ">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row row-cols-lg-2 row-cols-md-2 single-book-card">
                        <div class="col">
                            <div class="product__details--media">
                                <div class="product__media--preview swiper">
                                    <div class="swiper-wrapper">
                                        @foreach($product->pages as $page)
                                            <div class="swiper-slide">
                                                <div class="product__media--preview__items">
                                                    <a class="product__media--preview__items--link glightbox" data-gallery="product-media-preview" href="{{asset($page->pages_photos) }}">
                                                        <img class="product__media--preview__items--img" src="{{ asset($page->pages_photos) }}" alt="product-media-img">
                                                    </a>
                                                    <div class="product__media--view__icon">
                                                        <a class="product__media--view__icon--link glightbox" href="{{asset($page->pages_photos) }}" data-gallery="product-media-preview">
                                                            <svg class="product__media--view__icon--svg" xmlns="http://www.w3.org/2000/svg" width="22.51" height="22.443" viewBox="0 0 512 512">
                                                                <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"></path>
                                                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="product__media--nav swiper">
                                    <div class="swiper-wrapper">
                                        @foreach($product->pages as $page)
                                            <div class="swiper-slide">
                                                <div class="product__media--nav__items">
                                                    <img class="product__media--nav__items--img" src="{{ asset($page->pages_photos) }}" alt="product-nav-img">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper__nav--btn swiper-button-next"></div>
                                    <div class="swiper__nav--btn swiper-button-prev"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            @include('frontend.modules.product.campaign_offer_time')

                            <div class="product__details--info py-3">

                                    <h2 class="product__details--info__title mb-15">{{$product->bangla_name}}</h2>
                                    @include('frontend.modules.product.rating')
                                    @php
                                        $discountInfo = calculateDiscount($product);
                                    @endphp

                                       <div class="product__details--info__price mb-10 mt-3">
                                    @if (priceAfterDiscount($product) < $product->mrp_price)
                                    <span class="old__price">৳{{ $product->mrp_price }}</span>
                                    @endif
                                    <span class="current__price">{{ formatPrice(priceAfterDiscount($product)) }}</span>
                                    
                                      @if($product->show_discount ==1  && $discountInfo['discountPercentage']>0 )
                                    <span class="text-primary discount">
                                        ইনস্ট্যান্ট {{ formatPrice($discountInfo['discountAmount']) }} সেভ করুন
                                        ({{ $discountInfo['discountPercentage'] }}% ছাড়)
                                    </span>
                                    
                                     @endif
                                </div>

                                    <hr style="margin: 1rem 0;">

                                    <div class="product__details--info__meta">



                                            @isset($product->publisher)
                                                <p class="product__details--info__meta--list">ব্র্যান্ড:
                                                    <a href="{{route('publisher.single',['slug'=>$product->publisher->slug ?:$product->publisher->id])}}" class="text-red-english-moja">{{ $product->publisher->name }}</a>
                                                </p>
                                            @endisset



                                    @isset($product->edition)
                                            <p class="product__details--info__meta--list">সংস্করণ:
                                                <span><strong class="text-red-english-moja">{{ $product->edition }}</strong></span>
                                            </p>
                                        @endisset

                                            <p class="product__details--info__meta--list">
                                                ক্যাটাগরি:
                                                @foreach($product->categories as $category)
                                                    <a href="{{route('category.single',['slug' => $category->slug ?: $category->id])}}" class="category-link">
                                                        {{ $category->name }}
                                                    </a>{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            </p>


                                        <p class="product__details--info__meta--list "><i class="ri-book-3-line text-red-english-moja"></i> পৃষ্ঠা :{{ $product->pages_no }}, কভার: {{ $product->cover_type }}
                                        </p>

                                        <div class="guarantee__safe--checkout">
                                            <h5 class="guarantee__safe--checkout__title text-green-english-moja"><i class="ri-bookmark-3-fill"></i> সমগ্র বাংলাদেশ <strong class="text-red-english-moja">ক্যাশ অন ডেলিভারি মাত্র {{get_option('shipping_charge')}}/- টাকা</strong> (বই হাতে পাওয়ার পর মূল্য পরিশোধের সুযোগ)</h5>

                                            <h5 class="guarantee__safe--checkout__title text-green-english-moja"><i class="ri-bookmark-3-fill"></i> অনলাইনে <strong class="text-red-english-moja">bKash</strong> অর্ডার করলে কুরিয়ার খরচ একদম ফ্রি (বই পৌঁছে যাবে নিকটস্থ সুন্দরবন কুরিয়ারে)</h5>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="product__variant">
                                        <div
                                            class="product__variant--list quantity d-flex align-items-center mb-20">
                                            @if($product->stock_status == 'in_stock')
                                                <div class="quantity__box">
                                                    <button type="button"
                                                            class="quantity__value quickview__value--quantity decrease "
                                                            aria-label="quantity value" value="Decrease Value">-
                                                    </button>
                                                    <label>
                                                        <input type="number" class="quantity__number quickview__value--number"
                                                               value="1" data-max-quantity="{{get_option('max_order')}}" />
                                                    </label>
                                                    <button type="button"
                                                            class="quantity__value quickview__value--quantity increase"
                                                            aria-label="quantity value" value="Increase Value">+
                                                    </button>
                                                </div>


                                                <button class="quickview__cart--btn success__btn add-to-cart"
                                                        data-product-id="{{ $product->id }}" data-quantity="1" ><i class="ri-shopping-cart-line"></i> কার্টে নিন 
                                                </button>


                                                <button class="quickview__cart--btn primary__btn add-to-cart buy-now"
                                                        data-product-id="{{ $product->id }}" data-quantity="1"  >
                                                    <i class="ri-flashlight-fill"></i> এখনই কিনুন
                                                </button>
                                            @elseif($product->stock_status =='out_of_stock')
                                                <button class="quickview__cart--btn primary__btn w-100" style="margin-left: 0 !important;">
                                                    <i class="ri-close-circle-line"></i> স্টক আউট
                                                </button>

                                            @else
                                                <button class="quickview__cart--btn success__btn w-100" style="margin-left: 0 !important;">
                                                    <i class="ri-calendar-schedule-line"></i>Wait for Next Edition
                                                </button>

                                            @endif
                                        </div>
                                        <div class="product__variant--list mb-15 add-to-wishlist"    data-product-id="{{ $product->id }}">
                                            <a class="variant__wishlist--icon mb-15" href="javascript:void(0)"
                                               title="Add to wishlist">
                                                <i class="ri-heart-3-line"></i> Add to Wishlist
                                            </a>
                                        </div>
                                    </div>



                            </div>
                             @include('frontend.modules.product.social_share')
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 ">
                    <div class="book-list-section  bg-white d-none d-md-block    " id="related-products-container">

                        <div class="loader" style="display: none;">


                        </div>


                    </div>
                </div>
            </div>


        </div>


    </section>
    <!-- End product details section -->

    <!-- Start product details tab section -->
    <section class="product__details--tab__section section--padding">
        <div class="container">

            @if(count($product->bundleProducts) > 0)
            <div class="row row-cols-1 mb-3">
                <div class="col">
                <h2 class="mb-4">বান্ডিল পণ্য তালিকা</h2>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">পণ্যের নাম</th>
                        <th scope="col">পরিমাণ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($product->bundleProducts as $bundleProduct)
                    <tr>
                        <th scope="row">{{$loop->iteration }}</th>

                        <td>{{ $bundleProduct->name }}</td>
                        <td>{{ $bundleProduct->quantity }}</td>

                    </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            </div>
            @endif
            <div class="row row-cols-1">
                <div class="col">

                    <ul class="product__details--tab d-flex mb-30">

                        <li class="product__details--tab__list active" data-toggle="tab" data-target="#summary">
                            সারসংক্ষেপ
                        </li>
                        <li class="product__details--tab__list" data-toggle="tab" data-target="#description"> বিবরণ</li>

                        <li class="product__details--tab__list" data-toggle="tab" data-target="#reviews"> রিভিউ</li>
                    </ul>
                    <div class="product__details--tab__inner border-radius-10" id="product-description-container">

                        <div class="tab_content">

                            <div id="summary" class="tab_pane  active show">
                                <div class="product__tab--content">
                                    <div class="product__tab--content__step mb-20">


                                        <div class="table-responsive">
                                            <table class=" summary-table table table-bordered">
                                                <tbody>
                                                <tr>
                                                    <th scope="row" class="w-25">
                                                        <span class="ri-image-fill"></span> নাম
                                                    </th>
                                                    <td>{{ $product->bangla_name }}</td>
                                                </tr>

                                                @if(isset($product->categories) && $product->categories->isNotEmpty())
                                                    <tr>
                                                        <th scope="row" class="w-25">
                                                            <span class="ri-grid-fill"></span> ক্যাটাগরি
                                                        </th>
                                                        <td>
                                                            @foreach($product->categories as $category)
                                                                <a href="{{ route('category.single',['slug' => $category->slug ?? $category->id]) }}">{{ $category->name }}</a>@if (!$loop->last), @endif
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endif

                                                @isset($product->publisher)
                                                    <tr>
                                                        <th scope="row" class="w-25">
                                                            <span class="ri-file-text-line"></span> ব্র্যান্ড
                                                        </th>
                                                        <td><a href="{{ route('publisher.single', ['slug' => $product->publisher->slug]) }}">{{ $product->publisher->name }}</a></td>
                                                    </tr>
                                                @endisset

                                                @isset($product->pages_no)
                                                    <tr>
                                                        <th scope="row" class="w-25">
                                                            <span class="ri-file-list-line"></span> পৃষ্ঠা সংখ্যা
                                                        </th>
                                                        <td>{{ $product->pages_no }}</td>
                                                    </tr>
                                                @endisset
                                                @isset($product->cover_type)
                                                    <tr>
                                                        <th scope="row" class="w-25">
                                                            <span class="ri-file-3-line"></span> কভার ধরণ
                                                        </th>
                                                        <td>{{ $product->cover_type }}</td>
                                                    </tr>
                                                @endisset

                                                </tbody>
                                            </table>
                                        </div>




                                    </div>
                                </div>
                            </div>

                            <div id="description" class="tab_pane ">
                                <div class="product__tab--content">
                                    <div class="product__tab--content__step mb-30">

                                        {!! $product->description !!}

                                    </div>

                                </div>
                            </div>



                            <div id="reviews" class="tab_pane">
                                <div class="product__reviews">
                                    <div class="product__reviews--header">
                                        <h2 class="product__reviews--header__title h3 mb-20">গ্রাহকের রিভিউ তালিকা</h2>

                                        @include('frontend.modules.product.rating')
                                        @auth
                                            <a class="actions__newreviews--btn primary__btn" href="#writereview">রিভিউ লিখুন</a>
                                        @else
                                            <button type="button"   class="actions__newreviews--btn primary__btn" onclick="window.location='{{ route('login') }}'">  রিভিউ লিখতে লগইন করুন </button>

                                        @endauth

                                    </div>

                                    @if($product->reviews->isNotEmpty())
                                        <div class="reviews__comment--area" id="reviewList">
                                            @foreach($product->reviews as $review)
                                                @include('frontend.modules.product.review_list', ['review' => $review])
                                            @endforeach
                                        </div>

                                    @else
                                        <div class="row">
                                            <div style="text-align: center;">
                                                <p> কোনো রিভিউ পাওয়া যায়নি</p>
                                            </div>

                                        </div>
                                    @endif




                                    <style>
                                        .error-message {
                                            color: red;
                                            font-size: 12px;
                                        }
                                    </style>

                                    @auth
                                        <div
                                            id="writereview" class="reviews__comment--reply__area">
                                            <form id="reviewForm"  method="POST">
                                                @csrf
                                                <h3 class="reviews__comment--reply__title mb-15"> রিভিউ এবং রেটিং যোগ করুন</h3>

                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 mb-15">
                                                        <label for="rating">
                                                            <select class="reviews__comment--reply__input" name="rating" id="rating">
                                                                <option value="" disabled selected>Select your rating</option>
                                                                <option value="1">1 Star</option>
                                                                <option value="2">2 Stars</option>
                                                                <option value="3">3 Stars</option>
                                                                <option value="4">4 Stars</option>
                                                                <option value="5">5 Stars</option>
                                                            </select>
                                                            <span class="error-message" id="ratingError"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 mb-15">
                                                        <label for="name">
                                                            <input class="reviews__comment--reply__input" placeholder="Your Name...." type="text" name="name" id="name">
                                                            <span class="error-message" id="nameError"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-lg-12 mb-10">
                                                        <label for="comment">
                                                            <textarea class="reviews__comment--reply__textarea" placeholder="Your Comments...." name="comment" id="comment"></textarea>
                                                            <span class="error-message" id="commentError"></span>
                                                        </label>
                                                        <span id="charCount" class="char-count">0 characters</span>
                                                    </div>

                                                </div>
                                                <button class="reviews__comment--btn text-white primary__btn" type="submit">SUBMIT</button>
                                            </form>
                                        </div>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End product details tab section -->

@endsection
@section('scripts')

    <style>

        @media (max-width: 768px) {
            .summary-table th,
            .summary-table td
            {
                font-size: 11px;
            }
        }

    </style>

    <script>

        $(document).ready(function() {
            // Function to handle increase button click
            $('.increase').on('click', function() {
                var $quantityInput = $(this).siblings('label').find('.quantity__number');
                var currentValue = parseInt($quantityInput.val());

                // Get the max quantity from the data attribute
                var maxQuantity = parseInt($quantityInput.data('max-quantity'));

                // Increment the value if it's within the allowed range
                if (currentValue < maxQuantity) {
                    $quantityInput.val(currentValue + 1);
                } else {
                    showToast('আপনি সর্বাধিক ' + maxQuantity + ' পরিমাণের চেয়ে বেশি অর্ডার করতে পারবেন না.','warning');
                    $quantityInput.val(maxQuantity);
                }

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', parseInt($quantityInput.val()));
            });

            // Function to handle decrease button click
            $('.decrease').on('click', function() {
                var $quantityInput = $(this).siblings('label').find('.quantity__number');
                var currentValue = parseInt($quantityInput.val());

                // Decrement the value if it's greater than 1
                if (currentValue > 1) {
                    $quantityInput.val(currentValue - 1);
                } else {
                    $quantityInput.val(1); // Minimum quantity is 1
                    showToast('আপনি ১ এর কম পরিমাণে অর্ডার করতে পারবেন না.' ,'danger');
                }

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', parseInt($quantityInput.val()));
            });

            // Handling manual input in the quantity field
            $('.quantity__number').on('input', function() {
                var currentValue = parseInt($(this).val());

                // Get the max quantity from the data attribute
                var maxQuantity = parseInt($(this).data('max-quantity'));

                // Ensure the value stays within the allowed range
                if (currentValue < 1) {
                    $(this).val(1);
                    showToast('আপনি ১ এর কম পরিমাণে অর্ডার করতে পারবেন না.','danger');
                } else if (currentValue > maxQuantity) {
                    $(this).val(maxQuantity);
                    showToast('আপনি সর্বাধিক ' + maxQuantity + ' পরিমাণের চেয়ে বেশি অর্ডার করতে পারবেন না.','danger');
                }

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', parseInt($(this).val()));
            });
        });

        $(document).ready(function() {

            var productId = {{ $product->id }}; // Assuming $product is available in your view

            // Function to fetch related products
            function fetchRelatedProducts() {
                var sectionId = 'related-products-container';
                showLoader(sectionId);
                $.ajax({
                    url: '{{ route("product.related", ":id") }}'.replace(':id', productId),
                    type: 'GET',
                    success: function(response) {
                        $('#related-products-container').html(response);
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
                        if (entry.target.id === 'related-products-container') {
                            fetchRelatedProducts();
                        }

                        observer.unobserve(entry.target); // Unobserve after fetching
                    }
                });
            };

            // Create an Intersection Observer
            var observer = new IntersectionObserver(observerCallback, observerOptions);

            // Observe the elements
            observer.observe(document.getElementById('related-products-container'));







        });
        $(document).ready(function() {
            // Function to handle increase button click
            $('.increase').on('click', function() {
                var $quantityInput = $(this).siblings('label').find('.quantity__number');
                var currentValue = parseInt($quantityInput.val());
                $quantityInput.val(currentValue + 1);

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', currentValue + 1);
            });

            // Function to handle decrease button click
            $('.decrease').on('click', function() {
                var $quantityInput = $(this).siblings('label').find('.quantity__number');
                var currentValue = parseInt($quantityInput.val());
                if (currentValue > 1) {
                    $quantityInput.val(currentValue - 1);

                    // Update data-quantity attribute on the add-to-cart button
                    $('.add-to-cart').data('quantity', currentValue - 1);
                }
            });

            // Optional: Update data-quantity attribute on the add-to-cart button whenever quantity input changes manually
            $('.quantity__number').on('change', function() {
                var currentValue = parseInt($(this).val());
                if (currentValue < 1) {
                    $(this).val(1);
                    currentValue = 1;
                }

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', currentValue);
            });


            var reviewSubmitted = false; // Variable to track review submission
            $('#reviewForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission


                // Check if review has already been submitted
                if (reviewSubmitted) {
                    alert('You have already submitted a review.');
                    return false; // Prevent further submission
                }

                // Disable submit button to prevent multiple submissions
                $('#reviewForm button[type="submit"]').prop('disabled', true);


                // Clear previous error messages
                $('.error-message').text('');

                // Validate form fields
                var isValid = true;

                var rating = $('#rating').val();
                if (!rating) {
                    $('#ratingError').text('Please select a rating');

                    isValid = false;
                }

                var name = $('#name').val();
                if (!name) {
                    $('#nameError').text('Please enter your name');
                    isValid = false;
                }

                var comment = $('#comment').val().trim(); // Trim to remove leading and trailing spaces
                var charCount = comment.length;

                if (!comment) {
                    $('#commentError').text('Please enter your comment');
                    isValid = false;
                } else if (charCount > 1000) {
                    $('#charCount').addClass('error'); // Apply error style
                    $('#commentError').text('Comment cannot exceed 1000 characters');
                    isValid = false;
                }

                if (!isValid) {
                    return false; // Prevent form submission if validation fails
                }

                if (!isValid) {
                    // Re-enable submit button if validation fails
                    $('#reviewForm button[type="submit"]').prop('disabled', false);
                    return false; // Prevent form submission if validation fails
                }

                // Serialize form data
                var formData = $(this).serialize();

                // AJAX request
                $.ajax({
                    type: 'POST',
                    url: '{{ route('submit-review', $product->id) }}',
                    data: formData,
                    success: function(response) {

                        showToast('Review submitted successfully!', 'success');

                        $('#reviewList').append(response.html);

                        $('#reviewForm')[0].reset();

                        $('#charCount').hide();

                        $('.error-message').text('');
                        reviewSubmitted = true;
                        $('#reviewForm button[type="submit"]').prop('disabled', false);
                    },
                    error: function(error) {

                        // alert('Error submitting review. Please try again.');
                        showToast('Error submitting review. Please try again.', 'danger');

                        // Re-enable submit button if there's an error
                        $('#reviewForm button[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $('#comment').on('input', function() {
                var comment = $(this).val().trim();
                var charCount = comment.length;

                $('#charCount').text(charCount + ' characters');

                if (charCount > 1000) {
                    $('#charCount').addClass('error');
                    $('#commentError').text('Comment cannot exceed 1000 characters');
                } else {
                    $('#charCount').removeClass('error');
                    $('#commentError').text('');
                }
            });

        });

    </script>


@endsection

