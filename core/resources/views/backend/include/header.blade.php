<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description">
    <meta name="author" >
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" >
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    {{-- Core CSS --}}
    <!-- Remix Icon Font CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/remixicon.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/bootstrap.min.css') }}">
    <!-- Apex Chart CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/apexcharts.css') }}">
    <!-- Data Table CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/dataTables.min.css') }}">
    <!-- Text Editor CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/editor-katex.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/editor.atom-one-dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/editor.quill.snow.css') }}">
    <!-- Date Picker CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/flatpickr.min.css') }}">
    <!-- Calendar CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/full-calendar.css') }}">
    <!-- Vector Map CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
    <!-- Popup CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/magnific-popup.css') }}">
    <!-- Slick Slider CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/slick.css') }}">
    <!-- Prism CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/prism.css') }}">
    <!-- File Upload CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/file-upload.css') }}">
    <!-- Audio Player CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/lib/audioplayer.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/css/style.css') }}?id={{rand(121,122233)}}">

    {{-- ajax data table css --}}
    {{-- <link rel="stylesheet" href="{{asset('theme/admin/assets/css/lib/ajaxdatatable/dataTables.bootstrap5.min.css')}}"> --}}

        {{-- select tow js --}}
    <link rel="stylesheet" href="{{asset('theme/admin/assets/js/lib/select2/css/select2.min.css')}}" ></link>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
 
</head>
