<!-- sidebar Offcanvas header menu -->
<div class="offcanvas__header">
    <div class="offcanvas__inner">
        <div class="offcanvas__logo">
            <a class="offcanvas__logo_link" href="{{route('home')}}">
                
                @php
                    $logo = get_option('logo');
                @endphp

                <img src="{{asset($logo)}}" alt="Grocee Logo" width="158"
                     height="36">
            </a>
            <button class="offcanvas__close--btn" data-offcanvas>close</button>
        </div>
        <nav class="offcanvas__menu">
            <ul class="offcanvas__menu_ul">
                <!--@inject('menuService', 'App\Services\MenuService')-->
                @foreach ($menuItems as $menuItem)
                    @if ($menuItem->menu_type == 'General')
                    
                                 @php
                                    $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $menuItem->link);
                                @endphp
                        <li class="offcanvas__menu_li">
                            <a class="offcanvas__menu_item" href="{{ $newLink }}">{{ $menuItem->name }}</a>

                        </li>
                    @elseif ($menuItem->menu_type == 'Mega')
                        <li class="offcanvas__menu_li">
                            <a class="offcanvas__menu_item" href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                            <ul class="offcanvas__sub_menu">
                                <li class="offcanvas__sub_menu_li">
                                    @foreach ($menuItem->megamenu as $singleMegaList)
                                    
                                    
                                    
                                     @php
                                        $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $singleMegaList->link);
                                    @endphp
                                            
                                        <a href="{{ $newLink }}"
                                           class="offcanvas__sub_menu_item">   {{ $singleMegaList->name }}</a>
                                    @endforeach
                                </li>
                            </ul>
                        </li>

                    @else
                        <li class="offcanvas__menu_li">
                            <a class="offcanvas__menu_item" href="{{ $menuItem->link }}">{{ $menuItem->name }}</a>
                            <ul class="offcanvas__sub_menu">
                                @foreach ($menuItem->submenus as $singleSubMenu)
                                
                                   
                                        @php
                                        $newLink = str_replace("_DOMAIN_/", env('APP_URL'), $singleSubMenu->link);
                                    @endphp
                                
                                    <li class="offcanvas__sub_menu_li">
                                        <a href="{{$newLink }}"

                                           class="offcanvas__sub_menu_item">{{ $singleSubMenu->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach
            </ul>
            <div class="offcanvas__account--items">
                @auth
                    <a class="offcanvas__account--items__btn d-flex align-items-center" href="{{route('logout')}}">
                          <span class="offcanvas__account--items__icon">
                           <i class="ri-login-box-line"></i> </span>
                        <span class="offcanvas__account--items__label">লগ আউট</span>
                    </a>

                @else
                    <a class="offcanvas__account--items__btn d-flex align-items-center" href="{{route('login')}}">
                            <span class="offcanvas__account--items__icon">
                                <i class="ri-key-fill"></i>
                            </span>
                        <span class="offcanvas__account--items__label">লগইন</span>
                    </a>
                @endauth
            </div>

        </nav>
    </div>
</div>
<!-- End Offcanvas header menu -->
