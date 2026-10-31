@extends('frontend.layouts.master')
@section('meta')
    <title>User Profile | {{ get_option('title') }}</title>
@endsection

@section('content')

    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">ড্যাশবোর্ড</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="">ড্যাশবোর্ড</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- my account section start -->
    <section class="my__account--section section--padding">
        <div class="container">
            <div class="my__account--section__inner border-radius-10 d-flex">


                @include('frontend.modules.dashboard.sidebar')

                <div class="account__wrapper">
                    <div class="account__content">

                        <div class="row ">
                            <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="account__content--title mb-20 ">প্রোফাইল</h3>
                                </div>
                                <div class="card-body">
                                    <h2 class="mb-2">{{ $user->name }}</h2>

                                    <p class="text-muted mb-1">
                                        <i class="ri-phone-line"></i> ফোন: {{ $user->phone }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="ri-phone-fill"></i> বিকল্প ফোন: {{ $user->alternate_phone }}
                                    </p>
                                    <p class="text-muted mb-2">
                                        <i class="ri-mail-line"></i> ইমেইল: {{ $user->email }}
                                    </p>

                                    <a href="{{ route('profile.edit') }}" class="btn btn-primary py-2">
                                        <i class="ri-edit-line"></i> প্রোফাইল এডিট
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>




@endsection
@section('scripts')

    <style>

        .btn-primary {
            margin-right: 10px;
        }

        .text-muted {
            color: #6c757d;
        }


    </style>
@endsection
