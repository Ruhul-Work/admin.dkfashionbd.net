@extends('frontend.layouts.master')
@section('meta')
    <title>User Profile Edit  | {{ get_option('title') }}</title>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="account__content--title mb-20 ">পাসওয়ার্ড পরিবর্তন</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('password.update') }}" method="POST">
                                            @csrf


{{--                                            <div class="mb-3">--}}
{{--                                                <label for="current_password" class="form-label">বর্তমান পাসওয়ার্ড</label>--}}
{{--                                                <input type="password" class="form-control custom-input" id="current_password" name="current_password">--}}
{{--                                                @error('current_password')--}}
{{--                                                <div class="text-danger">{{ $message }}</div>--}}
{{--                                                @enderror--}}
{{--                                            </div>--}}

                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">নতুন পাসওয়ার্ড</label>
                                                <input type="password" class="form-control custom-input" id="new_password" name="new_password">
                                                @error('new_password')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">নতুন পাসওয়ার্ড নিশ্চিত করুন</label>
                                                <input type="password" class="form-control custom-input" id="confirm_password" name="confirm_password">
                                                @error('confirm_password')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg">পরিবর্তন সংরক্ষণ করুন</button>
                                        </form>
                                    </div>
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
        .custom-input {
            border: 1px solid rgba(0, 0, 0, .125); /* Custom border color */
            border-radius: 5px; /* Rounded corners */
            padding: 8px; /* Padding inside the input */
            font-size: 1.5rem;
            transition: border-color 0.3s ease;
        }

        .custom-input:focus {
            border-color:rgba(0, 0, 0, .125);
            box-shadow: 0 0 0 0.2rem rgba(22, 245, 105, 0.25); /* Shadow on focus */
        }



    </style>



    <script>
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                let isValid = true;

                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();

                // Clear previous error messages
                $('.text-danger').remove();

                // // Validate current password
                // if ($('#current_password').val().trim() === '') {
                //     $('#current_password').after('<div class="text-danger">বর্তমান পাসওয়ার্ড প্রয়োজন</div>');
                //     isValid = false;
                // }

                // Validate new password
                if (newPassword.trim() === '') {
                    $('#new_password').after('<div class="text-danger">নতুন পাসওয়ার্ড প্রয়োজন</div>');
                    isValid = false;
                } else if (newPassword.length < 8) {
                    $('#new_password').after('<div class="text-danger">নতুন পাসওয়ার্ড অন্তত ৮ অক্ষরের হতে হবে</div>');
                    isValid = false;
                }

                // Validate confirm password
                if (confirmPassword.trim() === '') {
                    $('#confirm_password').after('<div class="text-danger">নতুন পাসওয়ার্ড নিশ্চিত করতে হবে</div>');
                    isValid = false;
                } else if (newPassword !== confirmPassword) {
                    $('#confirm_password').after('<div class="text-danger">পাসওয়ার্ড মেলেনি</div>');
                    isValid = false;
                }

                // Prevent form submission if validation fails
                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>


@endsection

