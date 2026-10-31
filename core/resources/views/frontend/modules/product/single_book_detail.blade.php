@extends('frontend.layouts.master')

@section('meta')
    <title>{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ get_option('title') }}</title>

    <meta property="og:title"
        content="{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ strtolower($product->meta_title ?? get_option('title')) }}">
    <meta property="og:description" content="{{ strip_tags($product->meta_description ?? get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ image($product->meta_image ?? get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('company_name') }}">

    <!-- Add more Open Graph tags as needed -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="{{ ucwords($product->bangla_name ?? 'Product Details') }} | {{ strtolower($product->meta_title ?? get_option('title')) }}">
    <meta name="twitter:description" content="{{ strip_tags($product->meta_description ?? get_option('description')) }}">
    <meta name="twitter:image" content="{{ image($product->meta_image ?? get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->

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
                            <div class="product__details--media"
                                >
                                <div class="product__media--preview">

                                    <div class="single-books">

                                        @if ( !empty($product->review_url))
                                            <div class="single-book-box" onclick="window.open('{{$product->review_url}}', '_blank')">
                                                
                                                <span class="single-book-ribbon" >
                                                    <!-- <div class="button px-3 shadow" id="button-7">-->
                                                    <!--    <div id="dub-arrow"><i class="ri-play-large-fill text-white"></i></div>-->
                                                    <!--    <a href="#">Review</a>-->
                                                    <!--</div>-->
                                                     <a class="flowrino-btn" href="javascript:void(0);">Review</a>
                                                </span>
                                            </div>
                                        @endif
                                        
                                        

                                        @if ( $product->isBundle != 1 && $product->pages->isNotEmpty())
                                            <div class="title read_now"  data-open="modalReadMore"   data-product-id="{{ $product->id }}" >
                                                <a>
                                                    <img class="blink-soft"
                                                        src="{{ asset('theme/frontend/assets/img/icon/read_more1.png') }}"
                                                        alt="img">
                                                </a>
                                            </div>
                                        @endif

                                    
                                    <div class="single-book-cover book-1  read_now" 
                                        @if ($product->isBundle != 1 && $product->pages->isNotEmpty()) 
                                            data-open="modalReadMore"      data-product-id="{{ $product->id }}" 
                                        @endif
                                        style="background: url('{{ image($product->thumb_image) }}'); background-size: cover;">
                                        
                                        <div class="effect"></div>
                                        <div class="light"></div>
                                    </div>

                                    
                                        <div class="book-inside">

                                        </div>

                                    </div>


                                </div>

                            </div>
                        </div>



                        <div class="col">


                            @include('frontend.modules.product.campaign_offer_time')

                            <div class="product__details--info py-3">

                                <h2 class="product__details--info__title mb-15">{{ $product->bangla_name }}</h2>


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
                                    
                                    
                                    
                                    @if($product->isBundle==1)
                                    
                                     @if (count($product->bundleProducts) > 0)
        <div class="combo_books_read py-3">
                                                <div class="book-list">
                                                    
                                                    
                                                    @foreach ($product->bundleProducts as $bundleProduct)
                                                    
                                                     @php
                                                        // Fetch the product using the full namespace without importing it
                                                        $productDetails = \App\Models\Product::find($bundleProduct->bundle_product_id);
                                                    @endphp
                                                     
                                                     
                                                    <div class="book-entry" data-product-id="{{ $bundleProduct->bundle_product_id }}">

                                                        <p class="book-title"><i class="ri-star-s-fill"></i> {{ $bundleProduct->name }}</p>
                                                    @if($productDetails->pages->isNotEmpty())
                                                        <button data-open="modalBunldeReadMore" class="preview-btn">পড়ে
                                                            দেখুন</button>
                                                    @endif
                                                    </div>
                                  
                                                    @endforeach
                                  
                                  
                                                </div>
                                                
                                                
                                                @endif
                                            </div>

                                          
                                            @else

                                    @if ($product->authors->isNotEmpty())
                                        @php
                                            $authors = $product->authors;
                                            $primaryAuthors = $authors->take(2);
                                            $remainingAuthors = $authors->slice(2);
                                        @endphp

                                        <p class="product__details--info__meta--list">
                                            লেখক:
                                            <span>
                                                <strong class="text-red-english-moja">
                                                    @foreach ($primaryAuthors as $author)
                                                        <a
                                                            href="{{ route('author.single', ['slug' => $author->slug]) }}">{{ $author->name }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                    @if ($remainingAuthors->isNotEmpty())
                                                        ,
                                                        <a href="#" id="moreAuthorsToggle" class="text-success"> আরও
                                                            লেখক...</a>
                                                        <span id="moreAuthors" style="display: none;">
                                                            @foreach ($remainingAuthors as $author)
                                                                <a
                                                                    href="{{ route('author.single', ['slug' => $author->slug]) }}">{{ $author->name }}</a>
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </span>
                                                    @endif
                                                </strong>
                                            </span>
                                        </p>
                                    @endif



                                    @isset($product->publisher)
                                        <p class="product__details--info__meta--list">প্রকাশনী:
                                            <a href="{{ route('publisher.single', ['slug' => $product->publisher->slug ?: $product->publisher->id]) }}"
                                                class="text-red-english-moja">{{ $product->publisher->name }}</a>
                                        </p>
                                    @endisset




                                    @if ($product->subjects->isNotEmpty())
                                        <p class="product__details--info__meta--list">বিষয়:
                                            <span>{{ $product->subjects->pluck('name')->implode(', ') }}</span>
                                        </p>
                                    @endif

                                    @isset($product->edition)
                                        <p class="product__details--info__meta--list">সংস্করণ:
                                            <span><strong class="text-red-english-moja">{{ $product->edition }}</strong></span>
                                        </p>
                                    @endisset


                                    <p class="product__details--info__meta--list">
                                        ক্যাটাগরি:
                                        @foreach ($product->categories as $category)
                                            <a href="{{ route('category.single', ['slug' => $category->slug ?: $category->id]) }}"
                                                class="category-link">
                                                {{ $category->name }}
                                            </a>{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </p>


                                    <p class="product__details--info__meta--list "><i
                                                class="ri-book-3-line text-red-english-moja"></i>  পৃষ্ঠা: {{ $product->pages_no }}, কভার: {{ $product->cover_type }}</p>
                                    
                                    
                                    @endif
                                    
                                    <hr>
                                    <div class="guarantee__safe--checkout mb-3">
                                       
                                        <h5 class="guarantee__safe--checkout__title text-green-english-moja">
                                            <i class="ri-bookmark-3-fill"></i> সমগ্র বাংলাদেশ
                                            <strong class="text-red-english-moja">
                                                ক্যাশ অন ডেলিভারি মাত্র
                                                {{ convertToBengaliNumber(get_option('shipping_charge')) }}/- টাকা
                                            </strong>
                                            (বই হাতে পাওয়ার পর মূল্য পরিশোধের সুযোগ)
                                        </h5>


                                        <h5 class="guarantee__safe--checkout__title text-green-english-moja"><i
                                                class="ri-bookmark-3-fill"></i> অনলাইনে <strong
                                                class="text-red-english-moja">bkash-এ</strong> অর্ডার করলে কুরিয়ার খরচ একদম ফ্রি (বই পৌঁছে যাবে নিকটস্থ সুন্দরবন কুরিয়ারে)</h5>
                                    </div>
                                </div>



                                <div class="product__variant">
                                    <div class="product__variant--list quantity d-lg-flex align-items-center mb-10">
                                        @if ($product->stock_status == 'in_stock')
                                            <div class="quantity__box">
                                                <button type="button"
                                                    class="quantity__value quickview__value--quantity decrease "
                                                    aria-label="quantity value" value="Decrease Value">-
                                                </button>
                                                <label>
                                                    <input type="number" class="quantity__number quickview__value--number"
                                                        value="1" data-max-quantity="{{ get_option('max_order') }}" />
                                                </label>
                                                <button type="button"
                                                    class="quantity__value quickview__value--quantity increase"
                                                    aria-label="quantity value" value="Increase Value">+
                                                </button>
                                            </div>


                                            <button class="quickview__cart--btn success__btn add-to-cart"
                                                data-product-id="{{ $product->id }}" data-quantity="1"><i
                                                    class="ri-shopping-cart-line"></i> কার্টে নিন 
                                            </button>


                                            <button class="quickview__cart--btn primary__btn add-to-cart buy-now"
                                                data-product-id="{{ $product->id }}" data-quantity="1">
                                                <i class="ri-flashlight-fill"></i> এখনই কিনুন
                                            </button>
                                        @elseif($product->stock_status == 'out_of_stock')
                                            <button class="quickview__cart--btn primary__btn w-100"
                                                style="margin-left: 0 !important;">
                                                <i class="ri-close-circle-line"></i> স্টক আউট
                                            </button>
                                        @elseif($product->stock_status == 'upcoming')
                                            <button class="quickview__cart--btn success__btn w-100"
                                                style="margin-left: 0 !important;">
                                                <i class="ri-calendar-schedule-line"></i>আপকামিং
                                            </button>
                                            
                                            @else
                                              <button class="quickview__cart--btn success__btn w-100"
                                                style="margin-left: 0 !important;">
                                                <i class="ri-calendar-schedule-line"></i>wait for next edition
                                            </button>
                                            
                                        @endif


                                    </div>
                                    <div class="product__variant--list mb-15 add-to-wishlist {{ isProductInWishlist($product->id) ? 'active' : '' }}"
                                        data-product-id="{{ $product->id }}">
                                        <a class="variant__wishlist--icon mb-15" href="javascript:void(0)"
                                            title="Add to wishlist">
                                            <i class="ri-heart-3-line"></i> Add to Wishlist
                                        </a>
                                    </div>

                                </div>
                                
                                <style>
                                    .add-to-wishlist.active a > i {
                                        color: red; /* Set the background color to red */
                                    }
                                </style>




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
            @if (count($product->bundleProducts) > 0)
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
                                @foreach ($product->bundleProducts as $bundleProduct)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
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

                        @if ($product->authors->isNotEmpty())
                            <li class="product__details--tab__list" data-toggle="tab" data-target="#author">লেখক</li>
                        @endif
                        <li class="product__details--tab__list" data-toggle="tab" data-target="#reviews"> রিভিউ</li>
                    </ul>
                    <div class="product__details--tab__inner border-radius-10" id="product-description-container">

                        <div class="tab_content">

                            <div id="summary" class="tab_pane  active show">
                                <div class="product__tab--content">
                                    <div class="product__tab--content__step mb-20">
                                        <style>
                                            /* Media query for smaller screens */
                                            @media (max-width: 768px) {

                                                .summary-table th,
                                                .summary-table td {
                                                    font-size: 1.4rem;
                                                }
                                                
                                                  .summary-table  tr th {
                                                    width: 12rem;
                                                }
                                                .product__details--info__meta--list {
                                                    font-size: 1.4rem;
                                                }
                                                .product__details--info__title{
                                                     font-size: 1.8rem;
                                                     text-align: center;
                                                }
                                                .product__details--info__price{
                                                     
                                                     text-align: center;

                                                }
                                                .product__details--info__price .current__price {
                                                     font-size: 2.2rem;
                                                     text-align: center;

                                                }
                                            }
                                        </style>

                                        <div class="table-responsive">
                                            <table class=" summary-table table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" >
                                                            <span class="ri-image-fill"></span> বইয়ের নাম
                                                        </th>
                                                        <td>{{ $product->bangla_name }}</td>
                                                    </tr>

                                                    @if (isset($product->categories) && $product->categories->isNotEmpty())
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-grid-fill"></span> ক্যাটাগরি
                                                            </th>
                                                            <td>
                                                                @foreach ($product->categories as $category)
                                                                    <a
                                                                        href="{{ route('category.single', ['slug' => $category->slug ?? $category->id]) }}">{{ $category->name }}</a>
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if (isset($product->authors) && $product->authors->isNotEmpty())
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-pen-nib-fill"></span> লেখক
                                                            </th>
                                                            <td>
                                                                @foreach ($product->authors as $author)
                                                                    <a
                                                                        href="{{ route('author.single', ['slug' => $author->slug ?? $author->id]) }}">{{ $author->name }}</a>
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @isset($product->publisher)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-file-text-line"></span> প্রকাশনা
                                                            </th>
                                                            <td><a
                                                                    href="{{ route('publisher.single', ['slug' => $product->publisher->slug]) }}">{{ $product->publisher->name }}</a>
                                                            </td>
                                                        </tr>
                                                    @endisset

                                                    @if (isset($product->subjects) && $product->subjects->isNotEmpty())
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-book-3-line"></span> সংশ্লিষ্ট বিষয়
                                                            </th>
                                                            <td>
                                                                @foreach ($product->subjects as $subject)
                                                                    {{--                                                                <a href="{{ route('subject.single', ['slug' => $subject->slug]) }}">{{ $subject->name }}</a>@if (!$loop->last), @endif --}}
                                                                    <a href="javascript:void(0)">{{ $subject->name }}</a>
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @isset($product->pages_no)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-file-list-line"></span> পৃষ্ঠা সংখ্যা
                                                            </th>
                                                            <td>{{ $product->pages_no }}</td>
                                                        </tr>
                                                    @endisset
                                                    @isset($product->published_year)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-file-copy-line"></span> প্রকাশের সাল
                                                            </th>
                                                            <td>{{ date("F, Y",strtotime($product->published_year)) }}</td>
                                                        </tr>
                                                    @endisset
                                                    @isset($product->edition)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-file-copy-line"></span> সংস্করণ
                                                            </th>
                                                            <td>{{ $product->edition }}</td>
                                                        </tr>
                                                    @endisset



                                                    @isset($product->cover_type)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-file-3-line"></span> কভার ধরণ
                                                            </th>
                                                            <td>{{ $product->cover_type }}</td>
                                                        </tr>
                                                    @endisset

                                                    {{-- New row for Product Image --}}
                                                    @isset($product->isbn)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-barcode-box-line"></span> আইএসবিএন
                                                            </th>
                                                            <td>{{ $product->isbn }}</td>
                                                        </tr>
                                                    @endisset

                                                    @isset($product->language)
                                                        <tr>
                                                            <th scope="row" >
                                                                <span class="ri-global-line"></span> ভাষা
                                                            </th>
                                                            <td>{{ $product->language }}</td>
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

                            <div id="author" class="tab_pane">
                                <div class="product__tab--conten">
                                    <div class="product__tab--content__step mb-10">

                                        <div class="row">
                                            @foreach ($product->authors as $author)
                                                <div class=" col-lg-6 col-md-6 mb-3">
                                                    <a href="{{ route('author.single', ['slug' => $author->slug ?? $author->id]) }}"
                                                        class="text-decoration-none text-dark">
                                                        <div class="d-flex  align-items-center">
                                                            @if ($author->icon)
                                                                <img class="single-author_img" src="{{ asset($author->icon) }}" class="mb-2"
                                                                    width="60" height="60"
                                                                    alt="{{ $author->name }}">
                                                            @else
                                                                <img class="single-author_img" src="{{ asset('theme/frontend/assets/img/icon/author.jpg') }}"
                                                                    class="mb-2" width="60" height="60"
                                                                    alt="{{ $author->name }}">
                                                            @endif
                                                            <h4 class="single_page_autor">{{ $author->name }}</h4>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div id="reviews" class="tab_pane">
                                <div class="product__reviews">
                                    <div class="product__reviews--header">
                                        <h2 class="product__reviews--header__title h3 mb-20">গ্রাহকের রিভিউ তালিকা</h2>

                                        @include('frontend.modules.product.rating')
                                        @auth
                                            <a class="actions__newreviews--btn primary__btn" href="#writereview">রিভিউ
                                                লিখুন</a>
                                        @else
                                            <button type="button" class="actions__newreviews--btn primary__btn"
                                                onclick="window.location='{{ route('login') }}'"> রিভিউ লিখতে লগইন করুন
                                            </button>

                                        @endauth

                                    </div>

                                    @if ($product->reviews->isNotEmpty())
                                        <div class="reviews__comment--area" id="reviewList">
                                            @foreach ($product->reviews as $review)
                                                @include('frontend.modules.product.review_list', [
                                                    'review' => $review,
                                                ])
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="row">
                                            <div style="text-align: center;">
                                                <p> কোনো রিভিউ পাওয়া যায়নি</p>
                                            </div>

                                        </div>
                                    @endif


                                    @auth
                                        <div id="writereview" class="reviews__comment--reply__area">
                                            <form id="reviewForm" method="POST">
                                                @csrf
                                                <h3 class="reviews__comment--reply__title mb-15"> রিভিউ এবং রেটিং যোগ করুন</h3>

                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 mb-15">
                                                        <label for="rating">
                                                            <select class="reviews__comment--reply__input" name="rating"
                                                                id="rating">
                                                                <option value="" disabled selected>Select your rating
                                                                </option>
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
                                                            <input class="reviews__comment--reply__input"
                                                                placeholder="Your Name...." type="text" name="name"
                                                                id="name">
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
                                                <button class="reviews__comment--btn text-white primary__btn"
                                                    type="submit">জমা দিন</button>
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



    <!-- Start product  aurhor books section -->

    <section class="product__section product__section--style3 section--padding" id="writer" >
        <div class="container-fluid product3__section--container">
            <div class="section__heading text-center mb-50">
                <h2 class="section__heading--maintitle">এই লেখকের অন্যান্য বই</h2>
            </div>
            <div class="product__section--inner product__swiper--column4__activation swiper " id="related-author-books">
                <div class="loader" style="display: none;">


                </div>
            </div>
        </div>
    </section>
    <!-- End product section -->





    <!-- Auth Modal -->
    <div class="modal" id="authModal" data-animation="slideInUp">
        <div class="modal-dialog bg-light quickview__main--wrapper">

            <header class="modal-header quickview__header">
                <button class="close-modal quickview__close--btn" aria-label="close modal" data-close>✕</button>
            </header>
            <div class="quickview__inner bg-light">
                <div class="modal-body">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="authTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                                type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                                type="button" role="tab" aria-controls="register"
                                aria-selected="false">Register</button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="authTabContent">
                        <!-- Login Tab Content -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel"
                            aria-labelledby="login-tab">

                            @include('frontend.modules.auth.login_form')
                        </div>

                        <!-- Register Tab Content -->
                        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                            {{--                            @include('frontend.modules.auth.register') --}}

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

<!-- Quickview Wrapper -->
<div class="modal" id="modalBunldeReadMore" data-animation="slideInUp">
    <div class="modal-dialog bg-light quicksearch__main--wrapper">
        <header class="modal-header quickview__header">
            <button class="close-modal quickview__close--btn" aria-label="close modal" data-close>✕</button>
        </header>

        <div class="quickview__inner readmorescroll bg-light">
            <div class="row row-cols-lg-2 row-cols-md-2">
                <div class="col-md-12">
                    <div class="modal-body readmoreImg" id="readMoreBundleImages">
                        <!-- Loader -->
                        <div class="loader" style="display: none;">
              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Quickview Wrapper -->
    <div class="modal" id="modalReadMore" data-animation="slideInUp">
        <div class="modal-dialog bg-light quicksearch__main--wrapper">
            <header class="modal-header quickview__header">
                <button class="close-modal quickview__close--btn" aria-label="close modal" data-close>✕</button>
            </header>

            <div class="quickview__inner readmorescroll bg-light">
                <div class="row row-cols-lg-2 row-cols-md-2">
                    <div class="col-md-12">
                        <div class="modal-body readmoreImg" id="readMoreImages">
                            
                             <!-- Loader -->
                        <div class="loader" style="display: none;">
                    
                        </div>

        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quickview Wrapper End -->

@endsection
@section('scripts')
    <style>

.modal-backdrop {
        z-index: 10 !important;
}
        .error-message {
            color: red;
            font-size: 12px;
        }
 
              /*review button   
              */
            .flowrino-btn {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 90px;
                height: 30px;
                border-radius: 5px 10px;
                font-weight: 600;
                font-size: 12pt;
                background: linear-gradient(to right,
                        CornflowerBlue,
                        Aqua,
                        DeepPink,
                        CornflowerBlue);
                background-size: 600% 600%;
                animation: gradient 20s linear infinite;
               
                border: 1px solid transparent;
                
                color: white;
                box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset,
                    rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset,
                    rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px,
                    rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px,
                    rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
            }

            .flowrino-btn:hover {
                 background: linear-gradient(to right, Tomato, DarkOrange, Crimson, Tomato);
                background-size: 600% 600%;
                animation: gradient 30s linear infinite;
                filter: drop-shadow(0px 0px 30px CornflowerBlue);
                font-weight: 400;
                text-shadow: 0px 0px 3px CornflowerBlue;
                color: white;
                
            }

            @keyframes gradient {
                0% {
                    background-position: 0% 50%;
                }

                100% {
                    background-position: 600% 50%;
                }
            }
       
                                      
                                            #button-7 {
                                                position: relative;
                                                overflow: hidden;
                                                cursor: pointer;
                                                border: 2px solid #da0000;
                                                
                                            }

                                            #button-7 a {
                                                position: relative;
                                                left: 0;
                                                transition: all .35s ease-Out;
                                                color: #da0000;
                                            }

                                            #dub-arrow {
                                                width: 100%;
                                                height: 100%;
                                                background: #da0000;
                                                left: -200px;
                                                position: absolute;
                                                padding: 0;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                transition: all .35s ease-Out;
                                                bottom: 0;
                                            }

                                            #button-7:hover #dub-arrow {
                                                left: 0;
                                            }

                                            #button-7:hover a {
                                                left: 150px;
                                            }
                                            
                                            .single_page_autor{
                                                margin-left: 10px;
                                            }
                                            
                                            .single-author_img{
                                                border-radius: 50px;
                                            }
                                            
                                            
                                            
                                            
                                            
                                             
                                                .book-list {
                                                    display: flex;
                                                    flex-direction: column;
                                                    gap: 12px;
                                                }

                                                .book-entry {
                                                    display: flex;
                                                    justify-content: space-between;
                                                    align-items: center;
                                                    padding: 5px;

                                                    border-radius: 8px;
                                                    background-color: #f9f9f9;
                                                    transition: box-shadow 0.5s ease;
                                                    box-shadow: 0 2px 5px #0099ff54;
                                                }

                                                /* .book-entry:hover {
                                                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                                                } */

                                                .book-title {
                                                    margin: 0;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #333;
                                                    font-family: 'Poppins', sans-serif;
                                                }

                                                .preview-btn {
                                                    background-color: #00bcd4;
                                                    color: white;
                                                    border: none;
                                                    padding: 5px 10px;
                                                    border-radius: 5px;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    cursor: pointer;
                                                    transition: background-color 0.5s ease, transform 0.3s ease;
                                                    font-family: 'Poppins', sans-serif;
                                                }

                                                .preview-btn:hover {
                                                    background-color: #da0000;
                                                    transform: scale(1.05);
                                                }

                                                .preview-btn:focus {
                                                    outline: none;
                                                    box-shadow: 0 0 5px rgba(0, 188, 212, 0.7);
                                                }

                                                @media(max-width:480px) {
                                                    .book-title {
                                                    font-size: 10px;
                                                    }
                                                    .preview-btn { 
                                                    font-size: 10px;
                                                    padding: 3px 5px;
                                                    }
                                                }
                                            
                                            
                        </style>
    <script>
    
