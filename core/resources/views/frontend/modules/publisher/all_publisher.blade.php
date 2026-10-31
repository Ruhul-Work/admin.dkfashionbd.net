@extends('frontend.layouts.master')
@section('meta')
    <title>All Publishers | {{ get_option('title') }}</title>
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
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">প্রকাশক</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="text-dark" href="{{ route('home') }}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="">প্রকাশক</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- End breadcrumb section -->

    <section class="subject-section section--padding">
        <div class="container">
            <div class="row mb-30">
                <div class="col-md-6 mx-auto">
                    <div class="search-container">


                        <div class="product__view--mode__list product__view--search d-flex justify-content-center">
                            <form class="product__view--search__form" id="publisher-search-form">
                                <label>
                                    <input class="product__view--search__input border-1"
                                           placeholder="প্রকাশক অনুসন্ধান করুন" type="text" id="publisher-search">
                                </label>
                                <button class="product__view--search__btn" aria-label="অনুসন্ধান" type="submit">
                                    <svg class="product__view--search__btn--svg" xmlns="http://www.w3.org/2000/svg"
                                         width="22.51" height="20.443" viewBox="0 0 512 512">
                                        <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z"
                                              fill="none" stroke="currentColor" stroke-miterlimit="10"
                                              stroke-width="32"/>
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                              stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <div id="searchResultsContainer" class="search-group">
                            <ul id="search-result" class="search-list">
                                <!-- Search results will be dynamically added here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="publishers-list">


            </div>
        </div>

    </section>
@endsection
@section('scripts')



    <script>
        $(document).ready(function() {
            // Function to fetch publishers via AJAX for a specific page
            function fetchPublishers(page = 1) {
                $.ajax({
                    url: '{{ route('publisher.get') }}',
                    type: 'GET',
                    data: {
                        page: page
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#publishers-list').html(response.html);
                        updatePagination(response.pagination);
                        updateURL(page);
                        window.scrollTo({
                            top: 20,
                            behavior: 'smooth'
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching publishers:', error);
                        console.error('Status:', status);
                        console.error('XHR:', xhr);
                        let errorMessage = 'An error occurred while fetching publishers. ';
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

            // Function to update pagination links
            function updatePagination(pagination) {
                $('.pagination__wrapper').html(pagination);
            }

            // Function to update the URL
            function updateURL(page) {
                var currentURL = window.location.href.split('?')[0]; // Get base URL
                var newURL = currentURL + '?page=' + page;
                history.pushState({}, '', newURL);
            }

            // Event handler for pagination links (using event delegation)
            $(document).on('click', '.pagination__wrapper a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1]; // Extract page number from URL
                // Fetch publishers for the clicked page
                fetchPublishers(page);
            });

            // Initial call to fetch publishers
            fetchPublishers()


            // Debounce function to delay execution of a function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        timeout = null;
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Function to perform search
            function performSearch() {
                var query = $('#publisher-search').val().trim();
                if (query.length === 0) {
                    $('#search-result').empty(); // Clear previous results
                    $('#searchResultsContainer').hide(); // Hide results container if no query
                    return;
                }

                $.ajax({
                    url: "{{ route('publisher.search') }}",
                    type: "GET",
                    dataType: 'json',
                    data: { query: query },
                    success: function(response) {
                        $('#search-result').empty();
                        // Append new results
                        if (response.publishers.length > 0) {
                            response.publishers.forEach(function(publisher) {
                                let publisherUrl = "{{ route('publisher.single', ['slug' => ':slug']) }}".replace(':slug', publisher.slug);
                                $('#search-result').append(
                                    `<li><a href="${publisherUrl}">${publisher.name}</a></li>`
                                );
                            });
                        } else {
                            $('#search-result').append('<li>No publishers found</li>');
                        }
                        $('#searchResultsContainer').show(); // Show results container
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); // Handle error
                    }
                });
            }

            // Debounced version of performSearch function (delayed by 300ms)
            const debouncedSearch = debounce(performSearch, 300);

            // Input event handler for search input
            $('#publisher-search').on('input', function() {
                debouncedSearch(); // Perform search function after debounce
            });

            // Submit event handler for search form
            $('#publisher-search-form').submit(function(e) {
                e.preventDefault(); // Prevent form submission
                performSearch(); // Perform search function
            });
        });
    </script>

@endsection
