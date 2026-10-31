


@forelse($categories as $category)
    <div class="col-lg-2 col-md-3 col-sm-6 col-6">
        <div class="banner__items position__relative mb-20">
            <a class="banner__items--thumbnail" href="{{route('category.single',['slug' => $category->slug ?? $category->id])}}">

                @if($category->icon)
                <img
                    class="banner__items--thumbnail__img  lazy-load"
                    data-src="{{asset($category->icon)}}"  src="{{asset('theme/frontend/assets/img/default/stationary_cat.png')}}"
                    alt="banner-img">
                @else
                    <img
                        class="banner__items--thumbnail__img "
                        src="{{asset('theme/frontend/assets/img/default/stationary_cat.jpg')}}"
                        alt="banner-img">
                @endif
                <div class="banner__items--content style2 text-center">
                    <h3 class="banner__items--content__title  style2">{{$category->name}}</h3>
                    <span class="banner__items--content__link style2 "> এখনি কিনুন</span>
                </div>
            </a>
        </div>
    </div>


@empty
    <p>No categories found.</p>
@endforelse
@include('frontend.modules.pagination.pagination_design', ['items' => $categories])
