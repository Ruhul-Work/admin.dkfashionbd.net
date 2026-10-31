<div class="account__left--sidebar">
    <div class="d-flex justify-content-center">
        <img class="border-radius-50 border p-3 mb-10" src=" {{asset('theme/frontend/assets/img/other/user_dashboard.png')}}" alt="user_icon">

    </div>
    <p class="text-center">{{auth()->user()->name}}</p>


    <ul class="account__menu">
        <li class="account__menu--list active"><a href="{{ route('dashboard') }}"> অর্ডার ড্যাশবোর্ড</a></li>
{{--        <li class="account__menu--list"><a href="{{ route('addresses.index') }}">শিপিং ঠিকানা</a></li>--}}
        <li class="account__menu--list"><a href="{{ route('wishlist.index') }}">উইশ লিস্ট তালিকা</a></li>
        <li class="account__menu--list dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">অ্যাকাউন্ট সেটিংস</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item fs-4 mb-3" href="{{ route('profile.show') }}">প্রোফাইল দেখুন</a></li>
                <li><a class="dropdown-item fs-4" href="{{ route('password.change') }}">পাসওয়ার্ড পরিবর্তন</a></li>


            </ul>
        </li>
{{--        <li class="account__menu--list"><a href="{{ route('user.settings') }}">অ্যাকাউন্ট সেটিংস</a></li>--}}
        <li class="account__menu--list"><a href="{{ route('logout') }}">লগ আউট</a></li>
    </ul>



</div>
