<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Global JavaScript Variables -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>

    <!-- Tailwind-like utility classes (for the sidebar design) -->
    <style>
        .min-h-screen { min-height: 100vh; }
        .font-sans { font-family: Inter, sans-serif; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .divide-y > :not([hidden]) ~ :not([hidden]) { border-top-width: 1px; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
        .hover\:bg-blue-50:hover { background-color: #eff6ff; }
        .hover\:text-blue-600:hover { color: #2563eb; }
    </style>

    <!-- Styles -->
    @vite(['resources/js/app.js'])
</head>
<body class="font-sans antialiased @if(auth()->guard('web')->check()) has-sidebar @endif">
    <div id="app" class="min-h-screen flex">
        @if(auth()->guard('web')->check())
            @if(auth()->user()->hasRole('admin'))
                @include('admin.partials.sidebar')
            @elseif(auth()->user()->hasRole('ucua_officer'))
                @include('ucua-officer.partials.sidebar')
            @else
                @include('partials.sidebar')
            @endif
        @elseif(auth()->guard('department')->check())
            {{-- Department users have their own sidebar in their dashboard views --}}
        @endif
        <div class="flex-1">
            @yield('content')
        </div>
    </div>

    <!-- Fixed Footer -->
    @include('partials.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- UCUA Utilities -->
    <script src="{{ asset('js/ucua-utilities.js') }}"></script>

    <!-- Global JavaScript Utilities -->
    <script>
    // Ensure jQuery is available globally
    window.$ = window.jQuery = $;

    // Global CSRF setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global error handler for AJAX requests
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:', {
            url: settings.url,
            status: xhr.status,
            error: thrownError,
            response: xhr.responseText
        });

        if (xhr.status === 419) {
            alert('Your session has expired. Please refresh the page and try again.');
            location.reload();
        }
    });

    // Universal modal functions for Bootstrap 4
    window.showModal = function(modalId) {
        $('#' + modalId).modal('show');
    };

    window.hideModal = function(modalId) {
        $('#' + modalId).modal('hide');
    };

    // Global button click handler with loading state
    window.handleButtonClick = function(button, callback) {
        if (button.disabled) return false;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        Promise.resolve(callback()).finally(() => {
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 1000);
        });
    };
    </script>

    @stack('scripts')
</body>
</html>