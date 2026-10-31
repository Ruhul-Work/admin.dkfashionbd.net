@extends('auth.master')

@section('content')
<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-lg-block d-none ">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center bg-white">
            <img src="{{ asset('theme/admin/assets/images/auth/register.png') }}" alt="">
        </div>
    </div>

    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">

            <div class="text-center">
                <a href="javascript:void(0)" class="mb-40 max-w-290-px d-flex justify-content-center mx-auto">
                    @php
                            $logoUrl = optional($companySetting)->logo
                                ? image($companySetting->logo)
                                : asset('theme/admin/assets/images/logo1.png');
                        @endphp
                    <img src="{{ $logoUrl }}" alt="Logo" style="width:168px;height:auto;">
                </a>
                <h4 class="mb-12">Create your account</h4>
                <p class="mb-32 text-secondary-light text-lg">Fill the fields below to continue</p>
            </div>

            {{-- Global errors --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-16">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('backend.register.action') }}" autocomplete="off">
                @csrf

                {{-- Full name --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="f7:person"></iconify-icon>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control h-56-px bg-neutral-50 radius-12 @error('name') is-invalid @enderror"
                           placeholder="Full name" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Username (optional, auto-generate if blank) --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mdi:account-badge-outline"></iconify-icon>
                    </span>
                    <input type="text" name="username" value="{{ old('username') }}"
                           class="form-control h-56-px bg-neutral-50 radius-12 @error('username') is-invalid @enderror"
                           placeholder="Username (optional)">
                    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Email (optional) --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror"
                           placeholder="Email (optional)">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Phone (optional) --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                    </span>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="form-control h-56-px bg-neutral-50 radius-12 @error('phone') is-invalid @enderror"
                           placeholder="Phone (optional)">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Password --}}
                <div class="mb-16 position-relative">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" name="password" id="your-password"
                               class="form-control h-56-px bg-neutral-50 radius-12 @error('password') is-invalid @enderror"
                               placeholder="Password" autocomplete="new-password" required>
                        <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                              data-toggle="#your-password"></span>
                    </div>
                    <span class="mt-8 d-block text-sm text-secondary-light">Your password must have at least 6 characters</span>
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                    </span>
                    <input type="password" name="password_confirmation"
                           class="form-control h-56-px bg-neutral-50 radius-12"
                           placeholder="Confirm password" autocomplete="new-password" required>
                </div>

                {{-- Role (hidden or select) --}}
                {{-- <input type="hidden" name="role_id" value="{{ old('role_id', 1) }}"> --}}
                {{-- <input type="hidden" name="role_id" value="1"> --}}
                {{-- যদি রোল সিলেক্ট দেখাতে চান, কন্ট্রোলার থেকে $roles পাঠিয়ে নিচেরটা অনকমেন্ট করুন
                @if(isset($roles) && count($roles))
                    <div class="mb-16">
                        <select name="role_id" class="form-control h-56-px bg-neutral-50 radius-12 @error('role_id') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" @selected(old('role_id')==$r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                @endif
                --}}

                {{-- Branch (optional) --}}
                @if(isset($branches) && count($branches))
                    <div class="mb-16">
                        <select name="branch_id" class="form-control h-56-px bg-neutral-50 radius-12 @error('branch_id') is-invalid @enderror">
                            <option value="">-- No Branch --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                @endif

                {{-- Terms (optional UI) --}}
                <div class="d-flex justify-content-between gap-2 mt-8">
                    <div class="form-check style-check d-flex align-items-start">
                        <input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="1" id="condition">
                        <label class="form-check-label text-sm" for="condition">
                            By creating an account you agree to the
                            <a href="javascript:void(0)" class="text-primary-600 fw-semibold">Terms</a>
                            &amp; <a href="javascript:void(0)" class="text-primary-600 fw-semibold">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-24">
                    Sign Up
                </button>

                <div class="mt-32 center-border-horizontal text-center">
                    <span class="bg-base z-1 px-4">Or sign up with</span>
                </div>

                <div class="mt-24 d-flex align-items-center justify-content-center">
                    <button type="button" class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50" disabled>
                        <iconify-icon icon="logos:google-icon" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                        Google
                    </button>
                </div>

                <div class="mt-24 text-center text-sm">
                    <p class="mb-0">Already have an account?
                        <a href="{{ route('backend.login') }}" class="text-primary-600 fw-semibold">Sign In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('meta')
    <title>Register</title>
    <meta name="description" content="Create a new account">
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <script>
        // simple password show/hide
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.toggle-password');
            if (!btn) return;
            const target = document.querySelector(btn.dataset.toggle);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            btn.classList.toggle('ri-eye-off-line');
        });
    </script>
@endsection
