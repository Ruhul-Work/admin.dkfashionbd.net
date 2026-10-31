@extends('frontend.layouts.master')
@section('meta')
    <title>{{ $campaign->name ?? 'Campaign Products' }} | {{ get_option('title') }}</title>
    <meta property="og:title" content="{{ $campaign->name }} | {{ strtolower($campaign->meta_title ?? get_option('title')) }}">
    <meta property="og:description" content="{{ strip_tags($campaign->meta_description ?? get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset($campaign->meta_image ?? get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('company_name') }}">


    <!-- Add more Open Graph tags as needed -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $campaign->name }} | {{ strtolower($campaign->meta_title ?? get_option('title')) }}">
    <meta name="twitter:description" content="{{ strip_tags($campaign->meta_description ?? get_option('description')) }}">
    <meta name="twitter:image" content="{{ image($campaign->meta_image ?? get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->
@endsection
@section('content')
    <section class="campaign__details section--padding">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-2 text-center">
                    <img src="{{ asset($campaign->icon) }}" alt="{{ $campaign->name }}" class="img-fluid">
                </div>
                <div class="col-xl-9 col-lg-8 col-md-6">
                    <h2 class="campaign__name">{{ $campaign->name }}</h2>
                        <p id="countdown" class="my-3"></p>
                    <p class="campaign__total-products">মোট পণ্য:  {{ $products->count() }}</p>


          
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->
    <!-- Start shop section -->
    <section class="shop__section section--padding">
        <div class="container-fluid">
            <div class="shop__header bg__gray--color d-flex align-items-center justify-content-between mb-30">
                <button class="widget__filter--btn d-flex d-lg-none align-items-center" data-offcanvas>
                    <svg  class="widget__filter--btn__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="28" d="M368 128h80M64 128h240M368 384h80M64 384h240M208 256h240M64 256h80"/><circle cx="336" cy="128" r="28" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="28"/><circle cx="176" cy="256" r="28" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="28"/><circle cx="336" cy="384" r="28" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="28"/></svg>
                    <span class="widget__filter--btn__text">Filter</span>
                </button>
                <div class="product__view--mode__list product__short--by align-items-center d-none d-lg-flex ">
                    @include('frontend.modules.sorting_option.sorting')
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <form id="filter-form">
                        <div class="shop__sidebar--widget widget__area d-none d-lg-block">
                            @include('frontend.modules.filter_option.publication')
                            @include('frontend.modules.filter_option.author')
                        </div>
                    </form>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="shop__product--wrapper" id="products-container">
                        <div class="loader" style="display: none;">


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start offcanvas filter sidebar -->
    <div class="offcanvas__filter--sidebar widget__area">
        <button type="button" class="offcanvas__filter--close" data-offcanvas>
            <svg class="minicart__close--icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144M368 144L144 368"></path></svg> <span class="offcanvas__filter--close__text">Close</span>
        </button>
        <form id="filter-form">
            <div class="offcanvas__filter--sidebar__inner">

                <div class="shop__header bg__gray--color d-flex align-items-center justify-content-between mb-30">
                    <div class="product__view--mode__list product__short--by align-items-center  ">

                        <div style="padding: 1rem;
    border-radius: 0.5rem;
    -webkit-box-shadow: 0 2px 22px rgba(0, 0, 0, 0.1);
     box-shadow: 0 2px 22px rgba(0, 0, 0, 0.1); ">
                            @include('frontend.modules.sorting_option.sorting')
                        </div>

                    </div>
                </div>

                @include('frontend.modules.filter_option.publication')
                @include('frontend.modules.filter_option.author')

            </div>
        </form>
    </div>
    <!-- End offcanvas filter sidebar -->
    <!-- End shop section -->
