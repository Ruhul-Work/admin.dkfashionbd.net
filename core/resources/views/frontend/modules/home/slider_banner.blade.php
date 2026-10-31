{{--<div class="container-fluid">--}}
    <div class="row">


        <div class="col-lg-9 mb-28">
            <!-- Start slider section -->
            <section class="hero__slider--section">
                <div class="hero__slider--inner hero__slider--activation swiper">

                    <div class="hero__slider--wrapper swiper-wrapper ">

                        @if($sliders->isEmpty())

                            <div class="card card-body   text-danger d-flex justify-content-center align-items-center" style="height: 430px;">No sliders available</div>

                        @else
                            @foreach($sliders as $slider)

                        <div class="swiper-slide custom-height">
                            <a href="{{$slider->url}}">
                                <img class="lazy-load" data-loaded="false" data-src="{{image($slider->image)}}"  src="{{ asset('logo/loader.gif') }}"
                                     alt="{{$slider->name}}">
                            </a>
                        </div>

                            @endforeach
                        @endif

                    </div>


                    <div class="swiper__nav--btn swiper-button-next"></div>
                    <div class="swiper__nav--btn swiper-button-prev"></div>
                </div>
            </section>
            <!-- End slider section -->
        </div>
        <div class="col-lg-3 mb-28">
            <div class="row">

                @if($subSliders->isEmpty())

                    <div class="card card-body   text-danger d-flex justify-content-center align-items-center" style="height: 430px;">No sliders available</div>

                @else
                    @foreach($subSliders as $subSlider)
                <div class="col-6 col-lg-12 col-sm-6 mb-28">
                    <!-- Each column takes full width on mobile and half width on medium and larger screens -->
                    <a href="{{$subSlider->url}}" target="_blank">
                        <img class="slider-side-banner lazy-load"
                             data-src="{{asset($subSlider->image)}}" src="{{ asset('theme/frontend/assets/img/default/slider.png') }}"
                             alt="banner  image">
                    </a>
                </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
{{--</div>--}}
