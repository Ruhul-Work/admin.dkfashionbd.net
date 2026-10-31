    <footer class="footer__section bg__secondary">
    <div class="container-fluid">
        <div class="main__footer d-flex justify-content-between">
            <div class="footer__widget footer__widget--width">
{{--                <h2 class="footer__widget--title text-gray h3">আমাদের সম্পর্কে--}}
{{--                    <button class="footer__widget--button" aria-label="footer widget button">--}}
{{--                        <svg class="footer__widget--title__arrowdown--icon" xmlns="http://www.w3.org/2000/svg"--}}
{{--                             width="12.355" height="8.394" viewBox="0 0 10.355 6.394">--}}
{{--                            <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z"--}}
{{--                                  transform="translate(-6 -8.59)" fill="currentColor"></path>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                </h2>--}}
                <div class="footer-logo py-3">
                      @php
                     $logo = get_option('logo');
                    @endphp
                    <img src="{{ asset($logo) }}" alt="logo">
                </div>
                <div class="footer__widget--inner">
                    <p class="footer__widget--desc text-gray mb-20">
                        স্বপ্ন পূরনের পথে আমরা তোমরা
                        </p>
                    <h4>ট্রেড লাইসেন্স নং-০২ | বি-১৩০২</h4>
                </div>
                <div class="footer__instagram--list d-flex align-items-center py-4">
                    <div class="instagram__thumbnail">
                        <h4 class="text-gray">ফলো করুন:</h4>
                    </div>
                        <div class="instagram__thumbnail">
                            <a class="instagram__thumbnail--img" target="_blank"
                               href="{{get_option('facebook')}}"><img
                                    src="{{asset('theme/frontend/assets/img/icon/facebook.png')}}" alt="Facebook Page"></a>
                        </div>
                        <div class="instagram__thumbnail">
                            <a class="instagram__thumbnail--img" target="_blank"
                               href="{{get_option('youtube')}}"><img
                                    src="{{asset('theme/frontend/assets/img/icon/youtube.png')}}" alt="Youtube Channel"></a>
                        </div>

                </div>
            </div>
            <div class="footer__widget--menu__wrapper d-flex footer__widget--width">
                <div class="footer__widget">
                    <h2 class="footer__widget--title text-gray h3">বিশেষ ফিচার
                        <button class="footer__widget--button" aria-label="footer widget button">
                            <svg class="footer__widget--title__arrowdown--icon" xmlns="http://www.w3.org/2000/svg"
                                 width="12.355" height="8.394" viewBox="0 0 10.355 6.394">
                                <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z"
                                      transform="translate(-6 -8.59)" fill="currentColor"></path>
                            </svg>
                        </button>
                    </h2>
                    <ul class="footer__widget--menu footer__widget--inner">
                        @auth
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('profile.show')}}">প্রোফাইল

                            </a>
                        </li>

                            <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                      href="{{route('logout')}}">লগ আউট

                                </a></li>

                        @else


                            <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                      href="{{route('login')}}">লগইন</a></li>

                            <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                      href="{{route('register')}}">রেজিস্টার</a></li>
                        @endauth
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('cart.show')}}">ক্রয় তালিকা</a></li>


                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('wishlist.index')}}">উইশ লিস্ট</a></li>
                                                                  
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text" target="_blank"
                                                                  href="https://www.englishmojabd.com/blogs">ব্লগ</a></li>
                    </ul>
                </div>
                <div class="footer__widget">
                    <h2 class="footer__widget--title text-gray h3">প্রয়োজনীয় লিংক
                        <button class="footer__widget--button" aria-label="footer widget button">
                            <svg class="footer__widget--title__arrowdown--icon" xmlns="http://www.w3.org/2000/svg"
                                 width="12.355" height="8.394" viewBox="0 0 10.355 6.394">
                                <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z"
                                      transform="translate(-6 -8.59)" fill="currentColor"></path>
                            </svg>
                        </button>
                    </h2>
                    <ul class="footer__widget--menu footer__widget--inner">
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('about')}}">আমাদের সম্পর্কে</a></li>
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('contact')}}">যোগাযোগ করুন</a></li>
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('terms')}}">শর্তাবলী</a></li>
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('privacy-policy')}}">গোপনীয়তা নীতি</a></li>
                        <li class="footer__widget--menu__list"><a class="footer__widget--menu__text"
                                                                  href="{{route('campaign.all')}}">অফার</a></li>

                       
                    </ul>
                </div>
            </div>
             <div class="footer__widget footer__widget--width">
                    <h2 class="footer__widget--title text-gray h3">পার্টনারস
                        
                        <button class="footer__widget--button" aria-label="footer widget button">
                            <svg class="footer__widget--title__arrowdown--icon" xmlns="http://www.w3.org/2000/svg"
                                 width="12.355" height="8.394" viewBox="0 0 10.355 6.394">
                                <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z"
                                      transform="translate(-6 -8.59)" fill="currentColor"></path>
                            </svg>
                        </button>
                    </h2>
                        
                    <ul class="footer__widget--menu footer__widget--inner">
                        <li class="footer-partner-info">
                            <p class="pb-0 mb-3">কুরিয়ার পার্টনার:</p>
                            <img src="{{asset('theme/frontend/assets/img/icon/sb.jpg')}}" alt="">
                            <span>সুন্দরবন কুরিয়ার </span>
                        </li>
                       
                        <li class="footer-partner-info mt-3">
                            <p class="pb-0 mb-3">পেমেন্ট গেটওয়ে:</p>
                            <img src="{{asset('theme/frontend/assets/img/icon/bkash.png')}}" alt="">
                            <span>বিকাশ</span>
                        </li>
                       
                        <li class="footer-partner-info mt-3">

                            <p class="pb-0 mb-3">ক্যাশ অন ডেলিভারি পার্টনার:</p>
                            <img src="{{asset('theme/frontend/assets/img/icon/std.jpg')}}" alt="">
                          <span>স্টেডফাস্ট</span>
                        </li>
                    </ul>
                </div>
                
                <style>
                    .footer-partner-info img{
                        height: 30px;
                              }
                </style>
            <div class="footer__widget footer__widget--width">
                <h2 class="footer__widget--title text-gray h3">যোগাযোগ
                    <button class="footer__widget--button" aria-label="footer widget button">
                        <svg class="footer__widget--title__arrowdown--icon" xmlns="http://www.w3.org/2000/svg"
                             width="12.355" height="8.394" viewBox="0 0 10.355 6.394">
                            <path d="M15.138,8.59l-3.961,3.952L7.217,8.59,6,9.807l5.178,5.178,5.178-5.178Z"
                                  transform="translate(-6 -8.59)" fill="currentColor"></path>
                        </svg>
                    </button>
                </h2>

                <div class="address">
                    <span>{{get_option('address')}}</span> <br>
                    <span>E-mail: {{get_option('email')}} <br>Telephone: {{get_option('phone_number')}} </span>
                </div>

                <div class="footer__widget--inner">
                    <p class="footer__widget--desc text-gray m-0">আমাদের সাময়িক অফার এবং আপডেট পেতে সাবস্ক্রাইব করুন</p>
                    <div class="newsletter__subscribe">
                        <form class="newsletter__subscribe--form" id="subscribeForm"  action="{{ route('subscribe.store') }}" method="POST">
                            @csrf

                            <label class="border d-flex justify-content-between">
                                <input class="newsletter__subscribe--input" name="email" id="email" placeholder="ইমেইল ঠিকানা" type="email" required>
                                <button class="newsletter__subscribe--button" type="submit">সাবস্ক্রাইব</button>
                            </label>
                        </form>


                    </div>
                </div>
            </div>
        </div>
       
        <div class="footer__bottom d-flex justify-content-between align-items-center">
            <p class="copyright__content text-gray m-0">
                কপিরাইট © ২০২৪ <a class="copyright__content--link fw-bold text-success" href="{{route('home')}}"> {{get_option('company_name')}}</a> . সকল অধিকার সংরক্ষিত
            </p>
            <p class="copyright__content text-gray m-0">
                ডেভেলপড বাই <a class="fw-bold text-danger" href="{{get_option('dev_url')}}" target="_blank">{{get_option('dev')}}</a>
            </p>

            
        </div>

    </div>
