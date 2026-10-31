
@extends('frontend.layouts.master')

@section('meta')
    <title>Send Password Reset Link | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title  mb-25">রিসেট লিঙ্ক পাঠান</h1>
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
                    <form id="passwordForm" method="post" action="{{ route('password.email') }}">
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

                                    <div class="form-group">
                                        <label for="email"> ইমেইল ঠিকানা:</label>
                                        <input id="email" type="email" name="email" class="account__login--input" placeholder="ইমেইল" value="{{ old('email') }}">
                                        <span id="emailError" class="error-message" style="display: none;"></span>
                                    </div>


                                    <button class="account__login--btn primary__btn" type="submit">পাসওয়ার্ড রিসেট লিঙ্ক পাঠান</button>

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
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                validateForm();
            });
        });

        function validateForm() {
            $('.error-message').text('').hide();

            const email = $('#email').val().trim();

            let isValid = true;

            if (!email) {
                $('#emailError').text('ইমেইল প্রয়োজন।').show();
                isValid = false;
            }

            if (isValid) {
                $('#passwordForm').unbind('submit').submit();
            }
        }

    </script>
@endsection







