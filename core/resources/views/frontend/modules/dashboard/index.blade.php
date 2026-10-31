
@extends('frontend.layouts.master')
@section('meta')
    <title>User Dashboard | {{ get_option('title') }}</title>
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


                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                            <div class="card dashboard-card card-color-1">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="text-center text-white">
                                        <h4 class="mb-10">মোট অর্ডার</h4>
                                        <p class="text-white  fw-bold">{{ $totalOrders }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                            <div class="card dashboard-card card-color-2">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="text-center text-white">
                                        <h4 class="mb-10">মোট খরচ</h4>
                                        <p class="text-white  fw-bold">৳{{$totalSpend }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                            <div class="card dashboard-card card-color-3">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="text-center text-white">
                                        <h4 class="mb-10">পথে আছে</h4>
                                        <p class="text-white  fw-bold">{{ $onTheWayOrders }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                            <div class="card dashboard-card card-color-4">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="text-center text-white">
                                        <h4 class="mb-10">ডেলিভারড</h4>
                                        <p class="text-white  fw-bold">{{ $deliveredOrders }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <hr>
                    <div class="account__content">
                        <h2 class="account__content--title h3 mb-20">অর্ডার ইতিহাস</h2>

                        <div class="account__table--area">
                            <table class="account__table">
                                <thead class="account__table--header">
                                <tr class="account__table--header__child">
                                    <th class="account__table--header__child--items">অর্ডার</th>
                                    <th class="account__table--header__child--items">তারিখ</th>
                                    <th class="account__table--header__child--items">পেমেন্ট স্ট্যাটাস</th>
                                    <th class="account__table--header__child--items">মোট</th>
                                    <th class="account__table--header__child--items">অর্ডার ট্র্যাক করুন</th>
                                </tr>
                                </thead>

                                <tbody class="account__table--body mobile__none">
                                @foreach ($orders as $order)
                                    <tr class="account__table--body__child">
                                        <td class="account__table--body__child--items">#{{ $order->order_number }}</td>
                                        <td class="account__table--body__child--items">{{ $order->created_at->format('F d, Y') }}</td>
                                        <td class="account__table--body__child--items">{{ $order->payment_status }}</td>

                                        <td class="account__table--body__child--items">{{formatPrice( $order->total) }}</td>
                                        <td class="account__table--body__child--items">
                                            <a href="{{ route('orders.track', $order->id) }}" class="btn btn-secondary me-2">অর্ডার ট্র্যাক</a>
                                            <a href="{{ route('orders.show.items', $order->id) }}" class="btn btn-primary">অর্ডার আইটেম</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>


{{--                                mobile version--}}
                                <tbody class="account__table--body mobile__block">
                                @foreach ($orders as $order)
                                    <tr class="account__table--body__child">
                                        <td class="account__table--body__child--items">
                                            <strong>অর্ডার</strong>
                                            <span>#{{ $order->order_number}}</span>
                                        </td>
                                        <td class="account__table--body__child--items">
                                            <strong>তারিখ</strong>
                                            <span>{{ $order->created_at->format('F d, Y') }}</span>
                                        </td>
                                        <td class="account__table--body__child--items">
                                            <strong>পেমেন্ট স্ট্যাটাস</strong>
                                            <span>{{ $order->payment_status }}</span>
                                        </td>
                                        <td class="account__table--body__child--items">
                                            <strong>মোট</strong>
                                            <span>{{ formatPrice($order->total) }}</span>
                                        </td>
                                        <td class="account__table--body__child--items">
                                            <strong>অর্ডার ট্র্যাক করুন</strong>
                                            <span><a href="{{ route('orders.track', $order->id) }}" class="btn btn-secondary">অর্ডার ট্র্যাক</a></span>
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- my account section end -->




@endsection
@section('scripts')







@endsection



