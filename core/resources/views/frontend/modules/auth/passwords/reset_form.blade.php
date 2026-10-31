
@extends('frontend.layouts.master')

@section('meta')
    <title>Reset Password | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title  mb-25"> পাসওয়ার্ড রিসেট</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class=""> পাসওয়ার্ড রিসেট</span></li>
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
                    <form id="resetForm" method="post" action="{{ route('password.update') }}">
                        @csrf
                        <div class="login__section--inner">
                            <div class="account__login">
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

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group">
                                        <label for="email">ইমেইল ঠিকানা:</label>
                                        <input id="email" type="email" name="email" class="account__login--input" placeholder="ইমেইল" value="{{ old('email', $email) }}">
                                        <span id="emailError" class="error-message" style="display: none;"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="password">পাসওয়ার্ড:</label>
                                        <input id="password" type="password" name="password" class="account__login--input" required placeholder="নতুন পাসওয়ার্ড">
                                        <span id="passwordError" class="error-message" style="display: none;"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation">পাসওয়ার্ড নিশ্চিত:</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation" class="account__login--input" required placeholder="পাসওয়ার্ড নিশ্চিত করুন">
                                        <span id="confirmPasswordError" class="error-message" style="display: none;"></span>
                                    </div>

                                    <button class="account__login--btn primary__btn" type="submit">পাসওয়ার্ড রিসেট করুন</button>
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
            $('#resetForm').on('submit', function(e) {
                e.preventDefault();
                validateForm();
            });
        });

        function validateForm() {
            $('.error-message').text('').hide();

            const email = $('#email').val().trim();
            const password = $('#password').val().trim();
            const confirmPassword = $('#password_confirmation').val().trim();

            let isValid = true;

            if (!email) {
                $('#emailError').text('ইমেইল প্রয়োজন।').show();
                isValid = false;
            }

            if (!password) {
                $('#passwordError').text('পাসওয়ার্ড প্রয়োজন।').show();
                isValid = false;
            }

            if (password && password.length < 8) {
                $('#passwordError').text('পাসওয়ার্ড অন্তত ৮ অক্ষর দীর্ঘ হতে হবে।').show();
                isValid = false;
            }

            if (password !== confirmPassword) {
                $('#confirmPasswordError').text('পাসওয়ার্ড নিশ্চিতকরণ মিলছে না।').show();
                isValid = false;
            }

            if (isValid) {
                $('#resetForm').unbind('submit').submit();
            }
        }
    </script>
@endsection








