<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }}</title>
    <!-- jQuery -->
    <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Google Font: Source Sans Pro -->
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('public/css/admin_css1.css')}}">
    <!-- Font Awesome -->
    <link href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    <!-- icheck bootstrap -->
    <link href="{{ asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}" rel="stylesheet" />
    <!-- Theme style -->
    <link href="{{ asset('public/dist/css/adminlte.min.css') }}" rel="stylesheet" />
</head>
<body class="hold-transition login-page">
    @yield("content")
    <!-- Bootstrap 4 -->
    <script src="{{ asset('public/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('public/dist/js/adminlte.min.js') }}"></script>
</body>
</html>