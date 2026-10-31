<div class="swiper-wrapper">
    @foreach($reviews as $review)
        <div class="swiper-slide">
            <div class="testimonial__items text-center">
                <div class="testimonial__items--thumbnail">
                    <img class="testimonial__items--thumbnail__img border-radius-50"
                         src="{{asset('theme/frontend/assets/img/icon/user1.png') }}" alt="review-img">
                </div>
                <div class="testimonial__items--content">
                    <h3 class="testimonial__items--title">{{ $review->name }}</h3>
                    <span class="testimonial__items--subtitle">কাস্টমার</span>
                    <hr>
                    <p class="testimonial__items--desc">{{ $review->comment }}</p>
                    <ul class="rating testimonial__rating d-flex justify-content-center">
                        @for ($i = 0; $i < $review->rating; $i++)
                            <li class="rating__list">
                        <span class="rating__list--icon">
                            <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                            </li>
                        @endfor
                        @for ($i = $review->rating; $i < 5; $i++)
                            <li class="rating__list">
                        <span class="rating__list--icon">
                            <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593L8 13.187l4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.329-.32.158-.888-.283-.95l-4.898-.696L8.461.792c-.197-.39-.73-.39-.927 0L5.35 5.119l-4.898.696c-.441.062-.612.63-.283.95l3.522 3.356-.83 4.73zM8 12.025l-3.764 1.936.718-4.08L1.57 6.614l4.108-.584L8 2.223l1.322 3.807 4.108.584-2.982 2.768.718 4.08L8 12.025z"/>
                            </svg>
                        </span>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="testimonial__pagination swiper-pagination"></div>

