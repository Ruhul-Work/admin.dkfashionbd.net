@extends('frontend.layouts.master')
@section('meta')
    <title>About Us | {{ get_option('title') }}</title>
@endsection

@section('content')

    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">আমাদের সম্পর্কে</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="text-dark" href="{{ route('home') }}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="">আমাদের সম্পর্কে</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- Start about section -->
    <section class="about__section section--padding mb-95">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about__thumb d-flex">
                        <div class="about__thumb--items">
                            <img class="about__thumb--img border-radius-5 display-block" src="{{asset('theme/frontend/assets/img/other/2.png')}}" alt="about-thumb">
                        </div>
                        <div class="about__thumb--items position__relative">
                            <img class="about__thumb--img border-radius-5 display-block" src="{{asset('theme/frontend/assets/img/other/2.png')}}" alt="about-thumb">
                            <div class="banner__bideo--play about__thumb--play">
                                <a class="banner__bideo--play__icon about__thumb--play__icon glightbox" href="https://www.youtube.com/embed/3Jv3t1aETyY?si=1GHeWPCFzHpTebvY" data-gallery="video">
                                    <svg id="play" xmlns="http://www.w3.org/2000/svg" width="40.302" height="40.302" viewBox="0 0 46.302 46.302">
                                        <g id="Group_193" data-name="Group 193" transform="translate(0 0)">
                                            <path id="Path_116" data-name="Path 116" d="M39.521,6.781a23.151,23.151,0,0,0-32.74,32.74,23.151,23.151,0,0,0,32.74-32.74ZM23.151,44.457A21.306,21.306,0,1,1,44.457,23.151,21.33,21.33,0,0,1,23.151,44.457Z" fill="currentColor"/>
                                            <g id="Group_188" data-name="Group 188" transform="translate(15.588 11.19)">
                                                <g id="Group_187" data-name="Group 187">
                                                    <path id="Path_117" data-name="Path 117" d="M190.3,133.213l-13.256-8.964a3,3,0,0,0-4.674,2.482v17.929a2.994,2.994,0,0,0,4.674,2.481l13.256-8.964a3,3,0,0,0,0-4.963Zm-1.033,3.435-13.256,8.964a1.151,1.151,0,0,1-1.8-.953V126.73a1.134,1.134,0,0,1,.611-1.017,1.134,1.134,0,0,1,1.185.063l13.256,8.964a1.151,1.151,0,0,1,0,1.907Z" transform="translate(-172.366 -123.734)" fill="currentColor"/>
                                                </g>
                                            </g>
                                            <g id="Group_190" data-name="Group 190" transform="translate(28.593 5.401)">
                                                <g id="Group_189" data-name="Group 189">
                                                    <path id="Path_118" data-name="Path 118" d="M328.31,70.492a18.965,18.965,0,0,0-10.886-10.708.922.922,0,1,0-.653,1.725,17.117,17.117,0,0,1,9.825,9.664.922.922,0,1,0,1.714-.682Z" transform="translate(-316.174 -59.724)" fill="currentColor"/>
                                                </g>
                                            </g>
                                            <g id="Group_192" data-name="Group 192" transform="translate(22.228 4.243)">
                                                <g id="Group_191" data-name="Group 191">
                                                    <path id="Path_119" data-name="Path 119" d="M249.922,47.187a19.08,19.08,0,0,0-3.2-.27.922.922,0,0,0,0,1.845,17.245,17.245,0,0,1,2.889.243.922.922,0,1,0,.31-1.818Z" transform="translate(-245.801 -46.917)" fill="currentColor"/>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="visually-hidden">Video Play</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about__content">
                        <span class="about__content--subtitle text__secondary mb-20">কেন আমাদের নির্বাচন করবেন</span>
                        <h2 class="about__content--maintitle mb-25">ইংলিশ মজা: শুধুমাত্র ইংরেজি বইয়ের জন্য নির্ভরযোগ্য ই-কমার্স সাইট</h2>
                        <p class="about__content--desc mb-20">ইংলিশ মজা একটি ওয়েবসাইট যেখানে শুধুমাত্র REW Publications-এর বই বিক্রি করা হয়। এই ওয়েবসাইটে শুধুমাত্র ইংরেজি বই পাওয়া যায়। এখানে আপনি প্রতিটি বইয়ের কয়েকটি পৃষ্ঠা পড়ে অর্ডার করতে পারেন। এই ওয়েবসাইটে উপলব্ধ বইগুলির দাম লাইব্রেরির দামের সমান।</p>
                        <p class="about__content--desc mb-25">ইংলিশ মজা একটি সম্পূর্ণ ব্যক্তিগত মালিকানাধীন অনলাইন প্ল্যাটফর্ম - ই-কমার্স সাইট।</p>

                        <div class="about__author position__relative d-flex align-items-center">
                            <div class="about__author--left">
                                <h4 class="about__author--name">এম. রফিক</h4>
                                <span class="about__author--rank">চেয়ারম্যান</span>
                            </div>
                            <!-- <img class="about__author--signature display-block" src="assets/img/icon/signature.png" alt="signature"> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End about section -->

    <!-- Start counterup banner section -->
    <div class="counterup__banner--section counterup__banner__bg2" id="funfactId">
        <div class="container">
            <div class="row row-cols-1 align-items-center">
                <div class="col">
                    <div class="counterup__banner--inner position__relative d-flex align-items-center justify-content-between">
                        <div class="counterup__banner--items text-center">

                            <img   src="{{asset('theme/frontend/assets/img/other/student.png')}}" alt="student-icon">
                            <h2 class="counterup__banner--items__text text-white mt-3">শিক্ষার্থী</h2>
                            <span class="counterup__banner--items__number js-counter text-white" data-count="1000000">0+</span>
                        </div>
                        <div class="counterup__banner--items text-center">
                            <img   src="{{asset('theme/frontend/assets/img/other/course.png')}}" alt="course-icon">
                            <h2 class="counterup__banner--items__text text-white mt-3">কোর্স</h2>
                            <span class="counterup__banner--items__number js-counter text-white" data-count="680">0+</span>
                        </div>
                        <div class="counterup__banner--items text-center">
                            <img  src="{{asset('theme/frontend/assets/img/other/ebbok.png')}}" alt="ebbok-icon">
                            <h2 class="counterup__banner--items__text text-white mt-3">ই-বুক</h2>
                            <span class="counterup__banner--items__number js-counter text-white" data-count="116">0</span>
                        </div>
                        <div class="counterup__banner--items text-center">
                            <img src="{{asset('theme/frontend/assets/img/other/user.png')}}" alt="user-icon">
                            <h2 class="counterup__banner--items__text text-white mt-3">ইউসার</h2>
                            <span class="counterup__banner--items__number js-counter text-white" data-count="710000">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End counterup banner section -->





@endsection

