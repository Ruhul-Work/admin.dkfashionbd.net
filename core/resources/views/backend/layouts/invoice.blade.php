<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Invoice' }}</title>

        <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/bootstrap.min.css') }}">

     <!-- Main CSS -->
     <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/style.css') }}?id={{rand(121,122233)}}">

    <style>
        html,
        body {
            width: 100%;
            min-height: auto !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-size: 13px;
            background: #fff;
            overflow: visible !important;
            position: static !important;
            text-align: left;
        }

        .invoice-page {
            max-width: 850px;
            margin: 0 auto;
            padding-top: 8px;
        }

        .invoice-wrapper {
            padding: 24px;
            padding-top: 0;
        }

        .invoice-branding {
            text-align: center;
            margin-bottom: 14px;
            padding: 0 24px;
        }

        .invoice-branding img {
            display: block;
            margin: 0 auto 8px;
            max-height: 60px;
        }

        @media print {
            @page {
                margin: 12mm;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-page">
    <div class="invoice-branding">
        @if(optional($companySetting)->logo)
            <img src="{{ image($companySetting->logo) }}" alt="logo">
        @endif
        <strong>{{ optional($companySetting)->name ?? config('app.name') }}</strong><br>
        Phone: {{ optional($companySetting)->phone ?? '-' }}<br>
    </div>

    @yield('content')
</div>

<script>
    // 🔥 AUTO PRINT ON LOAD
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>
