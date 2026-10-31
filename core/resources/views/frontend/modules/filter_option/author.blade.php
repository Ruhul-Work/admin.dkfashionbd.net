
@if(count($authors)>0)
<div class="single__widget widget__bg">
    <h2 class="widget__title h3">লেখক</h2>




    <div class="product__view--mode__list product__view--search d-none d-lg-block mb-3 ">
        <form class="form-inline justify-content-center  " action="#" method="GET">
            <div class="input-group " >
                <input type="text" class="form-control " id="author-search"   placeholder="Search for authors..."  style="height: 40px; font-size: 13px;">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" id="search-btn" style="height: 40px; padding: 10px 10px;">
                        <i class="fa fa-search" style="font-size: 1.5rem;"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <ul class="widget__form--check" id="author-list">


        @forelse($authors as $authorId => $authorName)
            <li class="widget__form--check__list single-author">
                <label class="widget__form--check__label author-name" for="author_{{ $authorId }}">{{ $authorName }}</label>
                <input class="widget__form--check__input filter-checkbox" id="author_{{ $authorId }}" type="checkbox" name="authors[]" value="{{ $authorId }}">
                <span class="widget__form--checkmark"></span>
            </li>
        @empty
            <li id="no-authors-found" class="widget__form--check__list">
                <label class="widget__form--check__label">No authors found</label>
            </li>
        @endforelse





    </ul>
</div>
    @endif
