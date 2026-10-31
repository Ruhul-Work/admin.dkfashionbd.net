@extends('frontend.layouts.master')

@section('meta')
    <title>All Blogs of {{ $blogCategory->name}}  | {{ get_option('title') }}</title>

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
                                <span class="">{{ $blogCategory->name}}</span>
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
@include('frontend.modules.blogs.blog_design')
                </div>
            </div>
        </div>
    </section>
    <!-- End blog section -->




@endsection
