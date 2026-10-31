
@extends('frontend.layouts.master')

@section('meta')
    <title>Log In | {{ get_option('title') }}</title>
@endsection
@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title  mb-25"> লগইন</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class=""> লগইন</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- Start login section  -->
    <div class="login__section section--padding">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form id="loginForm" method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="login__section--inner">

                                    <div class="account__login">
                                        <div class="account__login--header mb-25 text-center">
                                            <img src="{{ asset('theme/frontend/assets/img/icon/login.png') }}" alt="icon">
                                            <h2 class="account__login--header__title mb-10 mt-3">লগইন</h2>
                                        </div>
                                        <div class="account__login--inner">
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if(session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label for="identifier">মোবাইল নম্বর অথবা ইমেইল ঠিকানা:</label>
                                                <input id="identifier" name="identifier" class="account__login--input" placeholder="ইমেইল বা ফোন" type="text" value="{{ old('identifier') }}">
                                                <span id="identifierError" class="error-message" style="display: none;"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="password"><i class="ri-lock-2-line"></i> পাসওয়ার্ড:</label>
                                                <input id="password" name="password" class="account__login--input" placeholder="পাসওয়ার্ড" type="password" value="{{ old('password') }}">
                                                <span id="passwordError" class="error-message" style="display: none;"></span>
                                            </div>
                                            <div class="account__login--remember__forgot mb-15 d-flex justify-content-between align-items-center">
                                                <div class="account__login--remember position__relative">
                                                    <input class="checkout__checkbox--input" id="check1" name="remember" type="checkbox">
                                                    <span class="checkout__checkbox--checkmark"></span>
                                                    <label class="checkout__checkbox--label login__remember--label" for="check1">
                                                        আমাকে মনে রাখুন</label>
                                                </div>
                                           <button class="account__login--forgot" type="button" onclick="window.location='{{ route('password.request') }}'">আপনার পাসওয়ার্ড ভুলে গেছেন?</button>
                                            </div>
                                            <button class="account__login--btn primary__btn" type="submit">লগইন</button>
                                            <div class="account__login--divide">
                                                <span class="account__login--divide__text">অথবা</span>
                                            </div>
                                            <p class="account__login--signup__text">একটি অ্যাকাউন্ট নেই? <a href="{{ route('register') }}">এখনই রেজিস্টার করুন</a></p>
                                        </div>
                                    </div>

                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <!-- End login section  -->




@endsection
@section('scripts')
    <style>
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>

    <script>


        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                validateForm();
            });
        });

        function validateForm() {
            // পূর্বের ত্রুটি বার্তাগুলি সাফ করুন
            $('.error-message').text('').hide();

            const identifier = $('#identifier').val().trim();
            const password = $('#password').val().trim();
            let isValid = true;

            // পরিচয় যাচাই
            if (!identifier) {
                $('#identifierError').text('মোবাইল নম্বর বা ইমেইল প্রয়োজন।').show();
                isValid = false;
            }

            // পাসওয়ার্ড যাচাই
            if (!password) {
                $('#passwordError').text('পাসওয়ার্ড প্রয়োজন।').show();
                isValid = false;
            }

            // যদি সমস্ত যাচাই পাস করে, ফর্ম জমা দিন
            if (isValid) {
                $('#loginForm').unbind('submit').submit();
            }
        }


    </script>
@endsection







