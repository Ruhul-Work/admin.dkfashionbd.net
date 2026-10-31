
@if(count($products)>0)


      @forelse($products as $product)
        <a href="{{ route('product.details', ['slug_or_id' => $product->slug ?: $product->id]) }}" class="search-item">
            <img src="{{ image($product->thumb_image) }}" alt="{{ $product->english_name }}">
            <div class="details">
                <h3>{{ $product->bangla_name }}</h3>
                  <p>মূল্য: {{ formatPrice(priceAfterDiscount($product)) }}</p>
            </div>
        </a>
        @endforeach


        @if ( count($products) < $totalProducts)
            <div class="text-center p-3">
                <a href="{{ route('search.single', ['productType'=>$productType ,'search' =>$searchTerm]) }}" class="primary__btn  w-100 fw-bold fs-4"> সার্চ  সম্পর্কিত সব পণ্য দেখুন</a>
            </div>
        @endif


        @else
            <div class="text-center p-3">
                <p class="text-danger">কোনো পণ্য পাওয়া যায়নি</p>
            </div>

        @endif

