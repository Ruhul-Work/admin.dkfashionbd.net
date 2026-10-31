<form id="loginForm" method="post" action="{{ route('login') }}">
    @csrf
    <div class="login__section--inner">

        <div class="account__login">
            <div class="account__login--header mb-25 text-center">
                <img src="{{ asset('theme/frontend/assets/img/icon/login.png') }}" alt="icon" >
                <h2 class="account__login--header__title mb-10 mt-3">লগইন</h2>
            </div>

            <div class="account__login--inner">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="identifier">মোবাইল নম্বর অথবা ইমেইল ঠিকানা:</label>
                    <input id="identifier" name="identifier" class="account__login--input" placeholder="ইমেইল বা ফোন" type="text" value="{{ old('identifier') }}">
                    <span id="identifierError" class="error-message" style="display: none;"></span>
                </div>
                <div class="form-group">
                    <label for="password"><i class="ri-lock-2-line"></i> পাসওয়ার্ড:</label>
                    <input id="password" name="password" class="account__login--input" placeholder="পাসওয়ার্ড" type="password" value="{{ old('password') }}">
                    <span id="passwordError" class="error-message" style="display: none;"></span>
                </div>
                <div class="account__login--remember__forgot mb-15 d-flex justify-content-between align-items-center">
                    <div class="account__login--remember position__relative">
                        <input class="checkout__checkbox--input" id="check1" name="remember" type="checkbox">
                        <span class="checkout__checkbox--checkmark"></span>
                        <label class="checkout__checkbox--label login__remember--label" for="check1">
                            আমাকে মনে রাখুন</label>
                    </div>
                    <button class="account__login--forgot" type="button" >আপনার পাসওয়ার্ড ভুলে গেছেন?</button>
                    {{--                                        <button class="account__login--forgot" type="button" onclick="window.location='{{ route('password.request') }}'">আপনার পাসওয়ার্ড ভুলে গেছেন?</button>--}}
                </div>
                <button class="account__login--btn primary__btn" type="submit">লগইন</button>
                <div class="account__login--divide">
                    <span class="account__login--divide__text">অথবা</span>
                </div>
                <p class="account__login--signup__text">একটি অ্যাকাউন্ট নেই? <a href="{{ route('register') }}">এখনই রেজিস্টার করুন</a></p>
            </div>
        </div>
        <style>
            .error-message {
                color: red;
                font-size: 12px;
                margin-top: 5px;
            }
        </style>


    </div>
</form>
