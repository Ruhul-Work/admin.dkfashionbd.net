<!-- Start header area -->
<header class="header__section">
    <div class="header__topbar" style="background: #94030d;">
        <div class="container-fluid">
            <div class="header__topbar--inner d-flex align-items-center justify-content-center">
                <img src="{{asset('theme/frontend/assets/img/banner/banner.gif')}}" alt="banner image">
            </div>
        </div>
    </div>


@include('frontend.include.search_navbar')
@include('frontend.include.main_navbar')
@include('frontend.include.side_navbar')



<!-- bottom on mobile Offcanvas stikcy toolbar -->
   <div class="offcanvas__stikcy--toolbar">
        <ul class="d-flex justify-content-between">
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{route('home')}}">
                        <span class="offcanvas__stikcy--toolbar__icon">
                            <i class="ri-home-8-line"></i>
                        </span>
                    <span class="offcanvas__stikcy--toolbar__label">হোম</span>
                </a>
            </li>

            <li class="offcanvas__stikcy--toolbar__list ">
                <a class="offcanvas__stikcy--toolbar__btn search__open--btn" href="javascript:void(0)"
                   data-open="search-modal">
                        <span class="offcanvas__stikcy--toolbar__icon">
                            <i class="ri-search-line"></i>
                        </span>
                    <span class="offcanvas__stikcy--toolbar__label">সার্চ</span>
                </a>
            </li>
            <li class="offcanvas__stikcy--toolbar__list ">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{route('campaign.all')}}">
                        <span class="offcanvas__stikcy--toolbar__icon">
                            <i class="ri-gift-2-line"></i>
                        </span>
                    <span class="offcanvas__stikcy--toolbar__label">অফার</span>
                </a>
            </li>

            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn minicart__open--btn"  href="{{route('cart.show')}}">
                        <span class="offcanvas__stikcy--toolbar__icon">
                            <i class="ri-shopping-basket-2-line"></i>
                        </span>
                    <span class="offcanvas__stikcy--toolbar__label">কার্ট</span>
                    <span class="items__count cart-item-count">0</span>
                </a>
            </li>
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{route('wishlist.index')}}">
                        <span class="offcanvas__stikcy--toolbar__icon">
                            <i class="ri-heart-3-fill"></i>
                        </span>
                    <span class="offcanvas__stikcy--toolbar__label">উইশলিস্ট</span>
                    <span class="items__count wishlist-count" style="left:3.2rem;">0</span>
                </a>
            </li>
        </ul>
    </div>
  <!-- End Offcanvas stikcy toolbar -->

</header>
<!-- End header area -->

