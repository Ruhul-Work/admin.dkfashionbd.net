
@extends('frontend.layouts.master')
@section('meta')
    <title>Order Success | {{ get_option('title') }}</title>
@endsection

@section('content')


    <!-- my account section start -->

    <section class="my__account--section section--padding">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <div class="confirmation-page">
                        <div class="icon">
                            <img src="{{asset('theme/frontend/assets/img/gif/success.gif')}}" alt="img">
                        </div>
                        <h1>অভিনন্দন!</h1>
                        <h2> অর্ডার সফলভাবে সম্পন্ন হয়েছে</h2>
                        <div class="order-number">
                            আপনার অর্ডার নম্বর হল <span>{{$order->order_number}}</span>
                        </div>
                        
                        <div >
                            
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 1px dashed #ddd;">

        <thead>
            <tr style="border-top: 1px dashed #ddd; border-bottom: 1px dashed #ddd;">
                <th style="padding: 8px; text-align: left;">#Item</th>
                <th style="padding: 8px; text-align: left;">Price(৳) </th>
                <th style="padding: 8px; text-align: left;">Qty</th>
                <th style="padding: 8px; text-align: right;">Total(৳) </th>
            </tr>

            </thead>
            <tbody>



@foreach ($order->orderItems as $item)
    <tr>
        <td style="padding: 8px;">{{ $loop->iteration }}. {{ $item->product->bangla_name }}</td>
        <td style="padding: 8px;">{{ $item->price }}</td>
        <td style="padding: 8px;">{{ $item->qty }}</td>
        <td style="padding: 8px; text-align: right;">{{ $item->price * $item->qty }}</td>
    </tr>
    @if($item->product->isBundle == 1)
        <!-- Display the bundle products -->
        @foreach($item->product->bundleProducts as $bundleProduct)
            <tr>
                <td style="padding: 8px;">&nbsp;&nbsp;{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $bundleProduct->name }} <span style="font-weight: bold">&nbsp;x&nbsp;{{$item->qty*$bundleProduct->quantity}}copy</span></td>
            </tr>
        @endforeach
    @endif
@endforeach

<tr style="border-top: 1px dashed #ddd;">
    {{-- <td> </td> --}}
    <td colspan="4">
        <div style="text-align:right;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px;">Item Total:</td>
                    <td style="padding: 8px; text-align: right;">{{ formatPrice($order->subtotal) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">Discount:</td>
                    <td style="padding: 8px; text-align: right;">-{{ formatPrice($order->discount, 2) }}</td>
                </tr>
                @if($order->adjust_amount)
                    <tr>
                        <td style="padding: 8px;">Adjust Amount:</td>
                        <td style="padding: 8px; text-align: right;">{{ formatPrice($order->adjust_amount) }}</td>
                    </tr>
                @endif
                @if($order->packing_charge>0)
                    <tr>
                        <td style="padding: 8px;">Wrapping:</td>
                        <td style="padding: 8px; text-align: right;">{{ formatPrice($order->packing_charge) }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 8px;">Shipping :</td>
                    <td style="padding: 8px; text-align: right;">{{ formatPrice($order->shipping_charge, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">Tax:</td>
                    <td style="padding: 8px; text-align: right;">{{ formatPrice($order->tax, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Total Bill :</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong>{{ formatPrice($order->total, 2) }}</strong></td>
                </tr>


                @if($order->transactions->sum('amount')>0)
                <tr>
                    <td style="padding: 8px;">Paid :</td>
                    <td style="padding: 8px; text-align: right;">{{formatPrice($order->transactions->sum('amount'))}}</td>
                </tr>

                <tr>
                    <td style="padding: 8px;"><strong>Due :</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong>{{formatPrice($order->total- $order->transactions->sum('amount'))}}</strong></td>
                </tr>
                @endif

            </table>
        </div>
    </td>
</tr>


            <tr>
                <td colspan="4" style="padding: 8px;"><strong>Total in Words:</strong> {{ numberToWords($order->total) }}</td>
            </tr>
            </tbody>
        </table>
                        
                           </div>
                        
                        
          
                        
                        
                        
                        @if(!empty($order->shipping->email))
                            <div class="email-confirmation">
                                শীঘ্রই   নিচের ইমেইল ঠিকানায় অর্ডার নিশ্চিতকরণ ইমেল পাবেন
                                <a href="javascript:void(0)">{{$order->shipping->email}}</a>
                                <br><br>
                                আপনার ইমেইল ইনবক্স খুলতে নিচের লিংকগুলোর যেকোনো একটি ব্যবহার করুন:
                                <ul>
                                    <li><a href="https://mail.google.com/" target="_blank">Gmail</a></li>
                                    <li><a href="https://mail.yahoo.com/" target="_blank">Yahoo Mail</a></li>
                                    <li><a href="https://outlook.live.com/" target="_blank">Outlook</a></li>
                                    <li><a href="https://mail.aol.com/" target="_blank">AOL Mail</a></li>
                                </ul>
                            </div>
                        @endif
{{--                        <div class="payment-method">--}}
{{--                            পেমেন্ট পদ্ধতিঃ বিকাশ--}}
{{--                        </div>--}}
                        <div class="d-flex justify-content-around">
                            @auth
                            <a href="{{route('dashboard')}}" class="primary__btn">আমার অ্যাকাউন্ট</a>
                            @else
                              <a href="{{route('home')}}" class="primary__btn">হোম</a>
                              @endauth
                            <a href="{{route('home')}}" class="table__btn">কেনাকাটা চালিয়ে যান</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- my account section end -->





@endsection
@section('scripts')


    <style>
        .confirmation-page {

            text-align: center;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .confirmation-page .icon {
            font-size: 50px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .confirmation-page h1 {
            font-size: 40px;
            color: red;
            margin-bottom: 20px;
        }
        .confirmation-page h2 {
            font-size: 30px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .confirmation-page .order-number {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .confirmation-page .order-number span {
            color: red;
        }

        .order-number{
            font-size: 20px;
            font-weight: 600;
        }

        .confirmation-page .email-confirmation {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .confirmation-page .order-total,
        .confirmation-page .payment-method {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }


    </style>




@endsection


