@extends('auth.master')

@section('content')
    <style>
        .login-page {
            position: relative;
            isolation: isolate;
            min-height: 100vh;
            overflow: hidden;
            padding: 32px 16px;
            background:
                url('{{ asset('theme/admin/assets/images/cloth-bg.avif') }}')
                center 54% / cover no-repeat;
        }

        .login-page::before {
            position: absolute;
            z-index: -1;
            inset: 0;
            content: "";
            background: linear-gradient(115deg, rgba(24, 20, 17, .68), rgba(24, 20, 17, .24));
        }

        .login-page .login-card {
            box-sizing: border-box;
            width: 100%;
            max-width: 500px;
            padding: 42px;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 24px;
            background: rgba(255, 255, 255, .2);
            -webkit-backdrop-filter: blur(18px);
            backdrop-filter: blur(10px);
            box-shadow: 0 28px 80px rgba(18, 15, 12, .3);
        }

        .login-card h4 {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -.035em;
        }

        .login-card .max-w-464-px > .justify-center {
            text-align: center;
        }

        .login-card .max-w-464-px > .justify-center p {
            font-size: .95rem;
        }

        .login-card,
        .login-card .text-secondary-light,
        .login-card .form-check-label,
        .login-card p {
            color: rgba(255, 255, 255, .88);
        }

        .login-card .form-control {
            height: 50px;
            border: 1px solid rgba(255, 255, 255, .72);
            background: rgba(255, 255, 255, .82);
            color: #29231f;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .login-card .form-control::placeholder {
            color: #6b625d;
            opacity: 1;
        }

        .login-card .form-control:focus {
            border-color: #fff;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, .2);
        }

        .login-card .form-control:-webkit-autofill,
        .login-card .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #29231f;
            box-shadow: 0 0 0 1000px rgba(255, 255, 255, .92) inset;
        }

        .login-card .icon,
        .login-card .toggle-password {
            color: #583b2b;
            z-index: 2;
        }

        .login-card .iconify-icon,
        .login-card iconify-icon {
            color: inherit;
        }

        .login-card .btn-primary {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            border: 1px solid rgba(167, 243, 208, .5);
            background: linear-gradient(135deg, #0f766e 0%, #059669 52%, #047857 100%);
            background-size: 180% 180%;
            box-shadow: 0 10px 24px rgba(5, 150, 105, .28);
            color: #fff;
            font-weight: 650;
            letter-spacing: .01em;
            transition: transform .2s ease, box-shadow .2s ease, background-position .35s ease;
        }

        .login-card .btn-primary::before {
            position: absolute;
            inset: -1px;
            padding: 2px;
            border-radius: inherit;
            background: linear-gradient(110deg, transparent 22%, rgba(255, 255, 255, .95) 48%, #5eead4 54%, transparent 78%);
            background-size: 260% 100%;
            content: "";
            pointer-events: none;
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            will-change: background-position;
            animation: login-button-border-shine 3.2s ease-in-out infinite alternate;
        }

        .login-card .btn-primary:hover,
        .login-card .btn-primary:focus-visible {
            transform: translateY(-1px);
            background-position: 100% 50%;
            box-shadow: 0 14px 28px rgba(5, 150, 105, .38);
            color: #fff;
        }

        .login-card .btn-primary:focus-visible {
            outline: 3px solid rgba(94, 234, 212, .32);
            outline-offset: 3px;
        }

        @keyframes login-button-border-shine {
            from {
                background-position: 170% 0;
            }

            to {
                background-position: -70% 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-card .btn-primary::before {
                animation: none;
            }
        }

        .login-card .alert {
            border-radius: 12px;
        }

        .login-card a {
            color: #fff;
        }

        @media (max-width: 575.98px) {
            .login-page .login-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
        }
    </style>
    <section class="auth bg-base d-flex flex-wrap align-items-center justify-content-center login-page">
        <!-- <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center bg-white">
                <img src={{ asset('theme/admin/assets/images/auth/login.png') }} alt="">
            </div>
        </div> -->
        <div class="auth-right login-card d-flex flex-column justify-content-center">
            @if (session('fw_block_msg'))
                <div class="alert alert-danger mb-3">{{ session('fw_block_msg') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="max-w-464-px mx-auto w-100">
                <div class=" justify-center">
                    <a href="#" class="mb-40  d-flex justify-content-center">
                        @php
                            $logoUrl = optional($companySettings)->logo
                                ? image($companySettings->logo)
                                : asset('theme/admin/assets/images/logo1.png');
                        @endphp
                        <img src={{ $logoUrl }} alt=""
                            style="width: 200px; height: 86px; max-width: 100%; object-fit: contain;">
                    </a>
                    <h4 class="mb-12 fw-semibold text-secondary-light">Sign In to your Account</h4>
                    <p class="mb-32 text-secondary-light text-md">Welcome back! please enter your detail</p>
                </div>
                <form method="POST" action="{{ route('backend.login.action') }}">
                    {{-- CSRF Token --}}
                    @csrf
                    <div class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="text" name="identifier" class="form-control h-56-px bg-neutral-50 radius-12"
                            placeholder="Email">
                    </div>
                    <div class="position-relative mb-20">
                        <div class="icon-field">
                            <span class="icon top-50 translate-middle-y">
                                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                            </span>
                            <input type="password" name="password" class="form-control h-56-px bg-neutral-50 radius-12"
                                id="your-password" placeholder="Password">
                        </div>
                        <span
                            class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                            data-toggle="#your-password"></span>
                    </div>
                    <div class="">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input border border-neutral-300" type="checkbox" name="remember" value="1"
                                    id="remeber">
                                <label class="form-check-label" for="remeber">Remember me </label>
                            </div>
                            {{-- <a href="javascript:void(0)" class="text-primary-600 fw-medium">Forgot Password?</a> --}}
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32 "> Sign
                        In</button>

                    {{-- <div class="mt-32 center-border-horizontal text-center">
                        <span class="bg-base z-1 px-4">Or sign in with</span>
                    </div>
                    <div class="mt-32 d-flex align-items-center justify-content-center ">

                        <button type="button"
                            class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50">
                            <iconify-icon icon="logos:google-icon"
                                class="text-primary-600 text-xl line-height-1"></iconify-icon>
                            Google
                        </button>
                    </div> --}}
                    {{-- <div class="mt-32 text-center text-sm">
                        <p class="mb-0">Don’t have an account?<a href="{{ route('backend.register') }}"
                                class="text-primary-600 fw-semibold px-2">Sign Up</a></p>
                    </div> --}}

                </form>
            </div>
        </div>
    </section>
@endsection

@section('meta')
    <title>Login - </title>
    <meta name="description">
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toggle Password Visibility
            $('.toggle-password').on('click', function() {
                const target = $(this).data('toggle');
                const input = $(target);

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    $(this).removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    input.attr('type', 'password');
                    $(this).removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });
        });
    </script>
@endsection
