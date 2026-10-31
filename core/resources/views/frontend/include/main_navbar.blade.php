{{--    main navbar --}}{{-- inside header included --}}
<div class="header__bottom">
    <div class="container-fluid">
        <div
            class="header__bottom--inner position__relative d-none d-lg-flex justify-content-between align-items-center">
            <div class="header__menu">
                <!--@inject('menuService', 'App\Services\MenuService')-->
                <nav class="header__menu--navigation">
                    <ul class="d-flex">
                        @foreach ($menuItems as $menuItem)
                            @if ($menuItem->menu_type == 'General')
                                @php
                                    $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $menuItem->link);
                                @endphp
                                <li class="header__menu--items">
                                    <a class="header__menu--link @if ($loop->first) home @endif" href="{{ $newLink }}">{{ $menuItem->name }}</a>
                                </li>
                            @elseif($menuItem->menu_type == 'Mega')
                                <li class="header__menu--items mega__menu--items">
                                    <a class="header__menu--link arrow" href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                                    <ul class="header__mega--menu d-flex">
                                        @foreach ($menuItem->megamenu as $singleMegaList)
                                        @php
                                        $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $singleMegaList->link);
                                    @endphp
                                            <li class="header__mega--menu__li">
                                                <ul class="header__mega--sub__menu">
                                                    <li class="header__mega--sub__menu_li">
                                                        <a class="header__mega--sub__menu--title" href="{{ $newLink }}">{{ $singleMegaList->name }}</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="header__menu--items">
                                    <a class="header__menu--link arrow" href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                                    <ul class="header__sub--menu">
                                        @foreach ($menuItem->submenus as $singleSubMenu)
                                        @php
                                        $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $singleSubMenu->link);
                                    @endphp
                                            <li class="header__sub--menu__items">
                                                <a href="{{ $newLink }}" class="header__sub--menu__link">{{ $singleSubMenu->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>


            </div>

            <a href="tel:{{ get_option('phone_number') }}" class="header__discount--text text-white fw-bold rise-shake">
    <img src="{{asset('theme/frontend/assets/img/telephone.png')}}" alt="Telephone Icon"> হটলাইন: {{ get_option('phone_number') }}
</a>

        </div>
    </div>
</div>
