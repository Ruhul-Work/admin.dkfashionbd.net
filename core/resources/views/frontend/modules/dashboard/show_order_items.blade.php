
@extends('frontend.layouts.master')
@section('meta')
    <title> Order Items | {{ get_option('title') }}</title>
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

                    <div class="col">
                        <h3 class="mb-3 text-center">অর্ডার আইটেম</h3>
                    </div>
                    <div class="row">
                        @foreach ($order->orderItems as $item)
                            <div class="col-md-4 mb-3">
                                <div class="product-card border rounded p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="javascript:void(0);" class="product-img  me-2">
                                            <img src="{{ image($item->product->thumb_image) }}" alt="product" class="img-fluid rounded">
                                        </a>
                                        <a href="javascript:void(0);" class="product-name">{{ $item->product->bangla_name }}</a>
                                    </div>
                                    <p><strong>পরিমাণ:</strong> {{ $item->qty }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


            </div>
        </div>
    </section>
    <!-- my account section end -->



@endsection
@section('scripts')

    <style>

        .product-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }

        .product-img img {
            max-width: 100px;
            max-height: 100px;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .product-card {
                padding: 10px;
            }

            .product-img img {
                max-width: 80px;
                max-height: 80px;
            }
        }

    </style>

@endsection




