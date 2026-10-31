
@extends('frontend.layouts.master')
@section('meta')
    <title>Checkout | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start checkout page area -->
    <div class="checkout__page--area section--padding">
        <div class="container">
            <div class="row">

                <div class="col-md-7">
                    
                    @if ($errors->any())
     @foreach ($errors->all() as $error)
         <div>{{$error}}</div>
     @endforeach
 @endif

                    <form id="checkout-form" action="{{route('checkout.store.order')}}"  method="POST" class="checkout-form">

                        @csrf

                        <!-- Hidden input fields for coupon amount and shipping charge -->
                            <input type="hidden" id="hiddenCouponAmount" name="couponAmount">
                            <input type="hidden" id="hiddenShippingCharge" name="shippingCharge">
                            <input type="hidden" id="hiddenPaymentMethod" name="paymentMethod">



                        <div class="checkout__content--step section__shipping--address">
                            <div class="section__header mb-25">
                                <h3 class="section__header--title">ডেলিভারির ঠিকানা (অনুগ্রহপূর্বক আপনার প্রয়োজনীয় তথ্য দিন)</h3>
                            </div>
                            <hr>
                            <div class="d-lg-flex justify-content-arround py-3">
                                <div class="recieve-area">পার্সেল গ্রহণ করতে চান যেখান থেকে?</div>
                                <div class="d-flex justify-content-center">
                                    <div class="form-check px-3 ">
                                        <input class="form-check-input " type="radio" name="location_type" id="home" value="home" checked>
                                        <label class="form-check-label" for="home">বাসা</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="office" value="office">
                                        <label class="form-check-label" for="office">অফিস</label>
                                    </div>
                                </div>
                            </div>
                            <div class="section__shipping--address__content">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="name"> নাম <span class="contact__form--label__star">*</span></label>
                                            <input class="checkout__input--field border-radius-5" name="name" id="name" placeholder="আপনার নাম লিখুন" type="text" value="{{ old('name') }}">
                                            <div class="error" id="name-error">
                                                @error('name')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="phone"> ফোন <span class="contact__form--label__star">*</span></label>
                                            <input class="checkout__input--field border-radius-5" name="phone" id="phone" placeholder="আপনার ফোন নাম্বার লিখুন" type="text" value="{{ old('phone') }}">
                                            <div class="error" id="phone-error">
                                                @error('phone')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="alternate_phone"> জরুরি ফোন (ঐচ্ছিক)</label>
                                            <input class="checkout__input--field border-radius-5" name="alternate_phone" id="alternate_phone" placeholder="জরুরি ফোন নাম্বার লিখুন" type="text" value="{{ old('alternate_phone') }}">
                                            <div class="error" id="alternate_phone-error">
                                                @error('alternate_phone')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="email"> ইমেইল <span class="contact__form--label__star">*</span></label>
                                            <input class="checkout__input--field border-radius-5" name="email" id="email" placeholder="আপনার ইমেইল লিখুন" type="email" value="{{ old('email') }}">
                                            <div class="error" id="email-error">
                                                @error('email')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>





                                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                                        <label for="division">বিভাগ <span class="contact__form--label__star">*</span></label>
                                        <select id="division" name="division_id" class="checkout__select--field border-radius-5 ">
                                            <option value="0" disabled selected>সিলেক্ট করুন</option>
                                            <!-- Populate divisions dynamically -->
                                        </select>
                                        <div class="error" id="division-error"></div>

                                        @error('division_id')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 col-md-12 col-sm-12 mb-12">
                                        <label for="district">জেলা <span class="contact__form--label__star">*</span></label>
                                        <select id="district" name="city_id" class="checkout__select--field border-radius-5" disabled>
                                            <option value="0" disabled selected>সিলেক্ট করুন</option>
                                        </select>
                                        <div class="error" id="district-error"></div>
                                        @error('city_id')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 col-md-12 col-sm-12 mb-12">
                                        <label for="upazila">থানা/উপজেলা <span class="contact__form--label__star">*</span></label>
                                        <select id="upazila" name="upazila_id" class="checkout__select--field border-radius-5" disabled>
                                            <option value="0" disabled selected>সিলেক্ট করুন</option>
                                        </select>
                                        <div class="error" id="upazila-error"></div>

                                        @error('upazila_id')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                                        <label for="union">ইউনিয়ন (ঐচ্ছিক)</label>
                                        <select id="union" name="union_id" class="checkout__select--field border-radius-5" disabled>
                                            <option value="0" disabled selected>সিলেক্ট করুন</option>
                                        </select>
                                        
                                         @error('union_id')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                    </div>

                                    <div class="col-lg-12 col-sm-12 col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="address">ঠিকানা <span class="contact__form--label__star">*</span></label>
                                            <textarea class="checkout__input--field textarea_height border-radius-5" name="address" id="address" placeholder="বাসা/ফ্ল্যাট নম্বর, পাড়া-মহল্লার নাম, পরিচিতির এলাকা উল্লেখ করুন">{{ old('address') }}</textarea>
                                            <div class="error" id="address-error">
                                                @error('address')
                                                <span>{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-12">
                                        <div class="checkout__input--list">
                                            <label for="customer_note">নোট (যদি থাকে)</label>
                                            <textarea class="checkout__input--field textarea_height border-radius-5" name="customer_note" id="customer_note" placeholder="আপনার মন্তব্য লিখুন">{{ old('customer_note') }}</textarea>
                                                @error('customer_note')
                                                <span class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>


                                <div class="checkout__checkbox py-3">
                                    <input class="checkout__checkbox--input" id="save-info" name="saveInfo" type="checkbox">
                                    <span class="checkout__checkbox--checkmark"></span>
                                    <label class="checkout__checkbox--label" for="save-info">
                                        এই তথ্য পরবর্তী সময়ে ব্যবহারের জন্য সংরক্ষণ করুন
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-5">
                    <div class="checkout__sidebar sidebar">
                        <div class="card subtotal-sideArea">
                            <div class="d-flex justify-content-between">
                                <h5 class="fw-bold">পণ্য</h5>
                                <h5 class="fw-bold">সাবটোটাল</h5>
                            </div>
                            <hr style="border: 1px solid #bdbdbd">
                            <div class="card-body">
                                <div class="cart__table checkout__product--table">
                                    <table class="cart__table--inner">
                                        <tbody class="cart__table--body">
                                        @foreach($cartItems as $item)
                                            <tr class="cart__table--body__items">
                                                <td class="cart__table--body__list">
                                                    <div class="product__image two d-flex  align-items-center">
                                                        <div class="product__thumbnail border-radius-5">
                                                            <a href="{{ route('product.details',['slug_or_id' => $item['slug'] ?? $item['id']]) }}">
                                                                <img class="border-radius-5" src="{{ image($item['thumb_image']) }}" alt="cart-product" style="height:70px;min-width:50px;max-width:50px;object-fit:cover">
                                                            </a>
                                                            <span class="product__thumbnail--quantity">{{ $item['quantity'] }}</span>
                                                        </div>
                                                        <div class="product__description">
                                                            <h3 class="product__description--name h4">
                                                                <a href="{{ route('product.details', ['slug_or_id' => $item['slug'] ?? $item['id']]) }}">
                                                                    {{ $item['bangla_name'] }}
                                                                </a>
                                                            </h3>
                                                            @if(!empty($item['authors']))
                                                                <p class="text-red-english-moja">
                                                                   
                                                                   @foreach(collect($item['authors'])->take(2) as $author)
                                                                        {{ $loop->first ? '' : ', ' }}{{ $author['name'] }}
                                                                    @endforeach
                                                                </p>
                                                            @else
                                                                <p class="text-red-english-moja">
                                                                    {{ $item['publisher_name'] }}
                                                                </p>
                                                            @endif


                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cart__table--body__list checkout-product-list">
                                                    <span class="cart__price">{{formatPrice($item['current_price'] * $item['quantity'])}}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>








                                <hr style="border: 1px solid #bdbdbd">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-red-english-moja fw-bold">সাবটোটাল</h5>
                                    <p class="text-red-english-moja fw-bold" id="subtotal">{{formatPrice($subtotal)}}</p>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <h5 class="text-red-english-moja fw-bold">ছাড়</h5>
                                    <p class="text-red-english-moja fw-bold" id="totalDiscountAmount">{{formatPrice($totalDiscountAmount)}}</p>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <h5 class="text-red-english-moja fw-bold">কুপন ছাড়</h5>
                                    <p class="text-red-english-moja fw-bold" id="couponAmount">
                                            ৳  {{ session('couponDiscount', '00') }}

                                    </p>
                                </div>





                                <hr style="border: 1px solid #bdbdbd">

                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="text-red-english-moja fw-bold">শিপিং চার্জ:</h5>
                                    <div class="shipping-label">
                                        <div class="form-check"  id="advanceShippingDiv">
                                            <input class="form-check-input" type="radio" name="shippingOption" id="advancePayment" value="{{get_option('advance_pay')}}" checked>
                                            <label class="form-check-label" for="advancePayment">
                                                অগ্রিম পেমেন্ট: <strong>৳{{get_option('advance_pay')}}</strong>
                                            </label>
                                        </div>
                                       
                                      @if(count($cashPaymentMethods)>=1)
                                        <div class="form-check " id="" >
                                            <input class="form-check-input" type="radio" name="shippingOption" id="cashOnDelivery" value="{{getCartWeightBasedShipping()}}">
                                            <label class="form-check-label" for="cashOnDelivery">
                                                ক্যাশ পেমেন্ট: <strong> ৳{{getCartWeightBasedShipping()}} </strong>
                                            </label>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                                <hr style="border: 1px solid #bdbdbd">
                                @php
                                $activeCouponCount=hasActiveCoupons();
                                @endphp
                                @if ($activeCouponCount)

                                    <div class="checkout__discount--code">
                                        <form id="couponForm" class="d-flex justify-content-between" action="#">

                                            @if(session('couponDiscount'))

                                                <label>
                                                    <input class="checkout__discount--code__input--field border-radius-5" placeholder="কুপন কোড দিন" type="text" name="coupon_code" id="coupon_code" readonly> 
                                                </label>
                                                <button class=" primary__btn border-radius-5 d-none" type="button" id="removeCouponBtn">কুপন সরান</button>
                                            @else

                                                <label>
                                                    <input class="checkout__discount--code__input--field border-radius-5" placeholder="কুপন কোড দিন" type="text" name="coupon_code" id="coupon_code">
                                                </label>
                                                <button class="checkout__discount--code__btn primary__btn border-radius-5" type="submit" id="applyCouponBtn">প্রয়োগ করুন</button>

                                                <button class="primary__btn border-radius-5 d-none" type="button" id="removeCouponBtn">কুপন সরান</button>
                                            @endif
                                        </form>
                                    </div>

                                @endif
                                 <h5 class="mb-3 adv_pay d-none text-success">{{get_option('payment_text')}}</h5>
                                 <hr style="border: 1px solid #bdbdbd">
                           

                                <div class="d-flex justify-content-between">
                                    <h5 class="text-red-english-moja fs-3 fw-bold">সর্বমোট:</h5>
                                    <p class="text-red-english-moja fs-3 fw-bold" id="grandTotal"> </p>
                                </div>

                                <hr style="border: 1px solid #bdbdbd">



                                <div class="payment-methods" id="paymentMethods">

                                    <h5 class="mb-3">পেমেন্ট মেথড</h5>
                                    <div class="error " id="payment-method-error">

                                    </div>

                                    @foreach($cashPaymentMethods as $cashPaymentMethod)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check payment-check d-flex align-items-center" id="codPaymentOptions">
                                          <input class="form-check-input" type="radio" name="paymentMethod" id="{{$cashPaymentMethod->id}}" value="{{$cashPaymentMethod->id}}" >
                                           <label class="form-check-label" style="white-space:nowrap;"  for="{{$cashPaymentMethod->id}}">{{$cashPaymentMethod->name}}</label>
                                           <img src="{{asset($cashPaymentMethod->icon)}}" alt="Cash On Delivery">
                                        </div>
                                        
                                    </div>
                                    @endforeach


                                    <hr style="border: 1px solid #bdbdbd">
                                    
                                    <div class="d-flex justify-content-between py-3" id="mfsPaymentOptions">



                                        @foreach($mfsPaymentMethods as $mfsPaymentMethod)
                                        <div class="form-check payment-check d-flex align-items-center">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="{{$mfsPaymentMethod->id}}" value="{{$mfsPaymentMethod->id}}" >
                                            <label class="form-check-label" for="{{$mfsPaymentMethod->name}}">


                                                <img src="{{asset($mfsPaymentMethod->icon)}}" alt="{{$mfsPaymentMethod->name}}">
                                            </label>
                                        </div>
                                        @endforeach


                                    </div>


                                </div>
                                <a id="orderButton" class="continue__shipping--btn primary__btn text-center fw-bold border-radius-5 w-100" href="javascript:void(0)">অর্ডার করুন</a>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End checkout page area -->


@endsection
@section('scripts')
    <style>
        .input-error {
            border: 1px solid red;
        }
        .col-lg-6 select.input-error {
            border: 1px solid red;
        }

        .error { color: red; }

        .checkout__select--field {
            width: 100%;
            border: 1px solid var(--border-color2);
            height: 4.5rem;
            padding: 0 1.5rem;
        }

    </style>

    <script>



        $(document).ready(function() {




            function calculateGrandTotal() {
                // Parse values from the DOM
                const subtotal = parseFloat($('#subtotal').text().replace(/,/g, '').replace('৳', '')) || 0;
                // const totalDiscount = parseFloat($('#totalDiscountAmount').text().replace(/,/g, '').replace('৳', '')) || 0;
                const couponAmount = parseFloat($('#couponAmount').text().replace(/,/g, '').replace('৳', '')) || 0;
                const shippingCharge = parseFloat($('input[name="shippingOption"]:checked').val()) || 0;


                // Calculate grand total
                let grandTotal = subtotal - couponAmount + shippingCharge;

                // Update the grand total in the DOM
                $('#grandTotal').text('৳' + grandTotal.toFixed(2));
            }



            // Initial calculation on page load
            calculateGrandTotal();

            // Recalculate grand total when the shipping option changes
            $('input[name="shippingOption"]').on('change', function() {
                calculateGrandTotal();
            });




            @if(session('couponDiscount'))
            $('#applyCouponBtn').addClass('d-none');
            $('#removeCouponBtn').removeClass('d-none');
            @endif

            $('#couponForm').on('submit', function(event) {
                event.preventDefault();

                const couponCode = $('#coupon_code').val();

                $.ajax({
                    url: '{{ route('checkout.apply.coupon') }}',
                    type: 'POST',
                    data: {
                        coupon_code: couponCode,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            var discountAmount = parseFloat(response.couponDiscount);
                            $('#couponAmount').text('৳' + discountAmount.toFixed(2));
                            calculateGrandTotal();
                            showToast('Coupon applied successfully!', 'success');

                            // Hide the apply button and show the remove button
                            $('#applyCouponBtn').addClass('d-none');
                            $('#removeCouponBtn').removeClass('d-none');
                            location.reload();
                        } else {
                            showToast(response.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showToast(xhr.responseJSON.message, 'warning');
                        } else {
                            console.error(xhr.responseText);
                            showToast('An unexpected error occurred.', 'warning');
                        }
                    }
                });
            });

            $('#removeCouponBtn').on('click', function() {
                $.ajax({
                    url: '{{ route('checkout.remove.coupon') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#couponAmount').text('৳0.00');
                            calculateGrandTotal();
                            showToast('Coupon removed successfully!', 'success');

                            // Show the apply button and hide the remove button
                            $('#applyCouponBtn').removeClass('d-none');
                            $('#removeCouponBtn').addClass('d-none');
                            location.reload();
                        } else {
                            showToast(response.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showToast(xhr.responseJSON.message, 'warning');
                        } else {
                            console.error(xhr.responseText);
                            showToast('An unexpected error occurred.', 'warning');
                        }
                    }
                });
            });



            function togglePaymentOptions() {
                const selectedShippingOption = $('input[name="shippingOption"]:checked').attr('id');
                const $codPaymentOptions = $('#codPaymentOptions');
                const $mfPaymentOptions = $('#mfsPaymentOptions');
                const $advPay = $('.adv_pay');

                $codPaymentOptions.removeClass('d-none');
                $mfPaymentOptions.removeClass('d-none');
                $advPay.removeClass('d-none');
                
                if (selectedShippingOption === "advancePayment") {
                    $mfPaymentOptions.removeClass('d-none');
                    $codPaymentOptions.addClass('d-none');
                    $advPay.removeClass('d-none');
                    $('input[name="paymentMethod"]').prop('checked', false);
                } else if (selectedShippingOption === "cashOnDelivery") {
                    $codPaymentOptions.removeClass('d-none');
                    $mfPaymentOptions.addClass('d-none');
                    $advPay.addClass('d-none');
                    $('input[name="paymentMethod"]').prop('checked', false);
                }
            }

            $('input[name="shippingOption"]').on('change', togglePaymentOptions);

            togglePaymentOptions();
        });




        $(document).ready(function() {
            // Handle order button click
            // $('#orderButton').on('click', function() {
            //     if (validateForm()) {
            //         // Set the hidden input values and submit the form
            //         setHiddenInputs();

            //         $('#checkout-form').submit();
            //     }
            // });

            // // Prevent default form submission and validate form
            // $('#checkout-form').on('submit', function(event) {
            //     event.preventDefault();
            //     if (validateForm()) {
            //         this.submit();
            //     }
            // });
            
            
            
            let isSubmitting = false;

            // Handle order button click
            $('#orderButton').on('click', function () {
                if (isSubmitting) return; // Prevent double submission
            
                if (validateForm()) {
                    // Set the hidden input values and submit the form
                    setHiddenInputs();
            
                    isSubmitting = true; // Mark as submitting
                    $('#checkout-form').submit();
                }
            });
            
            // Prevent default form submission and validate form
            $('#checkout-form').on('submit', function (event) {
                if (isSubmitting) return; // Prevent double submission
            
                event.preventDefault(); // Stop default submission
                if (validateForm()) {
                    isSubmitting = true; // Mark as submitting
                    this.submit(); // Submit the form
                }
            });
            
            

            // Function to set hidden input values
            function setHiddenInputs() {
                $('#hiddenShippingCharge').val($('input[name="shippingOption"]:checked').val());
                $('#hiddenPaymentMethod').val($('input[name="paymentMethod"]:checked').val());

            }

            // Form validation function
            function validateForm() {
                let isValid = true;

                // Validate all required fields
                isValid &= validateField('#name', 'আপনার নাম লিখুন');
                isValid &= validatePhone('#phone', 'আপনার ফোন নাম্বার লিখুন', 'সঠিক ফোন নাম্বার লিখুন');
                // isValid &= validateEmail('#email', 'আপনার ইমেইল লিখুন ', 'সঠিক ইমেইল ঠিকানা লিখুন');
                isValid &= validateSelectField('#division', 'অনুগ্রহ করে বিভাগ সিলেক্ট করুন');
                isValid &= validateSelectField('#district', 'অনুগ্রহ করে জেলা সিলেক্ট করুন');
                isValid &= validateSelectField('#upazila', 'অনুগ্রহ করে থানা/উপজেলা সিলেক্ট করুন');
                isValid &= validateField('#address', 'অনুগ্রহ করে আপনার ঠিকানা লিখুন');
                isValid &= validatePaymentMethod();

                return Boolean(isValid);
            }

            // Generic field validation
            function validateField(selector, errorMessage) {
                const value = $(selector).val().trim();
                if (value === '') {
                    showError(selector, errorMessage);
                    return false;
                } else {
                    hideError(selector);
                    return true;
                }
            }

            // Phone validation
            function validatePhone(selector, emptyMessage, invalidMessage) {
                const value = $(selector).val().trim();
                const phoneRegex = /^(?:\+88|88)?(01[3-9]\d{8})$/;
                if (value === '') {
                    showError(selector, emptyMessage);
                    return false;
                } else if (!phoneRegex.test(value)) {
                    showError(selector, invalidMessage);
                    return false;
                } else {
                    hideError(selector);
                    return true;
                }
            }

            // Email validation
            // function validateEmail(selector, emptyMessage, invalidMessage) {
            //     const value = $(selector).val().trim();
            //     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            //     if (value === '') {
            //         showError(selector, emptyMessage);
            //         return false;
            //     } else if (!emailRegex.test(value)) {
            //         showError(selector, invalidMessage);
            //         return false;
            //     } else {
            //         hideError(selector);
            //         return true;
            //     }
            // }

            // Select field validation
            function validateSelectField(selector, errorMessage) {
                const value = $(selector).val();
                if (value === '0' || !value) {
                    showError(selector, errorMessage);
                    return false;
                } else {
                    hideError(selector);
                    return true;
                }
            }



            // // Payment method validation
            function validatePaymentMethod() {
                const selectedPaymentMethod = $('input[name="paymentMethod"]:checked').val();

                if (!selectedPaymentMethod) {
                    showError('#payment-method', 'একটি পেমেন্ট পদ্ধতি নির্বাচন করুন');
                    return false;
                } else {
                    hideError('#payment-method');
                    return true;
                }
            }


            // Show error message
            function showError(selector, message) {
                $(`${selector}-error`).text(message);
                $(selector).addClass('input-error');
            }

            // Hide error message
            function hideError(selector) {
                $(`${selector}-error`).text('');

                $(selector).removeClass('input-error');
            }




            $('#name, #phone, #division, #district, #upazila, #address').on('input change', function() {
                const $input = $(this);
                const errorId = '#' + $input.attr('id') + '-error';

                // Clear error message and input error class
                $(errorId).text('');
                $input.removeClass('input-error');
            });


            // Fetch divisions on page load
            $.ajax({
                url: '{{ route('places.divisions') }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(data) {
                    $.each(data, function(index, division) {
                        $('#division').append(new Option(division.name, division.id));
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Error fetching divisions. Please try again');
                }
            });

            // On division change, fetch districts
            $('#division').change(function() {
                const divisionId = $(this).val();
                resetSelectField('#district');
                resetSelectField('#upazila');
                resetSelectField('#union');

                if (divisionId !== '0') {
                    $('#district').prop('disabled', false);
                    $.ajax({
                        url: "{{ route('places.districts_by_division') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            division_id: divisionId
                        },
                        success: function(data) {
                            $.each(data, function(index, district) {
                                $('#district').append(new Option(district.name, district.id));
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('Error fetching districts. Please try again.');
                        }
                    });
                }
            });

            // On district change, fetch upazilas
            $('#district').change(function() {
                const districtId = $(this).val();
                resetSelectField('#upazila');
                resetSelectField('#union');

                if (districtId !== '0') {
                    $('#upazila').prop('disabled', false);
                    $.ajax({
                        url: '{{ route('places.upazilas') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                           city_id: districtId
                        },
                        success: function(data) {
                            $.each(data, function(index, upazila) {
                                $('#upazila').append(new Option(upazila.name, upazila.id));
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('Error fetching upazilas. Please try again.');
                        }
                    });
                }
            });

            // On upazila change, fetch unions
            $('#upazila').change(function() {
                const upazilaId = $(this).val();
                resetSelectField('#union');

                if (upazilaId !== '0') {
                    $('#union').prop('disabled', false);
                    $.ajax({
                        url: '{{ route('places.unions') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            upazila_id: upazilaId
                        },
                        success: function(data) {
                            $.each(data, function(index, union) {
                                $('#union').append(new Option(union.name, union.id));
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('Error fetching unions. Please try again.');
                        }
                    });
                }
            });

            function resetSelectField(selector) {
                $(selector).prop('disabled', true).empty().append(new Option('সিলেক্ট করুন', '')).prop('selected', true);
            }


        });

        $(document).ready(function() {


            const $saveInfoCheckbox = $('#save-info');




            // Load stored values if they exist
            if (localStorage.getItem('saveInfo') === 'true') {

                $saveInfoCheckbox.prop('checked', true);
                $('input[name="name"]').val(localStorage.getItem('name'));
                $('input[name="phone"]').val(localStorage.getItem('phone'));
                $('input[name="address"]').val(localStorage.getItem('address'));
                $('input[name="email"]').val(localStorage.getItem('email'));
                $('input[name="alternate_phone"]').val(localStorage.getItem('alternate_phone'));
            }

            $saveInfoCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    // Store values in local storage
                    localStorage.setItem('saveInfo', 'true');
                    localStorage.setItem('name', $('input[name="name"]').val());
                    localStorage.setItem('phone', $('input[name="phone"]').val());
                    localStorage.setItem('address', $('input[name="address"]').val());
                    localStorage.setItem('email', $('input[name="email"]').val());
                    localStorage.setItem('alternate_phone', $('input[name="alternate_phone"]').val());
                } else {
                    // Remove values from local storage
                    localStorage.removeItem('saveInfo');
                    localStorage.removeItem('name');
                    localStorage.removeItem('phone');
                    localStorage.removeItem('address');
                    localStorage.removeItem('email');
                    localStorage.removeItem('alternate_phone');
                }
            });
        });



    </script>







@endsection
