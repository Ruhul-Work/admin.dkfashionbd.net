<div class="reviews__comment--list d-flex">
    <div class="reviews__comment--thumb">

        @if(!empty($comment->user->image))
            <img src="{{ asset($comment->user->image) }}" alt="comment-thumb">
        @else
            <img src="{{ asset('theme/frontend/assets/img/icon/user.jpg') }}" alt="default-comment-thumb">
        @endif


    </div>
    <div class="reviews__comment--content">
        <div class="comment__content--topbar d-flex justify-content-between">
            <div class="comment__content--topbar__left">
                <h4 class="reviews__comment--content__title2">{{ $comment->user->name ?: 'নাম নেই' }}</h4>

                <span class="reviews__comment--content__date2">{{ $comment->created_at->format('F j, Y') }}</span>
            </div>

        </div>
        <p class="reviews__comment--content__desc">{{ $comment->comment }}</p>
    </div>
</div>
