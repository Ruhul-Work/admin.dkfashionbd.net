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
                                        <h3 class="account__content--title mb-20 ">প্রোফাইল  এডিটিং</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('profile.update') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">নাম <span class="contact__form--label__star">*</span></label>
                                                <input type="text" class="form-control custom-input" id="name" name="name" value="{{ old('name', $user->name) }}">
                                                @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">ফোন <span class="contact__form--label__star">*</span></label>
                                                <input type="text" class="form-control custom-input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                                @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="alternate_phone" class="form-label">বিকল্প ফোন</label>
                                                <input type="text" class="form-control custom-input" id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone', $user->alternate_phone) }}">
                                                @error('alternate_phone')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">ইমেইল<span class="contact__form--label__star">*</span></label>
                                                <input type="email" class="form-control custom-input" id="email" name="email" value="{{ old('email', $user->email) }}">
                                                @error('email')
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
@endsection
