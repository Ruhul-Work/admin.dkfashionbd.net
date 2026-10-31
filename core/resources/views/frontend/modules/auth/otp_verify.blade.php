
@extends('frontend.layouts.master')

@section('meta')
    <title>OTP Verification | {{ get_option('title') }}</title>
@endsection

@section('content')
    <!-- Start breadcrumb section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__content--title mb-25">OTP যাচাইকরণ</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a class="" href="{{route('home')}}">হোম</a></li>
                            <li class="breadcrumb__content--menu__items"><span class="">OTP যাচাইকরণ</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End breadcrumb section -->

    <!-- Start login section  -->
    <div class="login__section section--padding">
        <div class="container">
            <form action="{{ route('auth.otp.verify.post') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{$email}}">
                <div class="login__section--inner">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-6">
                            <div class="card bg-white border-0"
                                 style="box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1); border-radius:15px;">
                                <div class="card-body otp-body p-5 text-center">
                                    <img src="{{ asset('theme/frontend/assets/img/icon/otp.png') }}" alt="icon">
                                    <p>আপনার কোড আপনার ইমেইলে পাঠানো হয়েছে</p>
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

<div class="otp-field mb-4">
    <input type="number"  name="otp[]" required id="input1" oninput="enableNextInput(1)" maxlength="1" />
    <input type="number"  name="otp[]" required id="input2" oninput="enableNextInput(2)" maxlength="1" disabled />
    <input type="number"  name="otp[]" required id="input3" oninput="enableNextInput(3)" maxlength="1" disabled />
    <input type="number"  name="otp[]" required id="input4" maxlength="1" disabled />
</div>
                                    <!--<div class="otp-field mb-4">-->
                                    <!--    <input type="number" name="otp[]" required />-->
                                    <!--    <input type="number" name="otp[]" required />-->
                                    <!--    <input type="number" name="otp[]" required />-->
                                    <!--    <input type="number" name="otp[]" required />-->
                                    <!--</div>-->
                                    <button type="submit" class="cart__summary--footer__btn primary__btn mb-3">
                                        যাচাই করুন
                                    </button>
                                    <p class="resend text-muted mb-0">
                                        কোড পাননি? <a href="#" class="resend-otp">পুনরায় পাঠান</a>
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- End login section  -->

    <!-- End login section  -->




@endsection
@section('scripts')

    <script>
        $(document).ready(function() {
        $(document).on('click', '.resend a', function(e) {
            e.preventDefault();
            const email = '{{ $email }}';

            $.ajax({
                url: '{{ route('auth.otp.resend') }}',
                method: 'POST',
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Display success message
                    showToast(response.success,'success');
                },
                error: function(xhr) {
                    // Display error message
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (let key in errors) {
                        errorMessage += errors[key] + '\n';
                    }
                    alert(errorMessage);
                }
            });
        });
        });
    </script>
    
    <script>
    function enableNextInput(currentInput) {
        const nextInput = document.getElementById(`input${currentInput + 1}`);
        const currentElement = document.getElementById(`input${currentInput}`);
        
        if (currentElement.value.length === 1) {
            nextInput.disabled = false;
            nextInput.focus();
        }
    }
</script>
    <script>


        // $(document).ready(function() {
        //     $('#loginForm').on('submit', function(e) {
        //         e.preventDefault();
        //         validateForm();
        //     });
        // });
        //
        // function validateForm() {
        //     // পূর্বের ত্রুটি বার্তাগুলি সাফ করুন
        //     $('.error-message').text('').hide();
        //
        //     const identifier = $('#identifier').val().trim();
        //     const password = $('#password').val().trim();
        //     let isValid = true;
        //
        //     // পরিচয় যাচাই
        //     if (!identifier) {
        //         $('#identifierError').text('মোবাইল নম্বর বা ইমেইল প্রয়োজন।').show();
        //         isValid = false;
        //     }
        //
        //     // পাসওয়ার্ড যাচাই
        //     if (!password) {
        //         $('#passwordError').text('পাসওয়ার্ড প্রয়োজন।').show();
        //         isValid = false;
        //     }
        //
        //     // যদি সমস্ত যাচাই পাস করে, ফর্ম জমা দিন
        //     if (isValid) {
        //         $('#loginForm').unbind('submit').submit();
        //     }
        // }










    </script>
@endsection








