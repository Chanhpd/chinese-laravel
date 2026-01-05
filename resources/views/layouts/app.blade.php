<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chinese Learning App')</title>
    <link rel="stylesheet" href="{{ asset('client-assets/css/style.css') }}">
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <script src="{{ asset('client-assets/js/auth.js') }}"></script>
    @stack('scripts')
</body>
</html>
