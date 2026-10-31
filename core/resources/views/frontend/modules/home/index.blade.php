@extends('frontend.layouts.master')
@section('meta')
    <title>Home  | {{ get_option('title') }}</title>

    <meta property="og:title" content="{{get_option('title')}}">
    <meta property="og:description" content="{{ strip_tags( get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset(get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('company_name') }}">

    <!-- Add more Open Graph tags as needed -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{get_option('title')}}">
    <meta name="twitter:description" content="{{ strip_tags( get_option('description')) }}">
    <meta name="twitter:image" content="{{ asset(get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->
@endsection

@section('content')

    <main class="main__content_wrapper mt-4">

        <!-- banner section -->
        <section>
            <div class="container-fluid fetch_section" id="slider-banner-section">
                <div class="loader" style="display: none;"></div>
            </div>
        </section>
        <!-- End banner section -->

        <!-- category section -->
        <section id="category-section" class=" fetch_section category--section section--padding pt-0">


            <div class="loader" style="display: none;">


            </div>
        </section>

        <!-- End category section -->

        <!-- Start product section -->
        <section class="product__section section--padding pt-0 pb-0">
            <div class="container-fluid">
                <div class="section__heading text-center mb-35">
                    <h2 class="section__heading--maintitle">প্রোডাক্টস</h2>
                </div>
                <ul class="product__tab--one product__tab--primary__btn d-flex justify-content-center mb-50">
                    @foreach($tabSections as $tabSection)
                        <li class="product__tab--primary__btn__list  section-tab {{ $loop->first ? 'active' : '' }}"
                            data-id="{{ $tabSection->id }}">
                            {{ $tabSection->name }}
                        </li>
                    @endforeach
                </ul>
                <div class="tab_content">
                    @foreach($tabSections as $tabSection)
                        <div id="tab-{{ $tabSection->id }}" class="tab_pane {{ $loop->first ? 'active show' : '' }}">



                            <div class="loader" style="display: none;">

                            </div>



                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- End product section -->


        <!-- Start stationary section -->

        @if(get_option('show_stationary')==1)
        <section class="stationary--section section--padding">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-5">
                        <div class="product__collection--content">
                            <h2 class="product__collection--content__title">ষ্টেশনারী <br>
                                কালেকশন</h2>
                            <p class="product__collection--content__desc">আপনার পছন্দের সব ষ্টেশনারী পণ্য এখানেই!
                                খাতা, কলম, পেন্সিল, মার্কার, রাবার, এসার, স্কেল, এআর ব্যাগ, ফাইল এবং আরও অনেক কিছু!</p>
                            <a class="product__collection--content__btn primary__btn btn__style3" href="{{route('stationary.index')}}">
                                আরও খুঁজুন</a>
                        </div>
                    </div>


                    <div class="col-lg-8 col-md-7  " >
                        <div class="new__product--sidebar   position__relative fetch_section" id="stationary-section">
                            <div class="loader" style="display: none;">


                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End stationary section -->
        @endif


        <!-- Start best product section -->
        <section class="product__section section--padding pt-0 pb-0 my-4">
            <div class="container-fluid">
                <div class="section__heading text-center mb-50">
                    <h2 class="section__heading--maintitle">বেস্ট সেলার</h2>
                </div>

                <div class="product__section--inner product__swiper--activation swiper fetch_section"  id="best-seller-section">
                    <div class="loader" style="display: none;">


                    </div>
                </div>


            </div>
        </section>
        <!-- End product section -->

        <hr>
        <!-- Start  xtra small banner   e section -->
        <section class="banner__section px-4 py-4">
            <div class="container-fluid">
                <div class="row row-cols-md-4 row-cols-1 mb--n28">

                    @foreach($banners as $banner)
                        <div class="col-lg-3 col-sm-6 col-6 mb-28">
                            <div class="banner__items position__relative">
                                <a class="banner__items--thumbnail " href="{{$banner->link}}"><img
                                        class="banner__items--thumbnail__img banner__img--max__height lazy-load "

                                        data-loaded="false"   data-src="{{asset($banner->image)}}" src="{{ asset('theme/frontend/assets/img/default/banner_sm.png') }}" alt="banner-img"  >

                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
        <!-- End banner section -->
        <hr>



        <section class="mobile-app-section mt-5 mb-28">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 custom-response">
                        <div class="mobile-app-title">
                            <h6 class="text-white">আমাদের মোবাইল অ্যাপ</h6>
                            <h1 class="text-white">ডাউনলোড করুন</h1>
                            <a class="app-img" href="https://play.google.com/store/apps/details?id=com.nihazmi.englishmoja&hl=en&gl=US"><img
                                    src="{{image('theme/frontend/assets/img/banner/g-play.png')}}" alt=""></a>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mobile-app-right-image appoint-fift-img">
                            <img data-src="{{image('theme/frontend/assets/img/banner/app2.png')}}" src="{{image('theme/frontend/assets/img/default/book.png') }}"  alt="mobile-app img"
                                 class="img-fluid lazy-load" >
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Start testimonial section -->
        <section class="testimonial__section section--padding pt-0 pb-0">
            <div class="container-fluid">
                <div class="section__heading text-center mb-40">
                    <h2 class="section__heading--maintitle">গ্রাহকদের মন্তব্য</h2>
                </div>
                <div class="testimonial__section--inner testimonial__swiper--activation swiper  fetch_section" id="review-section">




                </div>
            </div>
        </section>
        <!-- End testimonial section -->

        <!-- Start shipping section -->
        <section class="shipping__section2 shipping__style3 mb-30">

            <div class="container-fluid">
                <div class="shipping__section2--inner shipping__style3--inner row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <a href="javascript:void(0)">
                            <div class="shipping__items2 d-flex align-items-center">
                                <div class="shipping__items2--icon ">
                                    <img src="{{asset('theme/frontend/assets/img/icon/delivery-truck.png')}}" alt="">
                                </div>
                                <div class="shipping__items2--content">
                                    <h2 class="shipping__items2--content__title h3">শিপিং</h2>
                                    <p class="shipping__items2--content__desc">দ্রুত এবং নির্ভরযোগ্য ডেলিভারি।</p>

                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <a href="javascript:void(0)">
                            <div class="shipping__items2 d-flex align-items-center">
                                <div class="shipping__items2--icon">
                                    <img src="{{asset('theme/frontend/assets/img/icon/secure-payment.png')}}" alt="">
                                </div>
                                <div class="shipping__items2--content">
                                    <h2 class="shipping__items2--content__title h3">পেমেন্ট</h2>
                                    <p class="shipping__items2--content__desc">নির্ভরযোগ্য ও নিরাপদ পেমেন্ট ব্যবস্থা।</p>

                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <a href="javascript:void(0)">
                            <div class="shipping__items2 d-flex align-items-center">
                                <div class="shipping__items2--icon">
                                    <img src="{{asset('theme/frontend/assets/img/icon/return.png')}}" alt="">
                                </div>
                                <div class="shipping__items2--content">
                                    <h2 class="shipping__items2--content__title h3">রিটার্ন</h2>
                                    <p class="shipping__items2--content__desc">সহজ এবং সুবিধাজনক রিটার্ন প্রক্রিয়া।</p>

                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <a href="javascript:void(0)">
                            <div class="shipping__items2 d-flex align-items-center">
                                <div class="shipping__items2--icon">
                                    <img src="{{asset('theme/frontend/assets/img/icon/availability.png')}}" alt="">
                                </div>
                                <div class="shipping__items2--content">
                                    <h2 class="shipping__items2--content__title h3">সাপোর্ট</h2>
                                    <p class="shipping__items2--content__desc">আপনার যে কোনো প্রশ্নের জন্য সাহায্য।</p>

                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </section>
        <!-- End shipping section -->
        
        


        @if($featuredCampaign)

                <div class="floating-offer">
                    <div id="campaign-div" class="campaign-div">
                        <div class="position-relative">
                            <a href="{{ route('campaign.single', ['slug' => $featuredCampaign->slug ?? $featuredCampaign->id]) }}">
                                <img src="{{asset('theme/frontend/assets/img/offer/offer-button.png')}}" alt=" campaign-img">
                            </a>
                            <!--{{ asset($featuredCampaign->icon) }}-->
                            <div class="offer-timer">
                                <p class="text-center text-success fw-bold py-0 mb-0">অফারটি শেষ হবে</p>
                                <div class="deals__countdown--style3 d-flex" data-countdown="{{ $featuredCampaign->end_date }}"></div>
                            </div>
                            <button id="close-btn" class="close-btn">X</button>
                        </div>
                    </div>
                    <a id="toggle-btn" class="toggle-btn" href="{{ route('campaign.single', ['slug' => $featuredCampaign->slug ?? $featuredCampaign->id]) }}">
                        <img src="{{ asset('theme/frontend/assets/img/offer/offer-button.png') }}" alt="offer-img">
                    </a>
                </div>

    @endif


    <!-- End floating offer -->
     
        <div class="newsletter__popup" data-animation="slideInUp">
        <div id="boxes" class="newsletter__popup--inner">
            <button class="newsletter__popup--close__btn" aria-label="close button">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 512 512">
                    <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="32" d="M368 368L144 144M368 144L144 368"></path>
                </svg>
            </button>
            <div class="box newsletter__popup--box d-flex align-items-center">
                <div class="newsletter__popup--thumbnail">
                    <img class="newsletter__popup--thumbnail__img display-block" onclick="window.location.href='{{ $popUpBanner->url }}'"
                         src="{{asset($popUpBanner->image)}}"
                         alt="newsletter-popup-thumb">
                </div>
            </div>
            <div class="newsletter__dont-show">
                <input type="checkbox" id="newsletter__dont--show" />
                <label for="newsletter__dont--show">Don't show this again</label>
            </div>
        </div>
    </div>

    
    
    
    

    </main>
