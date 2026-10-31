{{--     topbar --}}
<div class="main__header header__sticky">
    <div class="container-fluid">
        <div class="main__header--inner position__relative d-flex justify-content-between align-items-center">
            <div class="offcanvas__header--menu__open ">
                <a class="offcanvas__header--menu__open--btn" href="javascript:void(0)" data-offcanvas>
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon offcanvas__header--menu__open--svg"
                        viewBox="0 0 512 512">
                        <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10"
                            stroke-width="32" d="M80 160h352M80 256h352M80 352h352" />
                    </svg>
                    <span class="visually-hidden">Menu Open</span>
                </a>
            </div>
            <div class="main__logo">
                <h1 class="main__logo--title"><a class="main__logo--link" href="{{ route('home') }}">
                   @php
                     $logo = get_option('logo');
                    @endphp

                    <img
                            class="main__logo--img" src="{{ asset($logo) }}"
                            alt="logo-img"></a></h1>
            </div>
            <div class="header__search--widget header__sticky--none d-none d-lg-block">
                @if ($device != 'mobile')
                    @include('  frontend.modules.search.search_form')
                @endif
            </div>

            <div class="header__account header__sticky--none">
                <ul class="d-flex">
                    <li class="header__account--items">
                        <div class="header__account my-account ">
                            <a class="header__account--btn" href="javascript:void(0)">
                                <i class="ri-user-line"></i>
                                <span class="header__account--btn__text">আমার একাউন্ট</span>
                            </a>
                            <div class="header__account--dropdown">
                                @auth
                                    <a href="{{ route('dashboard') }}"> অর্ডার ড্যাশবোর্ড</a>
                                    
                                    @if(Auth::user()->is_admin)
                                     <a href="{{ route('dash.home') }}">ড্যাশবোর্ড</a>
                                    @endif
                                    <a href="{{ route('profile.show') }}">প্রোফাইল</a>
                                    {{--                                    <a href="add-fund.html">অ্যাড ফান্ড</a> --}}
                                    <a href="{{ route('logout') }}">লগ আউট</a>
                                @else
                                    <a href="{{ route('login') }}">লগইন</a>
                                    <a href="{{ route('register') }}">রেজিস্টার</a>
                                @endauth
                            </div>
                        </div>
                    </li>
                    <li class="header__account--items d-none d-lg-block">
                        <a class="header__account--btn" href="{{ route('wishlist.index') }}">
                            <i class="ri-heart-3-line"></i>
                            <span class="header__account--btn__text"> উইশ লিস্ট</span>
                            <span class="items__count wishlist wishlist-count">0</span>
                        </a>
                    </li>
                    <li class="header__account--items d-none d-lg-block">
                        {{--                        <a class="header__account--btn minicart__open--btn" href="javascript:void(0)" data-offcanvas> --}}
                        <a class="header__account--btn minicart__open--btn" href="{{ route('cart.show') }}">
                            <i class="ri-shopping-cart-line"></i>
                            <span class="header__account--btn__text"> ক্রয় তালিকা</span>
                            <span class="items__count cart-item-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="header__menu d-none header__sticky--block d-lg-block">
                <!--@inject('menuService', 'App\Services\MenuService')-->
                <nav class="header__menu--navigation">
                    <ul class="d-flex">
                        @foreach ($menuItems as $menuItem)
                            @if ($menuItem->menu_type == 'General')
                                @php
                                    $newLink = str_replace('_DOMAIN_/', env('APP_URL'), $menuItem->link);
                                @endphp
                                <li class="header__menu--items ">
                                    <a class="header__menu--link-sticky"
                                        href="{{ $newLink }}">{{ $menuItem->name }}</a>
                                </li>
                            @elseif ($menuItem->menu_type == 'Mega')
                                <li class="header__menu--items mega__menu--items style2">
                                    <a class="header__menu--link-sticky arrow"
                                        href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                                    <ul class="header__mega--menu">
                                        @foreach ($menuItem->megamenu as $singleMegaList)
                                            @php
                                                $newLink = str_replace(
                                                    '_DOMAIN_/',
                                                    env('APP_URL'),
                                                    $singleMegaList->link,
                                                );
                                            @endphp
                                            <li class="header__mega--menu__li">
                                                <ul class="header__mega--sub__menu">
                                                    <li class="header__mega--sub__menu_li">
                                                        <a class="header__mega--sub__menu--title"
                                                            href="{{ $newLink }}">
                                                            {{ $singleMegaList->name }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="header__menu--items style2">

                                    <a class="header__menu--link-sticky arrow"
                                        href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                                    <ul class="header__sub--menu">
                                        @foreach ($menuItem->submenus as $singleSubMenu)
                                            @php
                                                $newLink = str_replace(
                                                    '_DOMAIN_/',
                                                    env('APP_URL'),
                                                    $singleSubMenu->link,
                                                );
                                            @endphp
                                            <li class="header__sub--menu__items">
                                                <a href="{{ $newLink }}" class="header__sub--menu__link">
                                                    {{ $singleSubMenu->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
            <div class="header__account header__account2 header__sticky--block">
                <ul class="d-flex">
                    <li
                        class="header__account--items header__account2--items  header__account--search__items d-none d-lg-block">
                        <a data-open="search-modal" href="javascript:void(0)">
                            <svg class="header__search--button__svg" xmlns="http://www.w3.org/2000/svg" width="26.51"
                                height="23.443" viewBox="0 0 512 512">
                                <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z"
                                    fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10"
                                    stroke-width="32" d="M338.29 338.29L448 448" />
                            </svg>
                        </a>
                    </li>
                    <li class="header__account--items header__account2--items">
                        <div class="header__account my-account">
                            <a class="header__account--btn" href="javascript:void(0)">
                                <i class="ri-user-line"></i>
                                <span class="visually-hidden">আমার একাউন্ট</span>
                            </a>
                            <div class="header__account--dropdown">
                                @auth
                                    <a href="{{ route('dashboard') }}"> অর্ডার ড্যাশবোর্ড</a>
                                    @if(Auth::user()->is_admin)
                                     <a href="{{ route('dash.home') }}">ড্যাশবোর্ড</a>
                                    @endif
                                    <a href="{{ route('profile.show') }}">প্রোফাইল</a>
                                    <a href="{{ route('logout') }}">লগ আউট</a>
                                @else
                                    <a href="{{ route('login') }}">লগইন</a>
                                    <a href="{{ route('register') }}">রেজিস্টার</a>
                                @endauth

                            </div>
                        </div>
                    </li>
                    <li class="header__account--items header__account2--items d-none d-lg-block">
                        <a class="header__account--btn" href="{{ route('wishlist.index') }}">
                            <i class="ri-heart-3-line"></i>
                            <span class="items__count  wishlist style2 wishlist-count">0</span>
                        </a>
                    </li>
                    <li class="header__account--items header__account2--items d-none d-lg-block">
                        <a class="header__account--btn minicart__open--btn" href="{{ route('cart.show') }}">
                            <i class="ri-shopping-cart-line"></i>
                            <span class="items__count style2 cart-item-count" id="count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
