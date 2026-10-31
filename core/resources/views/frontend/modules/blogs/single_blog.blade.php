@extends('frontend.layouts.master')

@section('meta')
    <title>{{ $blog->title ?? 'Blog Details' }} | {{ get_option('title') }}</title>

    <meta property="og:title" content="{{ $blog->title ?? 'Blog Details' }} | {{ strtolower($blog->meta_title ?? get_option('title')) }}">
    <meta property="og:description" content="{{ strip_tags($blog->meta_description ?? get_option('description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset($blog->meta_image ??get_option('meta_image')) }}">
    <meta property="og:site_name" content="{{ get_option('company_name') }}">
    <!-- Add more Open Graph tags as needed -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $blog->title ?? 'Blog Details' }} | {{ strtolower($blog->meta_title ?? get_option('title')) }}">
    <meta name="twitter:description" content="{{ strip_tags($blog->meta_description ?? get_option('description')) }}">
    <meta name="twitter:image" content="{{ asset($blog->meta_image ?? get_option('meta_image')) }}">
    <!-- Add more Twitter meta tags as needed -->
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">ব্লগের বিস্তারিত</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items">
                                <a class="text-dark" href="{{ route('home') }}">হোম</a>
                            </li>
                            <li class="breadcrumb__content--menu__items">
                                <span class="">ব্লগের বিস্তারিত</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Start blog details section -->
    <section class="blog__details--section section--padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xxl-9 col-xl-8 col-lg-8">
                    <div class="blog__details--wrapper">
                        <div class="entry__blog">
                            <div class="blog__post--header mb-30">
                                <h2 class="post__header--title mb-15">{{$blog->title}}</h2>
                                <p class="blog__post--meta">প্রকাশিত: অ্যাডমিন / তারিখ : {{ $blog->created_at->format('F d, Y') }}</p>
                            </div>
                            <div class="blog__thumbnail mb-30">
                                <img class="blog__thumbnail--img border-radius-10" src="{{asset($blog->thumbnail)}}" alt="blog-img">
                            </div>
                            <div class="blog__details--content">


                             {!! $blog->content  !!}
                            </div>
                        </div>
                        <div class="blog__tags--social__media d-flex align-items-center justify-content-between">
                            <div class="blog__tags--media d-flex align-items-center">
                                <label class="blog__tags--media__title">সম্পর্কিত ট্যাগসমূহ :</label>
                                <ul class="d-flex">
                                    @foreach($blog->tags as $tag)
                                    <li class="blog__tags--media__list"><a class="blog__tags--media__link" href="javascript:void(0)">{{$tag->name}}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <div class="blog__social--media d-flex align-items-center">
                                <label class="blog__social--media__title">সোশ্যাল শেয়ার :</label>
                                <ul class="d-flex">
                                    <li class="blog__social--media__list">
                                        <a class="blog__social--media__link" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="7.667" height="16.524" viewBox="0 0 7.667 16.524">
                                                <path data-name="Path 237" d="M967.495,353.678h-2.3v8.253h-3.437v-8.253H960.13V350.77h1.624v-1.888a4.087,4.087,0,0,1,.264-1.492,2.9,2.9,0,0,1,1.039-1.379,3.626,3.626,0,0,1,2.153-.6l2.549.019v2.833h-1.851a.732.732,0,0,0-.472.151.8.8,0,0,0-.246.642v1.719H967.8Z" transform="translate(-960.13 -345.407)" fill="currentColor"></path>
                                            </svg>
                                            <span class="visually-hidden">Facebook</span>
                                        </a>
                                    </li>
                                    <li class="blog__social--media__list">
                                        <a class="blog__social--media__link" target="_blank" href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.489" height="13.384" viewBox="0 0 16.489 13.384">
                                                <path data-name="Path 303" d="M966.025,1144.2v.433a9.783,9.783,0,0,1-.621,3.388,10.1,10.1,0,0,1-1.845,3.087,9.153,9.153,0,0,1-3.012,2.259,9.825,9.825,0,0,1-4.122.866,9.632,9.632,0,0,1-2.748-.4,9.346,9.346,0,0,1-2.447-1.11q.4.038.809.038a6.723,6.723,0,0,0,2.24-.376,7.022,7.022,0,0,0,1.958-1.054,3.379,3.379,0,0,1-1.958-.687,3.259,3.259,0,0,1-1.186-1.666,3.364,3.364,0,0,0,.621.056,3.488,3.488,0,0,0,.885-.113,3.267,3.267,0,0,1-1.374-.631,3.356,3.356,0,0,1-.969-1.186,3.524,3.524,0,0,1-.367-1.5v-.057a3.172,3.172,0,0,0,1.544.433,3.407,3.407,0,0,1-1.1-1.214,3.308,3.308,0,0,1-.4-1.609,3.362,3.362,0,0,1,.452-1.694,9.652,9.652,0,0,0,6.964,3.538,3.911,3.911,0,0,1-.075-.772,3.293,3.293,0,0,1,.452-1.694,3.409,3.409,0,0,1,1.233-1.233,3.257,3.257,0,0,1,1.685-.461,3.351,3.351,0,0,1,2.466,1.073,6.572,6.572,0,0,0,2.146-.828,3.272,3.272,0,0,1-.574,1.083,3.477,3.477,0,0,1-.913.8,6.869,6.869,0,0,0,1.958-.546A7.074,7.074,0,0,1,966.025,1144.2Z" transform="translate(-951.23 -1140.849)" fill="currentColor"></path>
                                            </svg>




                                            <span class="visually-hidden">Twitter</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="related__post--area">
                            <div class="section__heading text-center mb-30">
                                <h2 class="section__heading--maintitle">সম্পর্কিত ব্লগসমূহ</h2>
                            </div>
                            <div class="row row-cols-md-2 row-cols-sm-2 row-cols-sm-u-2 row-cols-1 mb--n28">
                                @forelse($relatedBlogs as $blog)
                                    <div class="col mb-28">
                                        <div class="related__post--items">
                                            <div class="related__post--thumb border-radius-10 mb-15">
                                                <a class="display-block" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?? $blog->id]) }}">
                                                    <img class="related__post--img display-block border-radius-10" src="{{ asset($blog->thumbnail) }}" alt="{{ $blog->title }}">
                                                </a>
                                            </div>
                                            <div class="related__post--text">
                                                <h3 class="related__post--title">
                                                    <a class="related__post--title__link" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?? $blog->id]) }}">{{ $blog->title }}</a>
                                                </h3>
                                                <span class="related__post--deta">{{ $blog->created_at->format('F d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center">কোন সম্পর্কিত ব্লগ পাওয়া যায়নি</p>
                                @endforelse
                            </div>
                        </div>


                        <div class="comment__box">
                            <div class="reviews__comment--area2 mb-50">
                                <h2 class="reviews__comment--reply__title mb-25">সাম্প্রতিক মন্তব্য</h2>
                                <div class="reviews__comment--inner" id="reviewList">
                                    @forelse($blog->comments as $comment)



                                        @include('frontend.modules.blogs.comment_list',['comment' =>$comment])

                                    @empty
                                        <p>এখনো কোনো মন্তব্য নেই.</p>
                                    @endforelse
                                </div>
                            </div>

                            @auth
                            <div class="reviews__comment--reply__area">
                                <form id="reviewForm"  method="POST">
                                    @csrf
                                    <h2 class="reviews__comment--reply__title mb-20">মন্তব্য করুন</h2>
                                    <div class="row">


                                        <div class="col-lg-12 mb-15">
                                            <label for="comment">
                                                <textarea class="reviews__comment--reply__textarea" placeholder="Your Comments...." name="comment" id="comment"></textarea>
                                                <span class="error-message" id="commentError"></span>
                                            </label>
                                            <span id="charCount" class="char-count">0 characters</span>
                                        </div>


                                    </div>
                                    <button class="reviews__comment--btn primary__btn text-white" data-hover="Submit" type="submit">জমা দিন</button>
                                </form>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-4 col-lg-4">
                    <div class="blog__sidebar--widget left widget__area">
                        <div class="single__widget widget__search widget__bg ">
                            <h2 class="widget__title h3">ব্লগ অনুসন্ধান করুন</h2>
                            <form id="searchForm" class="widget__search--form search-container ">
                                <label>
                                    <input id="searchInput" class="widget__search--form__input" name="query" placeholder="ব্লগ অনুসন্ধান..." type="text">
                                </label>
                                <button class="widget__search--form__btn" aria-label="search button" type="button" onclick="performSearch()">
                                    <svg class="product__items--action__btn--svg" xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                                        <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"></path>
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448"></path>
                                    </svg>
                                </button>

                                <div id="searchResults" class="blog-search-result">

                                </div>
                            </form>


                        </div>


                        <div class="single__widget widget__bg">
                            <h2 class="widget__title h3">{{ __(' সর্বশেষ ব্লগসমূহ') }}</h2>
                            <ul class="widget__categories--menu">
                                @foreach($blogCategories as $blogCategory)
                                    <li class="widget__categories--menu__list">
                                        <label class="widget__categories--menu__label d-flex align-items-center">
                                            @if($blogCategory->image)
                                            <img class="widget__categories--menu__img" src="{{ asset($blogCategory->image) }}" alt="categories-img">
                                            @else
                                                <img class="widget__categories--menu__img" src="{{ asset('theme/frontend/assets/img/icon/blog_cat.png') }}" alt="categories-img">
                                            @endif


                                            <span class="widget__categories--menu__text">{{ $blogCategory->name }}</span>
                                            <svg class="widget__categories--menu__arrowdown--icon" xmlns="http://www.w3.org/2000/svg" width="12.355" height="8.394">
                                                <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z" transform="translate(-6 -8.59)" fill="currentColor"></path>
                                            </svg>
                                        </label>
                                        <ul class="widget__categories--sub__menu">
                                            @forelse($blogCategory->blogs->take(2) as $blog)
                                            <li class="widget__categories--sub__menu--list">
                                                <a class="widget__categories--sub__menu--link d-flex align-items-center" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">
                                                    <img class="widget__categories--sub__menu--img lazy-load" data-src="{{ asset($blog->thumbnail) }}" src="{{asset('theme/frontend/assets/img/default/blog.png')}}" alt="categories-img">
                                                    <span class="widget__categories--sub__menu--text">{{ $blog->title }}</span>
                                                </a>
                                            </li>
                                            @empty
                                                <li class="widget__categories--sub__menu--list">{{ __('No blogs available') }}</li>
                                            @endforelse
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>



                        <div class="single__widget widget__bg">
                            <h2 class="widget__title h3">সর্বাধিক পঠিত ব্লগগুলি</h2>
                            <div class="product__grid--inner">
                                @foreach($mostReadBlogs as $blog)
                                    <div class="product__items product__items--grid d-flex align-items-center">
                                        <div class="product__items--grid__thumbnail position__relative">
                                            <a class="product__items--link" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">
                                                <img class="product__grid--items__img product__primary--img" src="{{ asset($blog->thumbnail) }}" alt="{{ $blog->title }}">
                                                <img class="product__grid--items__img product__secondary--img" src="{{ asset($blog->thumbnail) }}" alt="{{ $blog->title }}">
                                            </a>
                                        </div>
                                        <div class="product__items--grid__content">
                                            <h3 class="product__items--content__title h4"><a href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">{{ $blog->title }}</a></h3>
                                            <span class="meta__deta">{{ $blog->created_at->format('F j, Y') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        <div class="single__widget widget__bg">
                            <h2 class="widget__title h3">ক্যাটেগরি</h2>
                            <ul class="widget__tagcloud">
                                @foreach($allCategories as $category)
                                    <li class="widget__tagcloud--list">
                                        <a class="widget__tagcloud--link" href="{{ route('blogs.by.categories', ['slug_or_id' => $category->slug ?: $category->id]) }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End blog details section -->



@endsection
@section('scripts')
    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }
    </style>



    <script>
        // Debounce function
        function debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function performSearch() {
            var query = $('#searchInput').val();
            var searchResults = $('#searchResults');

            if (query.trim() === '') {

                searchResults.hide();
                return;
            }

            $.ajax({
                url: '{{ route('blogs.search') }}',
                type: 'GET',
                data: { query: query },
                success: function(response) {
                    if (response.trim() === '') {
                        searchResults.hide();
                    } else {
                        searchResults.html(response).show();
                    }
                },
                error: function(xhr) {
                    searchResults.html('<p>An error occurred. Please try again.</p>').show();
                }
            });
        }

        // Bind to 'input' event with debounce
        $('#searchInput').on('input', debounce(performSearch, 300));


        $(document).ready(function() {

            var reviewSubmitted = false;
            $('#reviewForm').submit(function(e) {
                e.preventDefault();

                // Check if review has already been submitted
                if (reviewSubmitted) {
                    alert('You have already submitted a Comment.');
                    return false;
                }

                // Disable submit button to prevent multiple submissions
                $('#reviewForm button[type="submit"]').prop('disabled', true);


                // Clear previous error messages
                $('.error-message').text('');

                // Validate form fields
                var isValid = true;



                var comment = $('#comment').val().trim();
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
                    return false;
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
                    url: '{{ route('blogs.comment.store', $blog->id) }}',
                    data: formData,
                    success: function(response) {

                        showToast('Comment submitted successfully!', 'success');

                        $('#reviewList').append(response.html);

                        $('#reviewForm')[0].reset();

                        $('#charCount').hide();

                        $('.error-message').text('');
                        reviewSubmitted = true;
                        $('#reviewForm button[type="submit"]').prop('disabled', false);
                    },
                    error: function(error) {

                        // alert('Error submitting review. Please try again.');
                        showToast('Error submitting Comment. Please try again.', 'danger');

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

