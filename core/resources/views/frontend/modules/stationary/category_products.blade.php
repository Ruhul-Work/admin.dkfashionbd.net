@extends('frontend.layouts.master')
@section('meta')
    <title>{{ $category->name ?? 'Category products' }} | {{ get_option('title') }}</title>

    <meta property="og:title" content="{{ $category->name ?? 'Category' }} | {{ strtolower($category->meta_title ?? get_option('title')) }}">
    <meta property="og:description" content="{{ strip_tags($category->meta_description ?? get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset($category->meta_image ?? get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('title') }}">

    <!-- Add more Open Graph tags as needed -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $category->name ?? 'Category' }} | {{ strtolower($category->meta_title ?? get_option('title')) }}">
    <meta name="twitter:description" content="{{ strip_tags($category->meta_description ?? get_option('description')) }}">
    <meta name="twitter:image" content="{{ asset($category->meta_image ?? get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->

@endsection
@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title text-dark mb-25">{{$category->name}}</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="text-dark" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><a class="text-dark" href="{{route('stationary.index')}}">ষ্টেশনারী</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="text-dark">{{$category->name}}</span></li>
                        </ul>
                    </div>
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
        </div>
        </form>
    </div>
    <!-- End offcanvas filter sidebar -->
    <!-- End shop section -->
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
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
                    url: "{{ route('category.products', ['slug' => $category->slug]) }}",
                    type: 'GET',
                    data: filters,
                    success: function(response) {
                        var productsHtml = response.html;
                        $('#products-container').html(productsHtml);

                        observeLazyLoadImages();
                        hideLoader(loaderId);


                        window.scrollTo({
                            top: 20,
                            behavior: 'smooth'
                        });

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

                if (filters.publicationIds) {
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

