<!doctype html>
<html>

<head>
    <title>@yield('title') - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap4/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/cetak/main.css') }}">
</head>

<body>@yield('content')</body>

</html>
