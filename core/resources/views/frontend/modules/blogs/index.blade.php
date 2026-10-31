@extends('frontend.layouts.master')
@section('meta')
    <title>All Blogs | {{ get_option('title') }}</title>

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
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">ব্লগ</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items">
                                <a class="text-dark" href="{{ route('home') }}">হোম</a>
                            </li>
                            <li class="breadcrumb__content--menu__items">
                                <span class="">ব্লগ</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Start blog section -->
    <section class="blog__section section--padding">
        <div class="container">
            <div class="section__heading text-center mb-50">
                <h2 class="section__heading--maintitle">ব্লগ</h2>
            </div>
            <div class="blog__section--inner">
                    <div id="blog-list">

                    </div>
            </div>
        </div>
    </section>
    <!-- End blog section -->




@endsection



@section('scripts')


    <script>
        $(document).ready(function() {
            // Function to fetch blogs via AJAX for a specific page
            function fetchBlogs(page = 1) {
                var loaderId = 'blog-list' ;

                showLoader(loaderId);
                $.ajax({
                    url: '{{ route('blogs.get') }}',
                    type: 'GET',
                    data: {
                        page: page
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#blog-list').html(response.html);
                        observeLazyLoadImages();
                        updatePagination(response.pagination);
                        updateURL(page);
                        window.scrollTo({
                            top: 20,
                            behavior: 'smooth'
                        });
                        hideLoader(loaderId);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching blogs:', error);
                        console.error('Status:', status);
                        console.error('XHR:', xhr);
                        let errorMessage = 'An error occurred while fetching blogs. ';
                        if (xhr.status && xhr.statusText) {
                            errorMessage += `Status: ${xhr.status} - ${xhr.statusText}`;
                        }
                        if (xhr.responseText) {
                            errorMessage += `\nResponse: ${xhr.responseText}`;
                        }
                        console.log(errorMessage);
                        hideLoader(loaderId);
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
                // Fetch  for the clicked page
                fetchBlogs(page);
            });

            // Initial call to fetch
            fetchBlogs();

        });

    </script>
@endsection

