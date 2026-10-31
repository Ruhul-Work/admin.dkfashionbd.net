@extends('frontend.layouts.master')
@section('meta')
    <title>Privacy & Policy| {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">গোপনীয়তা নীতি</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="text-dark" href="{{ route('home') }}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="">গোপনীয়তা নীতি</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- Start privacy policy section -->
    <div class="privacy__policy--section section--padding">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    <!-- Section 1 -->
                    <div class="privacy__policy--content section_1">
                        <h2 class="privacy__policy--content__title">গোপনীয়তা নীতি</h2>
                        <h3 class="privacy__policy--content__subtitle">আমাদের গোপনীয়তা নীতি</h3>
                        <p class="privacy__policy--content__desc">
                            আমরা আপনার গোপনীয়তা সম্পর্কে অত্যধিক গুরুত্ব দিয়ে বিবেচনা করি। আমরা সংগ্রহ করা তথ্য সম্পর্কে আপনার সম্মতি নিয়ে কাজ করি এবং সে তথ্যের ব্যবহার বর্তমান আইন অনুযায়ী সীমাবদ্ধ করি।
                        </p>
                        <p class="privacy__policy--content__desc">
                            আমাদের গোপনীয়তা নীতি এই সংস্থার ওয়েবসাইট এবং সেবাদানকারী অন্যান্য উপস্থাপন পরিচালনার জন্য প্রযোজ্য হয়। এই নীতির মাধ্যমে আমরা প্রতিশ্রুতি দিই যে আমরা আপনার ব্যক্তিগত তথ্য সংরক্ষণ এবং ব্যবহার করা সম্পর্কে যথাযথ ব্যবস্থা গ্রহণ করব।
                        </p>
                    </div>

                    <!-- Section 2 -->
                    <div class="privacy__policy--content section_2">
                        <h3 class="privacy__policy--content__subtitle">তথ্য সংগ্রহ</h3>
                        <p class="privacy__policy--content__desc">
                            আমরা শুধুমাত্র আপনার অনুমতির সাথে প্রয়োজনীয় তথ্য সংগ্রহ করি, যা আমাদের সাইটের ব্যবহার সম্পর্কে বিস্তারিত জানাতে সাহায্য করে। এই তথ্য আপনার সুরক্ষিত থাকবে এবং কোনো অন্য উদ্দেশ্যে ব্যবহার করা হবে না।
                        </p>
                        <p class="privacy__policy--content__desc">
                            আপনি যদি আমাদের কাছে কোনো ধরণের ব্যক্তিগত তথ্য প্রদান করেন যেমন নাম, ঠিকানা, ইমেল ঠিকানা, ফোন নম্বর, অর্থাৎ আপনি কোনও অর্ডার দেন বা একাউন্ট তৈরি করেন, তাহলে আমরা এই তথ্য সংরক্ষণ এবং ব্যবহার করব শুধুমাত্র আপনার অনুমতি থাকলে।
                        </p>
                    </div>

                    <!-- Section 3 -->
                    <div class="privacy__policy--content section_3">
                        <h3 class="privacy__policy--content__subtitle">তথ্য ব্যবহার</h3>
                        <p class="privacy__policy--content__desc">
                            আমরা আপনার সংগ্রহকৃত তথ্য ব্যবহার করি শুধুমাত্র আমাদের সাইট এর সেবা সরবরাহ এবং আপনার অভিজ্ঞতা উন্নত করার জন্য। এই তথ্য আপনার সুরক্ষার জন্য পূর্ণভাবে সংরক্ষিত থাকবে।
                        </p>
                        <p class="privacy__policy--content__desc">
                            যেহেতু আমরা সংগ্রহ করা তথ্য ব্যবহার করি শুধুমাত্র আপনার সেবা পরিচালনার জন্য, তাই আমরা অন্য কোনো ধরণের ব্যবহারের জন্য এই তথ্য বিক্রি, অথবা বিনিময় করি না। এই বিষয়ে যদি কোন পরিবর্তন হয়, তাহলে আমরা আপনার অভিজ্ঞতা প্রদান করার আগে অবশ্যই আপনার অনুমতি চাইব।
                        </p>
                    </div>

                    <!-- Section 4 -->
                    <div class="privacy__policy--content section_4">
                        <h2 class="privacy__policy--content__title">আমাদের গোপনীয়তা নীতির অংশ</h2>
                        <p class="privacy__policy--content__desc">
                            আমরা সর্বদা আপনার তথ্যের সুরক্ষার জন্য সম্মান সাধা দিই এবং সে তথ্য সুরক্ষিত রাখতে প্রতিশ্রুতিবদ্ধ থাকি। আপনি যেকোনো সময়ে আমাদের গোপনীয়তা নীতির বিস্তারিত জানতে সাইটে দেখতে পারেন।
                        </p>
                        <p class="privacy__policy--content__desc">
                            আমাদের গোপনীয়তা নীতি পর্যালোচনা করতে আপনার কোনো প্রশ্ন থাকলে আমাদের সাপোর্ট দলের সাথে যোগাযোগ করুন। আমরা আপনার প্রশ্নগুলির উত্তর দেওয়ার জন্য সম্পূর্ণরূপে প্রস্তুত।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End privacy policy section -->

@endsection


