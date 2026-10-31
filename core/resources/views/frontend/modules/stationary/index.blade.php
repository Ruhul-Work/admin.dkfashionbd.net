@extends('frontend.layouts.master')
@section('meta')
 <title>Stationary | {{ get_option('title') }}</title>

 <!-- Open Graph / Facebook -->
 <meta property="og:title" content="Stationary | {{ get_option('title') }}" />
 <meta property="og:type" content="website" />
 <meta property="og:url" content="{{ url()->current() }}" />
 <meta property="og:description" content="Explore a wide range of stationary products including pens, pencils, notebooks, and more. Find quality stationary items at competitive prices." />
 <meta property="og:image" content="{{ asset(get_option('meta_image')) }}">
 <meta property="og:site_name" content="{{ get_option('company_name') }}">

 <!-- Add more Open Graph tags as needed -->

 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="{{get_option('title')}}">
 <meta name="twitter:description" content="Explore a wide range of stationary products including pens, pencils, notebooks, and more. Find quality stationary items at competitive prices.">
 <meta name="twitter:image" content="{{ asset(get_option('meta_image')) }}">
@endsection


@section('content')

    <!-- Start breadcrumb section -->
    <section class="stationary__bg">
        <div class="container">

        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- Start banner section -->
    <section class="banner__section banner__style2 section--padding">
        <div class="section__heading text-center mb-35">
            <h2 class="section__heading--maintitle">স্টেশনারী</h2>
        </div>
        <div class="container-fluid">
            <div class="row" id="categories-list">



            </div>
        </div>
    </section>
    <!-- End banner section -->
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Function to fetch categories via AJAX for a specific page
            function fetchCategories(page = 1) {

                var loaderId ='categories-list';

                showLoader(loaderId);
                $.ajax({
                    url: '{{ route('stationary.category.get') }}',
                    type: 'GET',
                    data: {
                        page: page
                    },
                    dataType: 'json',
                    success: function(response) {
                        hideLoader(loaderId);

                        $('#categories-list').html(response.html);


                        observeLazyLoadImages();

                        updatePagination(response.pagination);


                        updateURL(page);
                        window.scrollTo({
                            top: 20,
                            behavior: 'smooth'
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching categories:', error);
                        console.error('Status:', status);
                        console.error('XHR:', xhr);
                        let errorMessage = 'An error occurred while fetching categories. ';
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
                // Fetch categories for the clicked page
                fetchCategories(page);
            });

            fetchCategories()
        });

    </script>
@endsection



