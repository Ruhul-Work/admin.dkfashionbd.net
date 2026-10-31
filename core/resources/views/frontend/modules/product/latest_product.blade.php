<div class="swiper-wrapper">

    @forelse($latestProducts as $product)
        <div class="swiper-slide swiper-custom-padding">


            @include('frontend.modules.product.product')





        </div>

    @empty

        <div class="container mt-5">
            <div class="card card-body d-flex justify-content-center align-items-center" style="height: 200px">
                <h5 class="card-title">No product available</h5>
            </div>
        </div>

    @endforelse


</div>
<div class="swiper__nav--btn swiper-button-next"></div>
<div class="swiper__nav--btn swiper-button-prev"></div>