@endsection
@section('scripts')

<style>
        .newsletter__dont-show {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px; /* Adjust as needed */
        }

        .newsletter__dont-show input[type="checkbox"] {
            margin-right: 8px; /* Space between checkbox and label */
        }


    </style>

    <script>

        function initializeReviewSlider() {
            var swiper = new Swiper(".testimonial__swiper--activation", {
                slidesPerView: 3,
                loop: true,
                clickable: false,
                spaceBetween: 30,
                breakpoints: {
                    1200: {
                        slidesPerView: 3,
                    },
                    768: {
                        spaceBetween: 30,
                        slidesPerView: 2,
                    },
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    0: {
                        slidesPerView: 1,
                    },
                },
            });
        }


        $(document).ready(function() {

            // Function to load product  for a  tab
            function loadTabContent(tabId) {
                // Show loader while content is fetching
                var loaderId = 'tab-' + tabId;
                showLoader(loaderId);

                $.ajax({
                    url: '{{ route('section.products') }}',
                    type: 'GET',
                    data: { id: tabId },
                    success: function(response) {
                        // Load the fetched content into the tab pane
                        $('#tab-' + tabId).html(response.content);

                        observeLazyLoadImages();

                        // Hide loader after content is loaded
                        hideLoader(loaderId);


                    },
                    error: function() {
                        $('#tab-' + tabId).html('<p>Failed to load content. Please try again.</p>');

                        // Hide loader in case of error
                        hideLoader(loaderId);
                    }
                });
            }

            // Get the ID of the first tab section dynamically
            var firstTabId = $('.section-tab').first().data('id');
            // Initial load for the default active tab
            loadTabContent(firstTabId);

            $('.section-tab').click(function() {

                var tabId = $(this).data('id');

                // Remove active class from all tabs and add to the clicked tab
                $('.section-tab').removeClass('active');

                $(this).addClass('active');

                // Hide all tab panes and show the clicked one
                $('.tab_pane').removeClass('active show');
                $('#tab-' + tabId).addClass('active show');

                // Load content for the selected tab
                loadTabContent(tabId);
            });
            // tab section end






            // Create an IntersectionObserver
            let observer = new IntersectionObserver((entries, observer) => {
                // lazyLoadImage(entries, observer);
                fetchContentOnIntersection(entries, observer);
            }, {
                root: null, // viewport
                rootMargin: '0px', // no additional margin
                threshold: 0.2// trigger when 20% of the image is visible
            });


            // Queue to track the sections awaiting AJAX requests
            var requestQueue = [];
// Flag to track if an AJAX request is in progress
            var loading = false;

// Function to handle fetching content when section is in viewport
            function fetchContentOnIntersection(entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        var sectionId = entry.target.getAttribute('id');

                        // Check if the section has already been loaded and if it's not in the request queue
                        if (!$(entry.target).data('loaded') && !requestQueue.includes(sectionId)) {
                            requestQueue.push(sectionId);

                            if (!loading) {
                                processRequestQueue();
                            }
                        }

                        // Stop observing this section once it's loaded
                        observer.unobserve(entry.target);
                    }
                });
            }

