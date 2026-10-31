@include('backend.include.header')

<body class="backend-admin">
    <div>
        @include('backend.include.sidebar')
        <main class="dashboard-main">
            @include('backend.include.topbar')

            <div class="dashboard-main-body">
                @yield('content')
            </div>

            {{-- golobal sweet alert for session messages --}}
            @if (session('success'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: "{{ session('success') }}",
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            @endif
            @if (session('warning'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: "{{ session('warning') }}",
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: "{{ session('error') }}",
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            @endif
            {{-- global sweet alert for session messages end --}}

            @include('backend.include.footer')
            @include('backend.include.scripts')

    </div>

    @yield('script')


</body>

</html>
