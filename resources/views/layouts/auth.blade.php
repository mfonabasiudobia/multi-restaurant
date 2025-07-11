<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ $generaleSetting?->favicon ?? asset('assets/favicon.png') }}" />

    <title>{{ $generaleSetting?->title ?? config('app.name', 'Laravel') }}</title>

    <!-- Google fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8f9fa;
        }
        .auth-card {
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .package-card {
            height: 100%;
            border: 1px solid #dee2e6;
        }
        .package-card:hover {
            border-color: var(--theme-color);
        }
        .package-card.selected {
            border: 2px solid var(--theme-color);
            background-color: var(--theme-hover-bg);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="app-content">
            <div class="content-wrapper">
                <div class="content-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <script>
        // Show toastr messages
        @if(session()->has('success'))
            toastr.success("{{ session()->get('success') }}")
        @endif

        @if(session()->has('error'))
            toastr.error("{{ session()->get('error') }}")
        @endif

        // Package selection
        document.querySelectorAll('.package-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.package-card').forEach(c => 
                    c.classList.remove('selected')
                );
                card.classList.add('selected');
                card.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Form submit loading state
        $('form').on('submit', function() {
            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true);
            submitButton.html(`
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Loading...</span>
                </div>
            `);
        });
    </script>

    @stack('scripts')
</body>
</html> 