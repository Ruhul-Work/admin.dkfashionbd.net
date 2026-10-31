<!doctype html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta name="description" content="{{ strip_tags( get_option('description')) }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('theme/frontend/assets/img/logo/Logo.webp') }}">
    <!-- ======= All CSS Plugins here ======== -->
    <link rel="stylesheet" href="{{asset('theme/frontend/assets/css/plugins/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/frontend/assets/css/plugins/glightbox.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css"-->
    <!--      referrerpolicy="no-referrer" />-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css"
          referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
    <!-- Plugin css -->
    <link rel="stylesheet" href="{{ asset('theme/frontend/assets/css/vendor/bootstrap.min.css') }}">
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="{{ asset('theme/frontend/assets/css/custom.css') }}?id={{time()}}">
    <link rel="stylesheet" href="{{ asset('theme/frontend/assets/css/style.css') }}?id={{time()}}">
    
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1THY1BY8GY"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1THY1BY8GY');
</script>

</head>

<body>
@include('frontend.include.header')

@yield('content')

@include('frontend.include.footer')
{{--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
<script src="{{asset('theme/frontend/assets/js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
<!-- All Script JS Plugins here  -->
<script src="{{ asset('theme/frontend/assets/js/vendor/popper.js') }}" defer></script>
<script src="{{ asset('theme/frontend/assets/js/vendor/bootstrap.min.js') }}" defer></script>
<script src="{{ asset('theme/frontend/assets/js/plugins/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('theme/frontend/assets/js/plugins/glightbox.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

<!-- Custom theme script js -->
<script src="{{ asset('theme/frontend/assets/js/script.js') }}"></script>

@include('frontend.include.custom_script')
@include('frontend.include.cart_ wishlist_script')

@yield('scripts')
@if(session('success') || session('error') || session('warning') || session('message'))
    <script>
        $(document).ready(function() {
            const message = '{{ session('success') ?? session('error') ?? session('warning') ?? session('message') }}';
            const type = '{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'message')) }}';
            showToast(message, type);
        });
    </script>
@endif
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/670cd69a4304e3196ad13e71/1ia52cjeu';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>

</html>
