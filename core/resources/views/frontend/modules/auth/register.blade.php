
@extends('frontend.layouts.master')

@section('meta')
    <title>Registration | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title  mb-25"> রেজিস্ট্রেশন</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class=""> রেজিস্ট্রেশন.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <div class="login__section section--padding">
        <div class="container">
            <form id="registerForm" method="post" action="{{ route('register') }}">
                @csrf
                <div class="login__section--inner">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-6">
                            <div class="account__login register">
                                <div class="account__login--header text-center mb-25">
                                    <img src="{{ asset('theme/frontend/assets/img/icon/register.png') }}" alt="register icon">
                                    <h2 class="account__login--header__title h3 mb-10">একাউন্ট তৈরি করুন</h2>
                                    <p class="account__login--header__desc">আপনি যদি নতুন গ্রাহক হন, তবে এখানে নিবন্ধন করুন</p>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="account__login--inner">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <input id="name" class="account__login--input" placeholder="নাম" type="text"
                                                   name="name" value="{{ old('name') }}" autocomplete="name">
                                            <span id="nameError" class="error-message" style="display: none;"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input id="email" class="account__login--input" placeholder="ইমেইল ঠিকানা " type="text"
                                                   name="email" value="{{ old('email') }}" autocomplete="email">
                                            <span id="emailError" class="error-message" style="display: none;"></span>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <input id="phone" class="account__login--input" placeholder="মোবাইল নম্বর" type="text"
                                                   name="phone" value="{{ old('phone') }}" autocomplete="phone">
                                            <span id="phoneError" class="error-message" style="display: none;"></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input id="password" class="account__login--input" placeholder="পাসওয়ার্ড" type="password"
                                               name="password" autocomplete="new-password">
                                        <span id="passwordError" class="error-message" style="display: none;"></span>
                                    </div>
                                    <div class="form-group">
                                        <input id="confirmPassword" class="account__login--input" placeholder="পাসওয়ার্ড নিশ্চিত করুন" type="password"
                                               name="password_confirmation" autocomplete="new-password">
                                        <span id="confirmPasswordError" class="error-message" style="display: none;"></span>
                                    </div>
                                    <div class="account__login--remember position__relative">
                                        <input class="checkout__checkbox--input" id="check2" type="checkbox" name="terms">
                                        <span class="checkout__checkbox--checkmark"></span>
                                        <label class="checkout__checkbox--label login__remember--label" for="check2">
                                            আমি শর্তাবলী পড়েছি এবং সম্মত আছি
                                        </label>
                                        <span id="termsError" class="error-message"></span> <!-- Error message for terms -->
                                    </div>

                                    <button class="account__login--btn primary__btn mb-10 mt-3" type="button" id="registerButton">রেজিস্টার</button>
                                </div>
                            </div>
                            <style>
                                .error-message {
                                    color: red;
                                    font-size: 12px;
                                    margin-top: 5px;
                                }
                            </style>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('scripts')


    <script>
        $(document).ready(function() {
            $('#registerButton').on('click', function() {
                validateForm();
            });
        });

        function validateForm() {
            // Clear previous error messages
            $('.error-message').text('').hide();

            const name = $('#name').val().trim();
            const phone = $('#phone').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val().trim();
            const confirmPassword = $('#confirmPassword').val().trim();
            const termsChecked = $('#check2').is(':checked'); // Check if the checkbox is checked

            let isValid = true;

            // Name validation
            if (!name) {
                $('#nameError').text('নাম আবশ্যক।').show();
                isValid = false;
            }

            // phone number validation
            if (!phone) {
                $('#phoneError').text('মোবাইল নম্বর আবশ্যক।').show();
                isValid = false;
            } else if (phone.length > 15) {
                $('#phoneError').text('মোবাইল নম্বর সর্বাধিক 15 অক্ষর হতে হবে।').show();
                isValid = false;
            } else if (!/^\+?\d{11}$/.test(phone)) {
                $('#phoneError').text('মোবাইল নম্বর 11 ডিজিট হতে হবে।').show();
                isValid = false;
            }

            // Email validation
            if (!email) {
                $('#emailError').text('ইমেইল আবশ্যক।').show();
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                $('#emailError').text('বৈধ ইমেইল ঠিকানা লিখুন।').show();
                isValid = false;
            }

            // Password validation
            if (!password) {
                $('#passwordError').text('পাসওয়ার্ড আবশ্যক।').show();
                isValid = false;
            } else if (password.length < 6) {
                $('#passwordError').text('পাসওয়ার্ড কমপক্ষে ৬ অক্ষর হতে হবে।').show();
                isValid = false;
            }

            // Confirm password validation
            if (password !== confirmPassword) {
                $('#confirmPasswordError').text('পাসওয়ার্ড মেলেনি।').show();
                isValid = false;
            }

            // Terms and conditions validation
            if (!termsChecked) {
                $('#termsError').text('আপনাকে শর্তাবলী মেনে নিতে হবে।').show();
                isValid = false;
            }

            // If all validations pass, you can submit the form
            if (isValid) {
                // alert('ফর্ম সফলভাবে জমা হয়েছে!');
                $('#registerForm').unbind('submit').submit();
            }
        }










    </script>
@endsection
