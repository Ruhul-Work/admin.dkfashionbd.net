
@if(count($categories)>0)
<div class="single__widget widget__bg">
    <h2 class="widget__title h3">ক্যাটাগরি</h2>

    <div class="product__view--mode__list product__view--search d-none d-lg-block mb-3" >
        <form class="form-inline justify-content-center" action="#" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" id="category-search" placeholder="Search for categories..." style="height: 40px; font-size: 13px;">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" id="search-btn" style="height: 40px; padding: 10px 10px;">
                        <i class="fa fa-search" style="font-size: 1.5rem;"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <ul class="widget__form--check" id="category-list">
        @forelse($categories as $categoryId => $categoryName)
            <li class="widget__form--check__list single-category">
                <label class="widget__form--check__label category-name" for="category_{{ $categoryId }}">{{ $categoryName }}</label>
                <input class="widget__form--check__input filter-checkbox" id="category_{{ $categoryId }}" type="checkbox" name="categories[]" value="{{ $categoryId }}">
                <span class="widget__form--checkmark"></span>
            </li>
        @empty
            <li id="no-categories-found" class="widget__form--check__list">
                <label class="widget__form--check__label">No categories found</label>
            </li>
        @endforelse
    </ul>
</div>
    @endif

