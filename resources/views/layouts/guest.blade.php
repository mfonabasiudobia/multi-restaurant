<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $directory ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $generaleSetting?->title ?? config('app.name') }}</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        body {
            background: url('{{ asset("assets/images/auth-bg.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .auth-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }

        .app-page-title {
            background: var(--theme-color);
            color: white;
            padding: 1rem;
            margin: 0;
            border-radius: 15px 15px 0 0;
        }

        .page-title-subheading {
            color: rgba(255,255,255,0.8);
        }

        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .form-control {
            border-radius: 5px;
        }

        .form-control:focus {
            border-color: var(--theme-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-rgb), 0.25);
        }

        .btn-primary {
            background: var(--theme-color);
            border: none;
        }

        .btn-primary:hover {
            background: var(--theme-hover);
        }

        .ratio1x1 {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 5px;
        }

        .ratio4x1 {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 5px;
        }

        .information .title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container">
        <div class="auth-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html> 