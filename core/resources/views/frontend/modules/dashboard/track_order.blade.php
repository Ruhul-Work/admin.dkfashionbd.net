
@extends('frontend.layouts.master')
@section('meta')
    <title>Track Order | {{ get_option('title') }}</title>
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
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header text-center" style="background-color: #dbf3ff">
                                    <h3>আপনার অর্ডার শনাক্ত করুন</h3>
                                </div>

                                <div class="card-body">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body row">
                                            <div class="col-md-4 col-12">
                                                <strong>অর্ডার</strong><br>
                                                #{{$order->order_number}}
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <strong>ডেলিভারি ETA</strong><br>
                                                {{get_option('delivery_time')}}

                                            </div>
                                            <div class="col-md-4 col-12">
                                                <strong>তারিখ</strong><br>
                                                {{ $order->created_at->format('F d, Y') }}
                                            </div>
                                        </div>

                                    </div>

                                @if($order->status_id != 5)
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="card card-timeline px-2 py-3 border-none">
                                                <ul class="bs4-order-tracking">
                                                    <li class="step @if($order->order_status_id == 1) active @endif">
                                                        <div><i class="ri-timer-line"></i></div>
                                                        <span>Pending</span>
                                                    </li>
                                                    <li class="step @if(in_array($order->order_status_id, [2, 3])) active @endif">
                                                        <div><i class="ri-check-line"></i></div>
                                                        <span>Confirm</span>
                                                    </li>
                                                    <li class="step @if($order->order_status_id == 4) active @endif">
                                                        <div><i class="ri-truck-line"></i></div>
                                                        <span>On the way</span>
                                                    </li>
                                                    <li class="step @if($order->order_status_id == 7) active @endif">
                                                        <div><i class="ri-gift-2-line"></i></div>
                                                        <span>Completed</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                        <h5 class="text-center">
                                            <b>The order has been Cancelled</b>
                                        </h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- my account section end -->



@endsection





