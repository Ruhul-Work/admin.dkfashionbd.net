@extends('auth.master')


@section('content')
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper login-new">
                <div class="container">
                    <div class="login-content user-login">
                        <div class="login-logo">
                            <img src="{{image(get_option('logo'))}}" alt="img">
                        </div>


                        @if (request()->otpSend)
                            <form id="otp-form">
                                @csrf
                                <div class="login-userset" style="padding: 20px">
                                    <div class="login-userheading">
                                        <h3 class="text-center">Reset Password</h3>
                                        <h4 class="text-center">Please enter your 4 Digit Code and set new password.</h4>
                                    </div>
                                    <div class="form-login">
                                        <label class="form-label fw-bold">OTP<span class="text-danger">*</span></label>
                                        <div class="form-addons">
                                            <input type="text" name="otp" class="form-control" required>
                                            <img src="{{ asset('theme/admin/assets/img/icons/mail.svg') }}" alt="img">
                                        </div>
                                    </div>

                                    <div class="form-login">
                                        <label class="fw-bold">New Password<span class="text-danger">*</span></label>
                                        <div class="pass-group">
                                            <input type="password" name="new_password" class="pass-input"
                                                autocomplete="new-password" required>
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div>

                                    <strong>For the security of your account,<br>We recommend following these guidelines to create a
                                        strong password:</strong>
                                    <ul style="line-height: 1.9;
                                    font-size: 14px;">
                                        <li>1.Use a minimum of 8 characters.</li>
                                        <li>2.Include a combination of uppercase and lowercase letters.</li>
                                        <li>3.Include numbers and special characters, such as @, #, $, %.</li>
                                        <li>4.Avoid using common or easily guessable passwords.</li>
                                        <li>5.Regularly update your password and avoid reusing it across multiple platforms.
                                        </li>
                                    </ul>
                                    <p>Thank you for your cooperation.</p>

                                    <div class="form-login">
                                        <button class="btn btn-login bg-danger" type="submit">

                                            <span class="spinner-border spinner-border-sm d-none"
                                                style="width: 1.2rem; height: 1.2rem;" role="status"
                                                aria-hidden="true"></span>
                                            Reset Password
                                        </button>
                                    </div>

                                    <div class="signinform">
                                        <h4>Already have a account?<a href="{{ route('backend.login') }}" class="hover-a">
                                                Login</a></h4>
                                    </div>


                                </div>

                            </form>
                        @else
                            <form id="login-form">
                                @csrf
                                <div class="login-userset" style="padding: 20px">
                                    <div class="login-userheading">
                                        <h3 class="text-center">Forget Password</h3>
                                        <h4 class="text-center">Please enter your Email/Phone/Username to get started</h4>
                                    </div>
                                    <div class="form-login">
                                        <label class="form-label fw-bold">Email/Username/Phone<span
                                                class="text-danger">*</span></label>
                                        <div class="form-addons">
                                            <input type="text" name="username" class="form-control">
                                            <img src="{{ asset('theme/admin/assets/img/icons/users1.svg') }}"
                                                alt="img">
                                        </div>
                                    </div>

                                    <div class="form-login">
                                        <button class="btn btn-login bg-danger" type="submit">

                                            <span class="spinner-border spinner-border-sm d-none"
                                                style="width: 1.2rem; height: 1.2rem;" role="status"
                                                aria-hidden="true"></span>
                                            Get Started
                                        </button>
                                    </div>

                                    <div class="signinform">
                                        <h4>Already have a account?<a href="{{ route('backend.login') }}" class="hover-a">
                                                Login</a></h4>
                                    </div>


                                </div>

                            </form>
                        @endif



                    </div>
                    <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                        <p><strong> Copyright &copy; {{ date('Y') }}</strong> <strong><a
                                    class="text-danger text-uppercase" target="_blank"
                                    href="{{ get_option('dev_url') }}">{{ get_option('dev') }}</a>.</strong> <strong> All
                                rights reserved</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('meta')
    <title>Forget Password - {{ get_option('title') }}</title>
    <meta name="description" content="Forget Password - {{ get_option('title') }}">
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#login-form').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                var formData = $(this).serialize();
                $('#login-form :input').prop('disabled', true);
                // Show spinner
                $('.btn-login .spinner-border').removeClass('d-none');

                // Perform AJAX login request
                $.ajax({
                    url: '{{ route('backend.forget.password.action') }}', // Route for handling login
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {



                        if (response.login) {

                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                showConfirmButton: false,
                            });
                            //perform redirect;
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('backend.forget.password') }}?otpSend=true";
                            }, 2000);
                        } else {
                            $('#login-form :input').prop('disabled', false);
                            // Show spinner
                            $('.btn-login .spinner-border').addClass('d-none');

                            Swal.fire({
                                title: "Failed",
                                text: response.message,
                                icon: "error"
                            });
                        }

                    },
                    error: function(xhr) {
                        $('#login-form :input').prop('disabled', false);
                        // Show spinner
                        $('.btn-login .spinner-border').addClass('d-none');
                        Swal.fire({
                            title: "Failed",
                            text: response.message,
                            icon: "error"
                        });
                    }
                });
            });

            $('#otp-form').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                var formData = $(this).serialize();
                $('#otp-form :input').prop('disabled', true);
                // Show spinner
                $('#otp-form .btn-login .spinner-border').removeClass('d-none');

                // Perform AJAX login request
                $.ajax({
                    url: '{{ route('backend.forget.password.verify') }}', // Route for handling login
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {



                        if (response.login) {

                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                showConfirmButton: false,
                            });
                            //perform redirect;
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('backend.login') }}";
                            }, 2000);
                        } else {
                            $('#otp-form :input').prop('disabled', false);
                            // Show spinner
                            $('#otp-form .btn-login .spinner-border').addClass('d-none');

                            Swal.fire({
                                title: "Failed",
                                text: response.message,
                                icon: "error"
                            });
                        }

                    },
                    error: function(xhr) {
                        $('#otp-form :input').prop('disabled', false);
                        // Show spinner
                        $('#otp-form .btn-login .spinner-border').addClass('d-none');
                        Swal.fire({
                            title: "Failed",
                            text: response.message,
                            icon: "error"
                        });
                    }
                });
            });
        });
    </script>
@endsection
