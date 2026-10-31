<!-- Search Form 2 -->
<form class="d-flex header__search--form search-container" id="header__search--form-2" action="#">
    <div class="header__select--categories select d-none d-md-block">
        <select class="header__select--inner product_type" name="product_type"  id="product_type-2">
            @foreach ($productTypes as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="header__search--box flex-grow-1">
        <label class="d-flex align-items-center">
            <input class="header__search--input auto-type" id="auto-type-2" placeholder="অনুসন্ধান করুন" type="text">
        </label>
        <button class="header__search--button bg__secondary text-white" type="submit" aria-label="search button">
            <svg class="header__search--button__svg" xmlns="http://www.w3.org/2000/svg" width="27.51" height="26.443" viewBox="0 0 512 512">
                <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"></path>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448"></path>
            </svg>
        </button>
    </div>
    <div id="search-results-2" class="search-results"></div>
</form>
