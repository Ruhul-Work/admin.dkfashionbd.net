<!DOCTYPE html>
<html lang="en">

@include('auth.include.header')


<body class="account-page" style="background: rgb(251, 251, 251);
background: linear-gradient(90deg, rgba(251, 251, 251, 1) 0%, rgb(183 110 140 / 10%) 0%, #28c76f26 62%);"> 


    @yield('content')

    @include('auth.include.footer')
    @include('backend.include.scripts')
    @yield('script')
</body>

</html>