@endsection
@section('scripts')
    <link rel="stylesheet" href="{{ asset('theme/frontend/assets/css/timeTo.css') }}" />
    <script src="{{ asset('theme/frontend/assets/js/jquery.time-to.js') }}" defer></script>

    <style>
        .campaign__details img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .campaign__name {
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--bintel-danger-color);
        }

        .campaign__total-products {
            font-size: 2.5rem;
            color: #666;
            margin-top: 45px;
            margin-bottom: 20px;
        }

        .campaign__notes {
            font-size: 1.7rem;
            color: var(--bintel-success-color);
            line-height: 1.6;
            margin-bottom: 15px;
        }



        @media (max-width: 768px) {
            .campaign__details .col-md-6 {
                text-align: center; /* Center align columns on smaller screens */
            }

            .campaign__details .col-md-6:nth-child(2) {
                margin-top: 20px;
                padding-left: 0;
            }

            .campaign__name {
                font-size: 2rem;
            }

            .campaign__total-products {
                font-size: 1.5rem;

            }

            .campaign__notes {
                font-size: 1.2rem;

            }

        }
    </style>
    <script>

        $(document).ready(function() {

            var endDateTime = "{{ $campaign->end_date }}";

            // Initialize timeTo.js countdown
            $('#countdown').timeTo({
                timeTo: new Date(endDateTime),
                displayDays: 2,
                // theme: "white",
                theme: "black",
                fontSize: 30,
                displayCaptions: true,
                captionSize: 14
            });
        });


        $(document).ready(function() {

            $('#author-search').on('input', function() {
                var query = $(this).val();
                searchFilter(query, '#author-list', '.author-name');
            });

            $('#publisher-search').on('input', function() {
                var query = $(this).val();
                searchFilter(query, '#publisher-list', '.publisher-name');
            });
        });


        $(document).ready(function() {

            // Function to fetch products based on filters, sorting, and pagination
            function fetchProducts(filters = {}, sortBy = 'latest', page = 1) {
                var loaderId = 'products-container';
                showLoader(loaderId);

                // Add sorting and pagination parameters to filters
                filters.sortBy = sortBy;
                filters.page = page;


                $.ajax({
                    url: "{{ route('campaign.products', ['slug' => $campaign->slug]) }}",
                    type: 'GET',
                    data: filters,
                    success: function(response) {
                        var productsHtml = response.html;
                        $('#products-container').html(productsHtml);

                        // updatePagination(response.pagination); // Update pagination dynamically


                        observeLazyLoadImages();
                        hideLoader(loaderId);


                        window.scrollTo({
                            top: 20,
                            behavior: 'smooth'
                        });

                        // if ($(".offcanvas__filter--close").length) {
                        //
                        //     $(".offcanvas__filter--close").click();
                        // }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching products:', error);
                        console.error('Status:', status);
                        console.error('XHR:', xhr);
                        let errorMessage = 'An error occurred while fetching products. ';
                        if (xhr.status && xhr.statusText) {
                            errorMessage += `Status: ${xhr.status} - ${xhr.statusText}`;
                        }
                        if (xhr.responseText) {
                            errorMessage += `\nResponse: ${xhr.responseText}`;
                        }
                        console.log(errorMessage);
                    }
                });
            }

            // Function to update URL using pushState
            function updateURL(filters, sortBy, page) {
                var currentURL = window.location.href.split('?')[0]; // Get base URL
                var queryString = '';

                // Build custom query string based on filters, sorting, and pagination
                if (filters.authorIds) {
                    queryString += `authorIds=${filters.authorIds}`;
                }
                if (filters.publicationIds) {
                    queryString += `${queryString ? '&' : ''}publicationIds=${filters.publicationIds}`;
                }
                if (sortBy) {
                    queryString += `${queryString ? '&' : ''}sortBy=${sortBy}`;
                }
                if (page) {
                    queryString += `${queryString ? '&' : ''}page=${page}`;
                }
                var newURL = currentURL + (queryString ? '?' + queryString : '');
                history.pushState({}, '', newURL);
            }

            // Event handler for filter changes (using event delegation)
            $(document).on('change', '.filter-checkbox', function() {
                var filters = {};

                // Process author filter
                var selectedAuthors = [];
                $('input[name="authors[]"]:checked').each(function() {
                    selectedAuthors.push($(this).val());
                });

                if (selectedAuthors.length > 0) {
                    filters.authorIds = selectedAuthors.join(',');
                }

                // Process publication filter
                var selectedPublications = [];
                $('input[name="publishers[]"]:checked').each(function() {
                    selectedPublications.push($(this).val());
                });

                if (selectedPublications.length > 0) {
                    filters.publicationIds = selectedPublications.join(',');
                }

                // Fetch products based on current filters, current sort, and reset to page 1
                var currentSortBy = $('.sort_by').val(); // Get current sort value
                fetchProducts(filters, currentSortBy, 1);

                // Update URL to reflect current filter, sort, and page state
                updateURL(filters, currentSortBy, 1);
            });

            // Event handler for sorting selection
            $('.sort_by').change(function() {
                var filters = {};

                // Process existing filters
                var selectedAuthors = [];
                $('input[name="authors[]"]:checked').each(function() {
                    selectedAuthors.push($(this).val());
                });

                if (selectedAuthors.length > 0) {
                    filters.authorIds = selectedAuthors.join(',');
                }

                var selectedPublications = [];
                $('input[name="publishers[]"]:checked').each(function() {
                    selectedPublications.push($(this).val());
                });

                if (selectedPublications.length > 0) {
                    filters.publicationIds = selectedPublications.join(',');
                }

                // Get selected sort value
                var sortBy = $(this).val();

                // Fetch products based on current filters, selected sort, and reset to page 1
                fetchProducts(filters, sortBy, 1);

                // Update URL to reflect current filter, sort, and page state
                updateURL(filters, sortBy, 1);
            });

            // Event handler for pagination links (using event delegation)
            $(document).on('click', '.pagination__wrapper a', function(e) {
                e.preventDefault();

                var page = $(this).attr('href').split('page=')[1]; // Extract page number from URL
                var sortBy = $('.sort_by').val(); // Get current sort value

                // Fetch products for the clicked page with current filters and sort
                fetchProducts({}, sortBy, page);

                // Update URL to reflect current filter, sort, and page state
                updateURL({}, sortBy, page);
            });

            // Function to parse query parameters from URL on page load
            function parseQueryParams() {
                var queryParams = {};
                var queryString = window.location.search.substring(1);
                var pairs = queryString.split('&');

                for (var i = 0; i < pairs.length; i++) {
                    var pair = pairs[i].split('=');
                    var key = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1] || '');
                    if (key && value) {
                        queryParams[key] = value;
                    }
                }

                return queryParams;
            }

            // Function to apply initial filters, sort, and page on page load
            function applyInitialFilters() {
                var filters = parseQueryParams();
                var sortBy = filters.sortBy || 'latest'; // Default sorting
                var page = filters.page || 1; // Default page

                if (filters.authorIds || filters.publicationIds) {
                    fetchProducts(filters, sortBy, page);
                } else {
                    fetchProducts({}, sortBy, page); // Fetch products without filters
                }
            }

            // Initial fetch of products
            applyInitialFilters();

        });

    </script>
@endsection


