
<div class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-2 row-cols-sm-u-2 row-cols-1 mb--n30">
    @forelse($blogs as $blog)
        <div class="col mb-30">
            <div class="blog__items">
                <div class="blog__thumbnail">
                    <a class="blog__thumbnail--link" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">
                        <img class="blog__thumbnail--img lazy-load"   data-src="{{ asset($blog->thumbnail) }}"  src="{{asset('theme/frontend/assets/img/default/blog.png')}}" alt="{{ $blog->title }}">
                    </a>
                </div>
                <div class="blog__content">
                    <span class="blog__content--meta">{{ $blog->created_at->format('F d, Y') }}</span>
                    <h3 class="blog__content--title">
                        <a href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">{{ $blog->title }}</a>
                    </h3>
                    <a class="blog__content--btn primary__btn" href="{{ route('blogs.single', ['slug_or_id' => $blog->slug ?: $blog->id]) }}">{{ __('Read more') }}</a>
                </div>
            </div>
        </div>
    @empty
        <p>{{ __('No Blog found') }}</p>
    @endforelse
</div>

@include('frontend.modules.pagination.pagination_design', ['items' => $blogs])