</footer>



<!-- End footer section -->




{{--modal search--}}

    <div class="modal" id="search-modal" data-animation="slideInUp">
        <div class="modal-dialog quickview__main--wrapper">
            <header class="modal-header quickview__header">
                <button class="close-modal quickview__close--btn " aria-label="close modal" data-close>✕ </button>
            </header>
            <div class="quickview__inner">
                <div class="row ">
                    <div class="col-md-12 mb-20" style="height: 400px;">
                        <h3 class=" py-3">আপনার পছন্দসই পণ্য অনুসন্ধান করুন</h3>


                        @include('frontend.modules.search.modal_search_form')


                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <div class="row mt-5">
{{--                    @foreach($banners as $banner)--}}
{{--                        <div class="col-lg-3 col-md-6 mb-3">--}}
{{--                            <div class="banner__items position__relative">--}}
{{--                                <a class="banner__items--thumbnail" href="shop.html">--}}
{{--                                    <img class="banner__items--thumbnail__img banner__img--max__height"--}}
{{--                                         src="{{ image($banner->image) }}" alt="banner-img">--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
                </div>
            </div>

        </div>
    </div>









    <!-- Scroll top bar -->


{{-- custom alert--}}
    <div class="position-fixed top-0 start-0 p-3" style="z-index: 1111">
        <div id="toastContainer" class="toast-container">
            <!-- Toasts will be dynamically added here -->
        </div>
    </div>



<button id="scroll__top"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48"
              d="M112 244l144-144 144 144M256 120v292" />
    </svg></button>


