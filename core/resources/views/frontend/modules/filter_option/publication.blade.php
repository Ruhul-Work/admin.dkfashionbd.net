@if(count($publishers)>0)
<div class="single__widget widget__bg">
    <h2 class="widget__title h3">প্রকাশক</h2>

    <div class="product__view--mode__list product__view--search d-none d-lg-block mb-3 " >
        <form class="form-inline justify-content-center  " action="#" method="GET">
            <div class="input-group " >
                <input type="text" class="form-control " id="publisher-search" placeholder="Search for publishers..." style="height: 40px; font-size: 13px;">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-danger" style=" height: 40px;
            padding: 10px 10px;">
                        <i class="fa fa-search" style="font-size: 1.5rem;"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <ul class="widget__form--check" id="publisher-list">
        @forelse($publishers as $publisherId => $publisherName)
            <li class="widget__form--check__list">
                <label class="widget__form--check__label publisher-name" for="publication_{{ $publisherId }}">{{ $publisherName }}</label>
                <input class="widget__form--check__input filter-checkbox" id="publication_{{ $publisherId }}" type="checkbox" name="publishers[]" value="{{ $publisherId }}">
                <span class="widget__form--checkmark"></span>
            </li>
            @empty
                <li id="no-authors-found" class="widget__form--check__list">
                    <label class="widget__form--check__label">No publications found</label>
                </li>
            @endforelse
    </ul>


</div>
@endif