//     $(document).on('click', '.single-book-cover[data-open="modalReadMore"]', function () {
//     var productId = $(this).data('product-id'); // Get the product ID from data attribute
//     var url = '{{ route("product.images", ":id") }}'; // Base URL for fetching images
//     url = url.replace(':id', productId); // Replace the placeholder with the actual product ID

//     $.ajax({
//         url: url,
//         method: 'GET',
//         success: function (response) {
//             // Clear any existing images in the modal
//             $('#readMoreImages').empty();

//             // Iterate through the response and append images to the modal
//             $.each(response, function (index, image) {
//                 var imgHtml = '<img class="img-fluid" src="' + image.url + '" alt="' + image.alt_text + '">';
//                 $('#readMoreImages').append(imgHtml);
//             });

//             // Show the modal
//             // $('#modalReadMore').modal('show');
//         },
//         error: function (xhr) {
//             console.error('Error fetching images:', xhr);
//             // You can show an error message to the user here if needed
//         }
//     });
// });


$(document).on('click', '.read_now[data-open="modalReadMore"]', function () {
    var productId = $(this).data('product-id'); // Get the product ID from the data attribute
    var url = '{{ route("product.images", ":id") }}'; // Base URL for fetching images
    url = url.replace(':id', productId); // Replace the placeholder with the actual product ID
    var sectionId = 'readMoreImages'; // Section ID for the modal images

    $.ajax({
        url: url,
        method: 'GET',
        beforeSend: function() {
            // Show the loader and clear any existing images
            $('#' + sectionId).find('.loader').show(); // Show the loader
            $('#' + sectionId).find('img').remove(); // Clear previous images
        },
        // success: function (response) {
        //     // Append the images to the modal
        //     $.each(response, function (index, image) {
        //         var imgHtml = '<img class="img-fluid" src="' + image.url + '" alt="' + image.alt_text + '">';
        //         $('#' + sectionId).append(imgHtml);
        //     });
        // },
        
        
         success: function (response) {
            // Append the images
            $.each(response, function (index, image) {
                var imgHtml = '<img class="img-fluid lazy-load" data-src="' + image.url + '" src="{{ asset('theme/frontend/assets/img/default/book.png') }}" alt="' + image.alt_text + '">';
                $('#' + sectionId).append(imgHtml);
            });

            // After appending, observe the lazy load images
            observeLazyLoadImages(); // Call the function to observe the images
        },
        error: function (xhr) {
            console.error('Error fetching images:', xhr);
            // Optionally display an error message
            $('#' + sectionId).append('<p>Error loading images. Please try again later.</p>');
        },
        complete: function() {
            // Hide the loader after images are loaded
            $('#' + sectionId).find('.loader').hide();
        }
    });
});


    
$(document).on('click', '.preview-btn', function () {
    var productId = $(this).closest('.book-entry').data('product-id'); // Get the product ID
    var url = '{{ route("product.images", ":id") }}'; // Generate the base URL
    url = url.replace(':id', productId); // Replace the placeholder with the actual product ID
    var sectionId = 'readMoreBundleImages'; // Corrected section ID

    $.ajax({
        url: url,
        method: 'GET',
        beforeSend: function() {
            // Show the loader and clear existing images
            $('#' + sectionId + ' .loader').show(); // Show the loader
            $('#' + sectionId).find('img').remove(); // Clear any existing images
        },
        
             success: function (response) {
            // Append the images
            $.each(response, function (index, image) {
                var imgHtml = '<img class="img-fluid lazy-load" data-src="' + image.url + '" src="{{ asset('theme/frontend/assets/img/default/book.png') }}" alt="' + image.alt_text + '">';
                $('#' + sectionId).append(imgHtml);
            });

            // After appending, observe the lazy load images
            observeLazyLoadImages(); // Call the function to observe the images
        },
    
        error: function (xhr) {
            console.error('Error fetching images:', xhr);
        },
        complete: function() {
            // Hide the loader after images are loaded
            $('#' + sectionId + ' .loader').hide();
        }
    });
});

    
    
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
                    showToast('আপনি সর্বাধিক ' + maxQuantity +
                        ' পরিমাণের চেয়ে বেশি অর্ডার করতে পারবেন না.', 'warning');
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
                    showToast('আপনি ১ এর কম পরিমাণে অর্ডার করতে পারবেন না.', 'danger');
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
                    showToast('আপনি ১ এর কম পরিমাণে অর্ডার করতে পারবেন না.', 'danger');
                } else if (currentValue > maxQuantity) {
                    $(this).val(maxQuantity);
                    showToast('আপনি সর্বাধিক ' + maxQuantity +
                        ' পরিমাণের চেয়ে বেশি অর্ডার করতে পারবেন না.', 'danger');
                }

                // Update data-quantity attribute on the add-to-cart button
                $('.add-to-cart').data('quantity', parseInt($(this).val()));
            });
        });




        $(document).ready(function() {
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



        function initializeAuthorProductSlider() {
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







        $(document).ready(function() {




            var productId = {{ $product->id }}; // Assuming $product is available in your view

            // Function to fetch related products
            function fetchRelatedProducts() {
                var sectionId = 'related-products-container';
                showLoader(sectionId);
                $.ajax({
                    url: '{{ route('product.related', ':id') }}'.replace(':id', productId),
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



            function fetchProductAuthorBooks() {
                var sectionId = 'related-author-books';
                showLoader(sectionId);
                $.ajax({
                    url: '{{ route('product.author.books', ':id') }}'.replace(':id', productId),
                    type: 'GET',
                    success: function(response) {
                        $('#related-author-books').html(response);
                        
                         if(response.includes("No product available")){
                            $("#writer").hide();
                        }
                        

                        initializeAuthorProductSlider()

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
                        if (entry.target.id === 'related-products-container') {
                            fetchRelatedProducts();
                        } else if (entry.target.id === 'related-author-books') {
                            fetchProductAuthorBooks();
                        }
                        observer.unobserve(entry.target); // Unobserve after fetching
                    }
                });
            };

            // Create an Intersection Observer
            var observer = new IntersectionObserver(observerCallback, observerOptions);

            // Observe the elements
            observer.observe(document.getElementById('related-products-container'));
            // observer.observe(document.getElementById('product-description-container'));
            observer.observe(document.getElementById('related-author-books'));
        });
    </script>
@endsection
