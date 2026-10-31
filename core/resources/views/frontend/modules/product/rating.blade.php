@if($product->reviews->isNotEmpty())
    <div class="product__details--info__rating d-flex align-items-center mb-15">
        <ul class="rating d-flex justify-content-center">
            @php
                $averageRating = calculateAverageRating($product);
                $fullStars = min(5, floor($averageRating));
                $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
            @endphp

            @for ($i = 1; $i <=5; $i++)
                <li class="rating__list">
                <span class="rating__list--icon">
                    @if ($i <= $fullStars)
                        <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="14.105" height="14.732" viewBox="0 0 10.105 9.732">
                            <path data-name="star - Copy" d="M9.837,3.5,6.73,3.039,5.338.179a.335.335,0,0,0-.571,0L3.375,3.039.268,3.5a.3.3,0,0,0-.178.514L2.347,6.242,1.813,9.4a.314.314,0,0,0,.464.316L5.052,8.232,7.827,9.712A.314.314,0,0,0,8.292,9.4L7.758,6.242l2.257-2.231A.3.3,0,0,0,9.837,3.5Z" transform="translate(0 -0.018)" fill="currentColor"/>
                        </svg>
                    @elseif ($i == $fullStars + 1 && $hasHalfStar)
                        <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="14.105" height="14.732" viewBox="0 0 10.105 9.732">
                            <path data-name="star - Copy" d="M9.837,3.5,6.73,3.039,5.338.179a.335.335,0,0,0-.571,0L3.375,3.039.268,3.5a.3.3,0,0,0-.178.514L2.347,6.242,1.813,9.4a.314.314,0,0,0,.464.316L5.052,8.232,7.827,9.712A.314.314,0,0,0,8.292,9.4L7.758,6.242l2.257-2.231A.3.3,0,0,0,9.837,3.5Z" transform="translate(0 -0.018)" fill="currentColor"/>
                            <rect width="7.052" height="14.732" x="0" y="0" fill="none" style="fill:white;clip-path:inset(0 0 0 50%);"></rect>
                        </svg>
                    @else
                        <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="14.105" height="14.732" viewBox="0 0 10.105 9.732">
                            <path data-name="star - Copy" d="M9.837,3.5,6.73,3.039,5.338.179a.335.335,0,0,0-.571,0L3.375,3.039.268,3.5a.3.3,0,0,0-.178.514L2.347,6.242,1.813,9.4a.314.314,0,0,0,.464.316L5.052,8.232,7.827,9.712A.314.314,0,0,0,8.292,9.4L7.758,6.242l2.257-2.231A.3.3,0,0,0,9.837,3.5Z" transform="translate(0 -0.018)" fill="none"/>
                        </svg>
                    @endif
                </span>
                </li>
            @endfor
            <span class="product__items--rating__count--number">| ({{ $product->reviews()->count() }}
                {{ $product->reviews()->count() == 1 ? 'review' : 'reviews' }})
                                            </span>

        </ul>
    </div>


@endif