// Function to observe all fetch_sections
            function observeFetchSections() {
                $('.fetch_section').each(function() {
                    observer.observe(this);
                });
            }



// Call observeFetchSections initially
            observeFetchSections();

// Function to process the request queue
            function processRequestQueue() {
                if (requestQueue.length > 0) {
                    var sectionId = requestQueue.shift(); // Dequeue the next section
                    loading = true; // Set loading flag

                    // Show loader for the section
                    showLoader(sectionId);

                    // Simulated AJAX request, replace with your actual logic
                    $.ajax({
                        url: "{{ route('getSectionContent') }}",
                        type: 'GET',
                        data: { section: sectionId },
                        success: function(response) {
                            console.log("Response for section " + sectionId + ":", response);

                            // Insert the content into the correct section
                            $('#' + sectionId).html(response.content);

                            // Mark the section as loaded to prevent duplicate requests
                            $('#' + sectionId).data('loaded', true);

                            // Example: Initialize sliders after content is loaded
                            if (sectionId === 'slider-banner-section') {
                                initializeBannerSlider();
                            }

                            if (sectionId === 'category-section') {
                                initializeCategorySlider();
                            }

                            if (sectionId === 'stationary-section') {
                                initializeStationarySlider();
                            }

                            if (sectionId === 'best-seller-section') {
                                initializeBestSellerSlider();
                            }

                            if (sectionId === 'review-section') {
                                initializeReviewSlider();
                            }

                            // Re-observe lazy-load images in the newly loaded section
                            observeLazyLoadImages();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching content for section ' + sectionId + ':', error);
                        },
                        complete: function() {
                            // Hide loader for the section
                            hideLoader(sectionId);

                            loading = false; // Reset loading flag

                            processRequestQueue(); // Process next request in the queue
                        }
                    });
                }
            }






            // floating campaign offer

            const $campaignDiv = $('#campaign-div');
            const $toggleBtn = $('#toggle-btn');
            const $closeBtn = $('#close-btn');
            const $toggleIcon = $('#toggle-icon');

            let isOpen = false;

            $toggleBtn.on('click', function() {
                if (!isOpen) {
                    openCampaign();
                } else {
                    closeCampaign();
                }
            });

            $closeBtn.on('click', closeCampaign);

            function openCampaign() {
                $campaignDiv.addClass('open');
                $toggleIcon.removeClass('ri-arrow-right-double-fill').addClass('ri-arrow-left-double-fill');
                $toggleBtn.hide(); // Hide the toggle button when open
                isOpen = true;
            }

            function closeCampaign() {
                $campaignDiv.removeClass('open');
                $toggleIcon.removeClass('ri-arrow-left-double-fill').addClass('ri-arrow-right-double-fill');
                $toggleBtn.css('display', 'flex'); // Show the toggle button when closed
                isOpen = false;
            }

            // Automatically open the campaign div when the page loads
            openCampaign();

        });


    </script>
@endsection
