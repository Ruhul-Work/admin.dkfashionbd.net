<h5>আরো দেখুন...</h5>
@foreach($relatedProducts as $relatedProduct)
    <a href="{{ route('product.details', ['slug_or_id' => $relatedProduct->slug]) }}">
    <div class="book-item py-2">

            <img src="{{ asset($relatedProduct->thumb_image) }}" alt="Book Image">
            <div class="book-info">
                <p class="py-0 mb-0">{{ $relatedProduct->bangla_name }}</p>
                <p class="py-0 mb-0 fs-5">{{ $relatedProduct->authors->pluck('name')->implode(', ') }}</p>
                
                @if (priceAfterDiscount($relatedProduct) < $relatedProduct->mrp_price)
    <del>{{ $relatedProduct->mrp_price }} ৳</del>
    <span class="book-price">{{ priceAfterDiscount($relatedProduct) }}৳</span>
@else
    <span class="book-price">{{ $relatedProduct->mrp_price }}৳</span>
@endif

            </div>

    </div>
    </a>
@endforeach
