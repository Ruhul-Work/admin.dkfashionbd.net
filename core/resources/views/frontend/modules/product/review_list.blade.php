<div class="reviews__comment--list d-flex">
    <div class="reviews__comment--thumb">
        @if(!empty($review->user->image))
            <img src="{{ asset($review->user->image) }}" alt="comment-thumb">
        @else
            <img src="{{ asset('theme/frontend/assets/img/icon/user.jpg') }}" alt="default-comment-thumb">
        @endif
    </div>
    <div class="reviews__comment--content">
        <div class="reviews__comment--top d-flex justify-content-between">
            <div class="reviews__comment--top__left">
                <h3 class="reviews__comment--content__title h4">{{ $review->name }}</h3>
                <ul class="rating reviews__comment--rating d-flex">
                    @for($i = 0; $i < $review->rating; $i++)
                        <li class="rating__list">
                            <span class="rating__list--icon">
                                <svg class="rating__list--icon__svg" xmlns="http://www.w3.org/2000/svg" width="14.105" height="14.732" viewBox="0 0 10.105 9.732">
                                    <path data-name="star - Copy" d="M9.837,3.5,6.73,3.039,5.338.179a.335.335,0,0,0-.571,0L3.375,3.039.268,3.5a.3.3,0,0,0-.178.514L2.347,6.242,1.813,9.4a.314.314,0,0,0,.464.316L5.052,8.232,7.827,9.712A.314.314,0,0,0,8.292,9.4L7.758,6.242l2.257-2.231A.3.3,0,0,0,9.837,3.5Z" transform="translate(0 -0.018)" fill="currentColor"></path>
                                </svg>
                            </span>
                        </li>
                    @endfor
                </ul>
            </div>
            <span class="reviews__comment--content__date">{{ $review->created_at->format('F d, Y') }}</span>
        </div>
        <p class="reviews__comment--content__desc">{{ $review->comment }}</p>
    </div>
</div>

